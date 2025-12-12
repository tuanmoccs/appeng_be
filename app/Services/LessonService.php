<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\UserLessonProgress;
use App\Models\UserLessonQuizResult;
use App\Models\UserAchievement;
use App\Models\UserStat;
use Illuminate\Support\Facades\DB;

class LessonService
{
  /**
   * Get all lessons with user progress
   */
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

    // Get quiz results
    $passedQuizzes = UserLessonQuizResult::where('user_id', $userId)
      ->where('is_passed', true)
      ->pluck('lesson_id')
      ->toArray();

    return $lessons->map(function ($lesson, $index) use ($userProgress, $completedLessons, $passedQuizzes) {
      $progress = $userProgress[$lesson->id] ?? 0;
      $isCompleted = in_array($lesson->id, $completedLessons);
      $hasPassedQuiz = in_array($lesson->id, $passedQuizzes);

      // NEW LOGIC: Lesson is locked if previous lesson's quiz is not passed
      $isLocked = false;
      if ($index > 0) {
        $previousLesson = Lesson::where('order', '<', $lesson->order)
          ->orderBy('order', 'desc')
          ->first();

        if ($previousLesson) {
          // Check if previous lesson has quiz
          if ($previousLesson->hasQuiz()) {
            // Must pass quiz to unlock next lesson
            $isLocked = !in_array($previousLesson->id, $passedQuizzes);
          } else {
            // If no quiz, just need to complete the lesson
            $isLocked = !in_array($previousLesson->id, $completedLessons);
          }
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
        'has_quiz' => $lesson->hasQuiz(),
        'quiz_passed' => $hasPassedQuiz,
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
        if ($previousLesson->hasQuiz()) {
          $previousQuizPassed = UserLessonQuizResult::where('user_id', $userId)
            ->where('lesson_id', $previousLesson->id)
            ->where('is_passed', true)
            ->exists();
          $isLocked = !$previousQuizPassed;
        } else {
          $previousProgress = UserLessonProgress::where('user_id', $userId)
            ->where('lesson_id', $previousLesson->id)
            ->where('is_completed', true)
            ->exists();
          $isLocked = !$previousProgress;
        }
      }
    }

    // Get quiz result if exists
    $quizResult = null;
    if ($lesson->hasQuiz()) {
      $quizResult = UserLessonQuizResult::where('user_id', $userId)
        ->where('lesson_id', $lessonId)
        ->orderBy('created_at', 'desc')
        ->first();
    }

    return [
      'id' => $lesson->id,
      'title' => $lesson->title,
      'description' => $lesson->description,
      'content' => $lesson->content,
      'quiz' => $lesson->quiz,
      'level' => $lesson->level,
      'duration' => $lesson->duration,
      'order' => $lesson->order,
      'progress' => $progress ? $progress->progress_percentage : 0,
      'is_completed' => $progress ? $progress->is_completed : false,
      'is_locked' => $isLocked,
      'has_quiz' => $lesson->hasQuiz(),
      'quiz_result' => $quizResult ? [
        'score' => $quizResult->score,
        'is_passed' => $quizResult->is_passed,
        'attempt_number' => $quizResult->attempt_number,
        'created_at' => $quizResult->created_at,
      ] : null,
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
        if ($previousLesson->hasQuiz()) {
          $previousQuizPassed = UserLessonQuizResult::where('user_id', $userId)
            ->where('lesson_id', $previousLesson->id)
            ->where('is_passed', true)
            ->exists();
          if (!$previousQuizPassed) {
            throw new \Exception('Bạn cần hoàn thành quiz của bài học trước');
          }
        } else {
          $previousProgress = UserLessonProgress::where('user_id', $userId)
            ->where('lesson_id', $previousLesson->id)
            ->where('is_completed', true)
            ->exists();
          if (!$previousProgress) {
            throw new \Exception('Bạn cần hoàn thành bài học trước đó');
          }
        }
      }
    }

    $progressPercentage = min(100, max(0, $progressPercentage));
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

    $this->updateUserStats($userId);

    return $progress;
  }

