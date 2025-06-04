<?php

namespace Database\Seeders;

use App\Models\Test;
use App\Models\TestQuestion;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
  public function run()
  {
    // Tạo Placement Test
    $placementTest = Test::create([
      'title' => 'English Placement Test',
      'description' => 'Bài test đánh giá trình độ tiếng Anh ban đầu',
      'type' => 'placement',
      'total_questions' => 50,
      'time_limit' => 60,
      'passing_score' => 60,
      'is_active' => true,
    ]);

    // Tạo Achievement Test
    $achievementTest = Test::create([
      'title' => 'Basic English Achievement Test',
      'description' => 'Bài test kiểm tra sau khi hoàn thành khóa học cơ bản',
      'type' => 'achievement',
      'total_questions' => 30,
      'time_limit' => 45,
      'passing_score' => 70,
      'is_active' => true,
    ]);

    // Tạo Practice Test
    $practiceTest = Test::create([
      'title' => 'English Practice Test',
      'description' => 'Bài test luyện tập hàng ngày',
      'type' => 'practice',
      'total_questions' => 20,
      'time_limit' => null,
      'passing_score' => 60,
      'is_active' => true,
    ]);

    // Thêm câu hỏi mẫu cho Placement Test
    $this->createSampleQuestions($placementTest);
  }

  private function createSampleQuestions($test)
  {
    $questions = [
      [
        'question' => 'What is the correct form of the verb "to be" for "I"?',
        'options' => ['am', 'is', 'are', 'be'],
        'correct_answer' => 'am',
        'difficulty' => 'easy',
        'order' => 1,
      ],
      [
        'question' => 'Choose the correct sentence:',
        'options' => [
          'She go to school every day.',
          'She goes to school every day.',
          'She going to school every day.',
          'She gone to school every day.'
        ],
        'correct_answer' => 'She goes to school every day.',
        'difficulty' => 'easy',
        'order' => 2,
      ],
      [
        'question' => 'What is the past tense of "eat"?',
        'options' => ['eated', 'ate', 'eaten', 'eating'],
        'correct_answer' => 'ate',
        'difficulty' => 'medium',
        'order' => 3,
      ],
      [
        'question' => 'Which sentence is in the present perfect tense?',
        'options' => [
          'I have finished my homework.',
          'I finished my homework.',
          'I am finishing my homework.',
          'I will finish my homework.'
        ],
        'correct_answer' => 'I have finished my homework.',
        'difficulty' => 'medium',
        'order' => 4,
      ],
      [
        'question' => 'Choose the correct conditional sentence:',
        'options' => [
          'If I was rich, I would buy a car.',
          'If I were rich, I would buy a car.',
          'If I am rich, I would buy a car.',
          'If I will be rich, I would buy a car.'
        ],
        'correct_answer' => 'If I were rich, I would buy a car.',
        'difficulty' => 'hard',
        'order' => 5,
      ],
    ];

    foreach ($questions as $questionData) {
      $test->questions()->create($questionData);
    }
  }
}
