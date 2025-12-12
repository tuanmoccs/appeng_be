<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Services\LessonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
  protected $lessonService;

  public function __construct(LessonService $lessonService)
  {
    $this->lessonService = $lessonService;
  }

  /**
   * Get all lessons with user progress
   */
  public function index()
  {
    try {
      $userId = Auth::id();
      $lessons = $this->lessonService->getLessonsWithProgress($userId);

      return response()->json([
        'success' => true,
        'lessons' => $lessons
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Không thể tải danh sách bài học',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  /**
   * Get lesson detail with user progress
   */
  public function show($id)
  {
    try {
      $userId = Auth::id();
      $lesson = $this->lessonService->getLessonWithProgress($userId, $id);

      if (!$lesson) {
        return response()->json([
          'success' => false,
          'message' => 'Không tìm thấy bài học'
        ], 404);
      }

      return response()->json([
        'success' => true,
        'lesson' => $lesson
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Không thể tải chi tiết bài học',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  /**
   * Update lesson progress
   */
  public function updateProgress(Request $request, $id)
  {
    try {
      $validator = Validator::make($request->all(), [
        'progress_percentage' => 'required|integer|min:0|max:100',
        'section_index' => 'sometimes|integer|min:0',
        'item_index' => 'sometimes|integer|min:0',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()
        ], 422);
      }

      $userId = Auth::id();
      $progress = $this->lessonService->updateProgress(
        $userId,
        $id,
        $request->progress_percentage,
        $request->section_index,
        $request->item_index
      );

      return response()->json([
        'success' => true,
        'message' => 'Cập nhật tiến độ thành công',
        'progress' => $progress
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage()
      ], 500);
    }
  }

  /**
   * Get quiz for a lesson
   */
  public function getQuiz($id)
  {
    try {
      $userId = Auth::id();
      $lesson = Lesson::find($id);

      if (!$lesson) {
        return response()->json([
          'success' => false,
          'message' => 'Không tìm thấy bài học'
        ], 404);
      }

      if (!$lesson->hasQuiz()) {
        return response()->json([
          'success' => false,
          'message' => 'Bài học này không có quiz'
        ], 404);
      }

      // Check if lesson is locked
      $lessonData = $this->lessonService->getLessonWithProgress($userId, $id);
      if ($lessonData['is_locked']) {
        return response()->json([
          'success' => false,
          'message' => 'Bài học bị khóa. Hoàn thành bài học trước đó.'
        ], 403);
      }

      // Remove correct answers from quiz (don't send to client)
      $quiz = $lesson->quiz;
      $quizForClient = [
        'title' => $quiz['title'] ?? 'Quiz',
        'description' => $quiz['description'] ?? '',
        'time_limit' => $quiz['time_limit'] ?? 15,
        'passing_score' => $quiz['passing_score'] ?? 80,
        'total_questions' => count($quiz['questions']),
        'questions' => array_map(function ($q) {
          return [
            'id' => $q['id'],
            'type' => $q['type'],
            'question' => $q['question'],
            'image_url' => $q['image_url'] ?? null,
            'options' => $q['options'] ?? null,
            // Don't send correct_answer or explanation
          ];
        }, $quiz['questions']),
      ];

      return response()->json([
        'success' => true,
        'quiz' => $quizForClient
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Không thể tải quiz',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  /**
   * Submit quiz answers
   */
  public function submitQuiz(Request $request, $id)
  {
    try {
      $validator = Validator::make($request->all(), [
        'answers' => 'required|array',
        'time_taken' => 'required|integer|min:0',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()
        ], 422);
      }

      $userId = Auth::id();
      $result = $this->lessonService->submitQuiz(
        $userId,
        $id,
        $request->answers,
        $request->time_taken
      );

      return response()->json([
        'success' => true,
        'message' => $result['is_passed'] ? 'Chúc mừng! Bạn đã đạt quiz!' : 'Bạn chưa đạt. Hãy thử lại!',
        'result' => $result
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage()
      ], 500);
    }
  }

  /**
   * Get user's lesson statistics
   */
  public function getStats()
  {
    try {
      $userId = Auth::id();
      $stats = $this->lessonService->getUserLessonStats($userId);

      return response()->json([
        'success' => true,
        'stats' => $stats
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Không thể tải thống kê bài học',
        'error' => $e->getMessage()
      ], 500);
    }
  }
}