  /**
   * Submit quiz answers
   */
  public function submitQuiz($userId, $lessonId, $answers, $timeTaken)
  {
    return DB::transaction(function () use ($userId, $lessonId, $answers, $timeTaken) {
      $lesson = Lesson::find($lessonId);
      if (!$lesson || !$lesson->hasQuiz()) {
        throw new \Exception('Bài học không có quiz');
      }

      $quiz = $lesson->quiz;
      $questions = $quiz['questions'];
      $totalQuestions = count($questions);
      $correctAnswers = 0;
      $detailedAnswers = [];

      // Grade answers
      foreach ($questions as $question) {
        $userAnswer = $answers[$question['id']] ?? null;
        $isCorrect = false;

        if ($userAnswer) {
          $correctAnswer = strtolower(trim($question['correct_answer']));
          $userAnswerLower = strtolower(trim($userAnswer));
          $isCorrect = $correctAnswer === $userAnswerLower;
        }

        if ($isCorrect) {
          $correctAnswers++;
        }

        $detailedAnswers[] = [
          'question_id' => $question['id'],
          'user_answer' => $userAnswer,
          'correct_answer' => $question['correct_answer'],
          'is_correct' => $isCorrect,
          'explanation' => $question['explanation'] ?? null,
        ];
      }

      $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 1) : 0;
      $passingScore = $quiz['passing_score'] ?? 80;
      $isPassed = $score >= $passingScore;

      // Get attempt number
      $attemptNumber = UserLessonQuizResult::where('user_id', $userId)
        ->where('lesson_id', $lessonId)
        ->max('attempt_number') ?? 0;
      $attemptNumber++;

      // Save result
      $result = UserLessonQuizResult::create([
        'user_id' => $userId,
        'lesson_id' => $lessonId,
        'score' => $score,
        'total_questions' => $totalQuestions,
        'correct_answers' => $correctAnswers,
        'time_taken' => $timeTaken,
        'answers' => $detailedAnswers,
        'is_passed' => $isPassed,
        'attempt_number' => $attemptNumber,
      ]);

      // If passed, mark lesson as completed
      if ($isPassed) {
        $this->updateProgress($userId, $lessonId, 100);
      }

      $this->updateUserStats($userId);

      return [
        'score' => $score,
        'total_questions' => $totalQuestions,
        'correct_answers' => $correctAnswers,
        'is_passed' => $isPassed,
        'passing_score' => $passingScore,
        'attempt_number' => $attemptNumber,
        'detailed_answers' => $detailedAnswers,
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

    $passedQuizzes = UserLessonQuizResult::where('user_id', $userId)
      ->where('is_passed', true)
      ->distinct('lesson_id')
      ->count();

    return [
      'total_lessons' => $totalLessons,
      'completed_lessons' => $completedLessons,
      'in_progress_lessons' => $inProgressLessons,
      'passed_quizzes' => $passedQuizzes,
      'overall_progress' => round($totalProgress, 1),
      'completion_rate' => $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 1) : 0,
    ];
  }

  /**
   * Get content preview for lesson list
   */
  private function getContentPreview($content)
  {
    if (!$content || !isset($content['sections'])) {
      return null;
    }

    $totalSections = count($content['sections']);
    $totalItems = 0;

    foreach ($content['sections'] as $section) {
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

    $passedQuizzes = UserLessonQuizResult::where('user_id', $userId)
      ->where('is_passed', true)
      ->distinct('lesson_id')
      ->count();

    UserStat::updateOrCreate(
      ['user_id' => $userId],
      [
        'lessons_completed' => $completedLessons,
        'quizzes_passed' => $passedQuizzes,
        'last_activity_at' => now(),
      ]
    );
  }
}
