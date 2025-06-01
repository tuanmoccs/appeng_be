<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\UserLessonProgress;
use App\Models\UserAchievement;
use App\Models\UserStat;
use Illuminate\Support\Facades\DB;

class LessonService
{
  public function getLessonsWithProgress($userId)
  {
    $lessons = Lesson::orderBy('order')->get();
    $userProgress = UserLessonProgress::where('user_id', $userId)
      ->pluck('progress_percentage', 'lesson_id')
      ->toArray();

    $completedLessons = UserLessonProgress::where('user_id', $userId)
      ->where('is_completed', true)
      ->pluck('lesson_id')
      ->toArray();

    return $lessons->map(function ($lesson, $index) use ($userProgress, $completedLessons) {
      $progress = $userProgress[$lesson->id] ?? 0;
      $isCompleted = in_array($lesson->id, $completedLessons);

      // Determine if lesson is locked
      $isLocked = false;
      if ($index > 0) {
        $previousLessonId = optional(
          $lesson->where('order', '<', $lesson->order)
            ->orderBy('order', 'desc')
            ->first()
        )->id;

        if ($previousLessonId && !in_array($previousLessonId, $completedLessons)) {
          $isLocked = true;
        }
      }

      return [
        'id' => $lesson->id,
        'title' => $lesson->title,
        'description' => $lesson->description,
        'level' => $lesson->level,
        'duration' => $lesson->duration,
        'order' => $lesson->order,
        'progress' => $progress,
        'is_completed' => $isCompleted,
        'is_locked' => $isLocked,
        'content_preview' => $this->getContentPreview($lesson->content),
      ];
    });
  }

  /**
   * Get lesson detail with user progress
   */
  public function getLessonWithProgress($userId, $lessonId)
  {
    $lesson = Lesson::find($lessonId);
    if (!$lesson) {
      return null;
    }

    $progress = UserLessonProgress::where('user_id', $userId)
      ->where('lesson_id', $lessonId)
      ->first();

    // Check if lesson is locked
    $isLocked = false;
    if ($lesson->order > 1) {
      $previousLesson = Lesson::where('order', '<', $lesson->order)
        ->orderBy('order', 'desc')
        ->first();

      if ($previousLesson) {
        $previousProgress = UserLessonProgress::where('user_id', $userId)
          ->where('lesson_id', $previousLesson->id)
          ->where('is_completed', true)
          ->exists();

        if (!$previousProgress) {
          $isLocked = true;
        }
      }
    }

    return [
      'id' => $lesson->id,
      'title' => $lesson->title,
      'description' => $lesson->description,
      'content' => json_decode($lesson->content, true),
      'level' => $lesson->level,
      'duration' => $lesson->duration,
      'order' => $lesson->order,
      'progress' => $progress ? $progress->progress_percentage : 0,
      'is_completed' => $progress ? $progress->is_completed : false,
      'is_locked' => $isLocked,
      'current_section' => $progress ? ($progress->current_section ?? 0) : 0,
      'current_item' => $progress ? ($progress->current_item ?? 0) : 0,
    ];
  }

  /**
   * Update lesson progress
   */
  public function updateProgress($userId, $lessonId, $progressPercentage, $sectionIndex = null, $itemIndex = null)
  {
    $lesson = Lesson::find($lessonId);
    if (!$lesson) {
      throw new \Exception('Không tìm thấy bài học');
    }

    // Check if lesson is locked
    if ($lesson->order > 1) {
      $previousLesson = Lesson::where('order', '<', $lesson->order)
        ->orderBy('order', 'desc')
        ->first();

      if ($previousLesson) {
        $previousProgress = UserLessonProgress::where('user_id', $userId)
          ->where('lesson_id', $previousLesson->id)
          ->where('is_completed', true)
          ->exists();

        if (!$previousProgress) {
          throw new \Exception('Bạn cần hoàn thành bài học trước đó');
        }
      }
    }

    // Đảm bảo progress không vượt quá 100%
    $progressPercentage = min(100, max(0, $progressPercentage));

    // Nếu progress >= 100%, tự động đánh dấu hoàn thành
    $isCompleted = $progressPercentage >= 100;

    $progress = UserLessonProgress::updateOrCreate(
      [
        'user_id' => $userId,
        'lesson_id' => $lessonId,
      ],
      [
        'progress_percentage' => $progressPercentage,
        'current_section' => $sectionIndex,
        'current_item' => $itemIndex,
        'is_completed' => $isCompleted,
        'completed_at' => $isCompleted ? now() : null,
      ]
    );

    // Update user stats
    $this->updateUserStats($userId);

    return $progress;
  }

