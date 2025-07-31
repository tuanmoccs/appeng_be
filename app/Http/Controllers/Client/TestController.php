<?php

namespace App\Http\Controllers\Client;

use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\UserTestResult;
use App\Services\TestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;

class TestController extends Controller
{
  protected $testService;

  public function __construct(TestService $testService)
  {
    $this->testService = $testService;
  }

  // Lấy danh sách các test available
  public function index(Request $request)
  {
    try {
      $tests = Test::where('is_active', true)->get();

      // Thử authenticate từ token
      $user = null;
      $token = $request->bearerToken();

      if ($token) {
        try {
          // Nếu dùng JWT
          $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
          // Token invalid, continue as guest
          $user = null;
        }
      }

      // Nếu có user, lấy kết quả gần nhất
      if ($user) {
        $userId = $user->id;

        $tests->transform(function ($test) use ($userId) {
          $latestResult = UserTestResult::where('user_id', $userId)
            ->where('test_id', $test->id)
            ->orderBy('created_at', 'desc')
            ->first();

          $test->user_latest_result = $latestResult ? [
            'score' => $latestResult->score,
            'completed_at' => $latestResult->created_at,
            'passed' => (bool) $latestResult->passed
          ] : null;

          return $test;
        });
      }

      return response()->json($tests);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Không thể tải danh sách test'
      ], 500);
    }
  }

  // Lấy chi tiết một test bao gồm các questions
  public function show($id)
  {
    try {
      $test = Test::with(['questions' => function ($query) {
        $query->orderBy('order', 'asc');
      }])->findOrFail($id);

      // Format lại options để FE dễ xử lý
      $test->questions->transform(function ($question) {
        // Kiểm tra xem options đã là array chưa
        if (is_string($question->options)) {
          $question->options = json_decode($question->options, true);
        }

        // Nếu decode thất bại hoặc không phải array, set default
        if (!is_array($question->options)) {
          $question->options = [];
        }

        return $question;
      });

      return response()->json([
        'success' => true,
        'test' => $test,
        'time_limit' => $test->time_limit,
        'total_questions' => $test->total_questions,
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Không thể tải test: ' . $e->getMessage()
      ], 500);
    }
  }

  // Submit kết quả test
  public function submitTest(Request $request, $testId)
  {
    try {
      $request->validate([
        'answers' => 'required|array',
        'answers.*.question_id' => 'required|integer',
        'answers.*.selected_answer' => 'required',
      ]);

      $userId = Auth::id();
      $result = $this->testService->evaluateTest($testId, $userId, $request->answers);

      return response()->json([
        'success' => true,
        'result' => $result
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Không thể nộp bài test: ' . $e->getMessage()
      ], 500);
    }
  }

  // Lấy kết quả test của user
  public function getUserResults($testId)
  {
    try {
      $userId = Auth::id();
      $results = UserTestResult::where('user_id', $userId)
        ->where('test_id', $testId)
        ->orderBy('created_at', 'desc')
        ->get();

      return response()->json([
        'success' => true,
        'results' => $results
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Không thể tải kết quả test'
      ], 500);
    }
  }
}
