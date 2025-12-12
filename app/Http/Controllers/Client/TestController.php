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

  public function index(Request $request)
  {
    try {
      $tests = Test::where('is_active', true)->get();

      $user = null;
      $token = $request->bearerToken();

      if ($token) {
        try {
          $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
          $user = null;
        }
      }

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

  public function show($id)
  {
    try {
      $test = Test::with([
        'passages' => function ($query) {
          $query->orderBy('order', 'asc')
            ->with(['questions' => function ($q) {
              $q->orderBy('order', 'asc');
            }]);
        },
        'standaloneQuestions' => function ($query) {
          $query->orderBy('order', 'asc');
        }
      ])->findOrFail($id);

      // Format data cho frontend
      $formattedTest = [
        'id' => $test->id,
        'title' => $test->title,
        'description' => $test->description,
        'type' => $test->type,
        'total_questions' => $test->total_questions,
        'time_limit' => $test->time_limit,
        'passing_score' => $test->passing_score,
        'is_active' => $test->is_active,
        'sections' => []
      ];

      // Thêm standalone questions
      if ($test->standaloneQuestions->isNotEmpty()) {
        foreach ($test->standaloneQuestions as $question) {
          $formattedTest['sections'][] = [
            'type' => 'standalone',
            'order' => $question->order,
            'question' => [
              'id' => $question->id,
              'question' => $question->question,
              'options' => is_string($question->options)
                ? json_decode($question->options, true)
                : $question->options,
              'difficulty' => $question->difficulty,
              'order' => $question->order
            ]
          ];
        }
      }

      // Thêm passages với questions
      if ($test->passages->isNotEmpty()) {
        foreach ($test->passages as $passage) {
          $passageQuestions = [];
          foreach ($passage->questions as $question) {
            $passageQuestions[] = [
              'id' => $question->id,
              'question' => $question->question,
              'options' => is_string($question->options)
                ? json_decode($question->options, true)
                : $question->options,
              'difficulty' => $question->difficulty,
              'order' => $question->order
            ];
          }

          $formattedTest['sections'][] = [
            'type' => 'passage',
            'order' => $passage->order,
            'passage' => [
              'id' => $passage->id,
              'title' => $passage->title,
              'content' => $passage->content
            ],
            'questions' => $passageQuestions
          ];
        }
      }

      // Sắp xếp sections theo order
      usort($formattedTest['sections'], function ($a, $b) {
        return $a['order'] - $b['order'];
      });

      return response()->json([
        'success' => true,
        'test' => $formattedTest
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Không thể tải test: ' . $e->getMessage()
      ], 500);
    }
  }

  public function submitTest(Request $request, $testId)
  {
    try {
      $request->validate([
        'answers' => 'nullable|array',
        'answers.*.question_id' => 'nullable|integer',
        'answers.*.selected_answer' => 'nullable',
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
