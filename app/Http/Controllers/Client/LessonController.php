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
        'message' => 'Không thể tải danh sách bài học'
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
        'message' => 'Không thể tải chi tiết bài học'
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
   * Complete lesson
   */
  public function complete($id)
  {
    try {
      $userId = Auth::id();
      $result = $this->lessonService->completeLesson($userId, $id);

      return response()->json([
        'success' => true,
        'message' => 'Hoàn thành bài học thành công!',
        'progress' => $result['progress'],
        'achievements' => $result['achievements'] ?? []
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
        'message' => 'Không thể tải thống kê bài học'
      ], 500);
    }
  }
}
