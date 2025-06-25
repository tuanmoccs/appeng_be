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
      'total_questions' => 20,
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
    $this->createAchievementTestQuestions($achievementTest);
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
  private function createAchievementTestQuestions($test)
  {
    $questions = [
        // Easy level questions (1-7)
        [
            'question' => 'Choose the correct article: ___ apple is red.',
            'options' => ['A', 'An', 'The', 'No article'],
            'correct_answer' => 'The',
            'difficulty' => 'easy',
            'order' => 1,
        ],
        [
            'question' => 'What is the plural form of "child"?',
            'options' => ['childs', 'children', 'childes', 'child'],
            'correct_answer' => 'children',
            'difficulty' => 'easy',
            'order' => 2,
        ],
        [
            'question' => 'Choose the correct preposition: She is good ___ mathematics.',
            'options' => ['in', 'at', 'on', 'for'],
            'correct_answer' => 'at',
            'difficulty' => 'easy',
            'order' => 3,
        ],
        [
            'question' => 'What time is it? It\'s ___ o\'clock.',
            'options' => ['three', 'third', 'tree', 'thirty'],
            'correct_answer' => 'three',
            'difficulty' => 'easy',
            'order' => 4,
        ],
        [
            'question' => 'Choose the correct possessive: This is ___ book.',
            'options' => ['Johns', 'John\'s', 'Johns\'', 'John'],
            'correct_answer' => 'John\'s',
            'difficulty' => 'easy',
            'order' => 5,
        ],
        [
            'question' => 'What is the opposite of "hot"?',
            'options' => ['warm', 'cool', 'cold', 'freeze'],
            'correct_answer' => 'cold',
            'difficulty' => 'easy',
            'order' => 6,
        ],
        [
            'question' => 'Choose the correct form: There ___ many students in the class.',
            'options' => ['is', 'are', 'was', 'been'],
            'correct_answer' => 'are',
            'difficulty' => 'easy',
            'order' => 7,
        ],

        // Medium level questions (8-14)
        [
            'question' => 'Which sentence is correct?',
            'options' => [
                'I have been studying English since 5 years.',
                'I have been studying English for 5 years.',
                'I have been studying English during 5 years.',
                'I have been studying English in 5 years.'
            ],
            'correct_answer' => 'I have been studying English for 5 years.',
            'difficulty' => 'medium',
            'order' => 8,
        ],
        [
            'question' => 'Choose the correct comparative: This book is ___ than that one.',
            'options' => ['more interesting', 'most interesting', 'interestinger', 'interesting'],
            'correct_answer' => 'more interesting',
            'difficulty' => 'medium',
            'order' => 9,
        ],
        [
            'question' => 'What is the correct passive form of "They built the house"?',
            'options' => [
                'The house built by them.',
                'The house was built by them.',
                'The house is built by them.',
                'The house has built by them.'
            ],
            'correct_answer' => 'The house was built by them.',
            'difficulty' => 'medium',
            'order' => 10,
        ],
        [
            'question' => 'Choose the correct modal verb: You ___ wear a helmet when riding a motorcycle.',
            'options' => ['can', 'may', 'must', 'would'],
            'correct_answer' => 'must',
            'difficulty' => 'medium',
            'order' => 11,
        ],
        [
            'question' => 'Which sentence uses the gerund correctly?',
            'options' => [
                'I enjoy to read books.',
                'I enjoy reading books.',
                'I enjoy read books.',
                'I enjoy for reading books.'
            ],
            'correct_answer' => 'I enjoy reading books.',
            'difficulty' => 'medium',
            'order' => 12,
        ],
        [
            'question' => 'Choose the correct reported speech: She said, "I am tired."',
            'options' => [
                'She said that she is tired.',
                'She said that she was tired.',
                'She said that she has been tired.',
                'She said that she will be tired.'
            ],
            'correct_answer' => 'She said that she was tired.',
            'difficulty' => 'medium',
            'order' => 13,
        ],
        [
            'question' => 'What is the correct question tag: You don\'t like coffee, ___?',
            'options' => ['don\'t you', 'do you', 'aren\'t you', 'are you'],
            'correct_answer' => 'do you',
            'difficulty' => 'medium',
            'order' => 14,
        ],

        // Hard level questions (15-20)
        [
            'question' => 'Choose the sentence with correct subject-verb agreement:',
            'options' => [
                'Neither of the students have finished their homework.',
                'Neither of the students has finished their homework.',
                'Neither of the students are finishing their homework.',
                'Neither of the students were finished their homework.'
            ],
            'correct_answer' => 'Neither of the students has finished their homework.',
            'difficulty' => 'hard',
            'order' => 15,
        ],
        [
            'question' => 'Which sentence correctly uses the subjunctive mood?',
            'options' => [
                'I wish I was taller.',
                'I wish I were taller.',
                'I wish I am taller.',
                'I wish I will be taller.'
            ],
            'correct_answer' => 'I wish I were taller.',
            'difficulty' => 'hard',
            'order' => 16,
        ],
        [
            'question' => 'Choose the correct form of the third conditional:',
            'options' => [
                'If I had studied harder, I would pass the exam.',
                'If I had studied harder, I would have passed the exam.',
                'If I studied harder, I would have passed the exam.',
                'If I have studied harder, I would pass the exam.'
            ],
            'correct_answer' => 'If I had studied harder, I would have passed the exam.',
            'difficulty' => 'hard',
            'order' => 17,
        ],
        [
            'question' => 'Which sentence correctly uses "used to" and "be used to"?',
            'options' => [
                'I used to wake up early, but now I\'m used to sleeping late.',
                'I used to wake up early, but now I used to sleeping late.',
                'I use to wake up early, but now I\'m used to sleeping late.',
                'I used to wake up early, but now I\'m use to sleeping late.'
            ],
            'correct_answer' => 'I used to wake up early, but now I\'m used to sleeping late.',
            'difficulty' => 'hard',
            'order' => 18,
        ],
        [
            'question' => 'Choose the sentence with the correct use of inversion:',
            'options' => [
                'Never I have seen such a beautiful sunset.',
                'Never have I seen such a beautiful sunset.',
                'Never I had seen such a beautiful sunset.',
                'Never did I have seen such a beautiful sunset.'
            ],
            'correct_answer' => 'Never have I seen such a beautiful sunset.',
            'difficulty' => 'hard',
            'order' => 19,
        ],
        [
            'question' => 'Which sentence correctly uses the perfect infinitive?',
            'options' => [
                'She seems to have forgotten her keys.',
                'She seems to forget her keys.',
                'She seems to had forgotten her keys.',
                'She seems to has forgotten her keys.'
            ],
            'correct_answer' => 'She seems to have forgotten her keys.',
            'difficulty' => 'hard',
            'order' => 20,
        ],
    ];

    foreach ($questions as $questionData) {
        $test->questions()->create($questionData);
    }
  }
}
