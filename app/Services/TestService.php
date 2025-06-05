<?php

namespace App\Services;

use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\UserTestResult;
use Illuminate\Support\Facades\Auth;

class TestService
{
  // Đánh giá kết quả test
  public function evaluateTest($testId, $userId, $userAnswers)
  {
    $test = Test::findOrFail($testId);
    $questions = TestQuestion::where('test_id', $testId)->get();

    $correctCount = 0;
    $totalQuestions = $test->total_questions;

    // Tính số câu trả lời đúng
    foreach ($userAnswers as $answer) {
      $question = $questions->find($answer['question_id']);
      if ($question && $question->correct_answer === $answer['selected_answer']) {
        $correctCount++;
      }
    }

    // Tính điểm
    $score = ($correctCount / $totalQuestions) * 100;
    $isPassed = $score >= $test->passing_score;

    // Lưu kết quả
    $result = UserTestResult::create([
      'user_id' => $userId,
      'test_id' => $testId,
      'score' => $score,
      'is_passed' => $isPassed,
      'total_questions' => $totalQuestions,
      'correct_answers' => $correctCount,
      'answers' => json_encode($userAnswers),
    ]);

    return [
      'score' => $score,
      'is_passed' => $isPassed,
      'correct_answers' => $correctCount,
      'total_questions' => $totalQuestions,
      'result_id' => $result->id,
    ];
  }

  // Lấy chi tiết kết quả test
  public function getTestResultDetails($resultId)
  {
    $result = UserTestResult::with('test')->findOrFail($resultId);
    $result->answers = json_decode($result->answers, true);

    return $result;
  }
}