  /**
   * Complete lesson
   */
  public function completeLesson($userId, $lessonId)
  {
    return DB::transaction(function () use ($userId, $lessonId) {
      $lesson = Lesson::find($lessonId);
      if (!$lesson) {
        throw new \Exception('Không tìm thấy bài học');
      }

      // Update progress to 100%
      $progress = $this->updateProgress($userId, $lessonId, 100);

      // Update user stats
      $this->updateUserStats($userId);

      // Check for achievements
      $achievements = $this->checkLessonAchievements($userId);

      return [
        'progress' => $progress,
        'achievements' => $achievements
      ];
    });
  }

  /**
   * Get user lesson statistics
   */
  public function getUserLessonStats($userId)
  {
    $totalLessons = Lesson::count();
    $completedLessons = UserLessonProgress::where('user_id', $userId)
      ->where('is_completed', true)
      ->count();

    $inProgressLessons = UserLessonProgress::where('user_id', $userId)
      ->where('is_completed', false)
      ->where('progress_percentage', '>', 0)
      ->count();

    $totalProgress = UserLessonProgress::where('user_id', $userId)
      ->avg('progress_percentage') ?? 0;

    return [
      'total_lessons' => $totalLessons,
      'completed_lessons' => $completedLessons,
      'in_progress_lessons' => $inProgressLessons,
      'overall_progress' => round($totalProgress, 1),
      'completion_rate' => $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 1) : 0,
    ];
  }

  /**
   * Get content preview for lesson list
   */
  private function getContentPreview($content)
  {
    $decoded = json_decode($content, true);
    if (!$decoded || !isset($decoded['sections'])) {
      return null;
    }

    $totalSections = count($decoded['sections']);
    $totalItems = 0;

    foreach ($decoded['sections'] as $section) {
      if (isset($section['items'])) {
        $totalItems += count($section['items']);
      }
    }

    return [
      'total_sections' => $totalSections,
      'total_items' => $totalItems,
    ];
  }

  /**
   * Update user stats
   */
  private function updateUserStats($userId)
  {
    $completedLessons = UserLessonProgress::where('user_id', $userId)
      ->where('is_completed', true)
      ->count();

    UserStat::updateOrCreate(
      ['user_id' => $userId],
      [
        'lessons_completed' => $completedLessons,
        'last_activity_at' => now(),
      ]
    );
  }

  /**
   * Check for lesson achievements
   */
  private function checkLessonAchievements($userId)
  {
    $achievements = [];
    $completedLessons = UserLessonProgress::where('user_id', $userId)
      ->where('is_completed', true)
      ->count();

    // First lesson achievement
    if ($completedLessons === 1) {
      $achievement = UserAchievement::firstOrCreate(
        [
          'user_id' => $userId,
          'achievement_type' => 'first_lesson',
        ],
        [
          'title' => 'Bài học đầu tiên',
          'description' => 'Bạn đã hoàn thành bài học đầu tiên!',
          'achieved_at' => now(),
        ]
      );
      if ($achievement->wasRecentlyCreated) {
        $achievements[] = $achievement;
      }
    }

    // 5 lessons achievement
    if ($completedLessons === 5) {
      $achievement = UserAchievement::firstOrCreate(
        [
          'user_id' => $userId,
          'achievement_type' => 'five_lessons',
        ],
        [
          'title' => 'Học giả nhỏ',
          'description' => 'Bạn đã hoàn thành 5 bài học!',
          'achieved_at' => now(),
        ]
      );
      if ($achievement->wasRecentlyCreated) {
        $achievements[] = $achievement;
      }
    }

    return $achievements;
  }
}
