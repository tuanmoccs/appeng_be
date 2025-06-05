<?php

namespace App\Http\Controllers\Client;

use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\UserTestResult;
use App\Services\TestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
  protected $testService;

  public function __construct(TestService $testService)
  {
    $this->testService = $testService;
  }

  // Lấy danh sách các test available
  public function index()
  {
    $tests = Test::where('is_active', true)->get();
    return response()->json($tests);
  }

  // Lấy chi tiết một test bao gồm các questions
  public function show($id)
  {
    $test = Test::with(['questions' => function ($query) {
      $query->orderBy('order', 'asc');
    }])->findOrFail($id);

    // Format lại options để FE dễ xử lý
    $test->questions->transform(function ($question) {
      $question->options = json_decode($question->options, true);
      return $question;
    });

    return response()->json([
      'test' => $test,
      'time_limit' => $test->time_limit,
      'total_questions' => $test->total_questions,
    ]);
  }

  // Submit kết quả test
  public function submitTest(Request $request, $testId)
  {
    $request->validate([
      'answers' => 'required|array',
      'answers.*.question_id' => 'required|integer',
      'answers.*.selected_answer' => 'required',
    ]);

    $userId = Auth::id();
    $result = $this->testService->evaluateTest($testId, $userId, $request->answers);

    return response()->json($result);
  }

  // Lấy kết quả test của user
  public function getUserResults($testId)
  {
    $userId = Auth::id();
    $results = UserTestResult::where('user_id', $userId)
      ->where('test_id', $testId)
      ->orderBy('created_at', 'desc')
      ->get();

    return response()->json($results);
  }
}
