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
    try {
      $test = Test::findOrFail($testId);
      $questions = TestQuestion::where('test_id', $testId)->get();

      $correctCount = 0;
      $totalQuestions = $test->total_questions;
      $detailedResults = [];
      // Tính số câu trả lời đúng
      foreach ($userAnswers as $answer) {
        $question = $questions->find($answer['question_id']);
        if ($question) {
          $isCorrect = $question->correct_answer === $answer['selected_answer'];
          if ($isCorrect) {
            $correctCount++;
          }

          // Thêm thông tin chi tiết cho từng câu hỏi
          $detailedResults[] = [
            'question_id' => $question->id,
            'question' => $question->question,
            'options' => is_string($question->options) ? json_decode($question->options, true) : $question->options,
            'user_answer' => $answer['selected_answer'],
            'correct_answer' => $question->correct_answer,
            'is_correct' => $isCorrect,
          ];
        }
      }

      // Tính điểm
      $score = $totalQuestions > 0 ? ($correctCount / $totalQuestions) * 100 : 0;
      $isPassed = $score >= $test->passing_score;

      // Lưu kết quả
      $result = UserTestResult::create([
        'user_id' => $userId,
        'test_id' => $testId,
        'score' => $score,
        'passed' => $isPassed,
        'total_questions' => $totalQuestions,
        'correct_answers' => $correctCount,
        'answers' => json_encode($userAnswers),
      ]);

      return [
        'test_id' => $testId,
        'score' => $score,
        'is_passed' => $isPassed,
        'correct_answers' => $correctCount,
        'total_questions' => $totalQuestions,
        'result_id' => $result->id,
        'detailed_results' => $detailedResults
      ];
    } catch (\Exception $e) {
      throw new \Exception('Lỗi khi đánh giá test: ' . $e->getMessage());
    }
  }

  // Lấy chi tiết kết quả test
  public function getTestResultDetails($resultId)
  {
    try {
      $result = UserTestResult::with('test')->findOrFail($resultId);

      // Decode answers nếu là string
      if (is_string($result->answers)) {
        $result->answers = json_decode($result->answers, true);
      }

      return $result;
    } catch (\Exception $e) {
      throw new \Exception('Không thể tải chi tiết kết quả: ' . $e->getMessage());
    }
  }
}
