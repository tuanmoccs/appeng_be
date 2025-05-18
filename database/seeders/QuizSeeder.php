<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Lấy ID của các bài học
        $lessonIds = Lesson::pluck('id')->toArray();

        $quizzes = [
            [
                'lesson_id' => $lessonIds[0],
                'title' => 'Kiểm tra từ vựng cơ bản',
                'description' => 'Bài kiểm tra kiến thức về các từ vựng cơ bản'
            ],
            [
                'lesson_id' => $lessonIds[1],
                'title' => 'Kiểm tra từ vựng về gia đình',
                'description' => 'Bài kiểm tra kiến thức về các từ vựng liên quan đến gia đình',
            ],
            [
                'lesson_id' => $lessonIds[2],
                'title' => 'Kiểm tra từ vựng về thức ăn',
                'description' => 'Bài kiểm tra kiến thức về các từ vựng liên quan đến thức ăn',
            ],
            [
                'lesson_id' => $lessonIds[3],
                'title' => 'Kiểm tra từ vựng về công việc',
                'description' => 'Bài kiểm tra kiến thức về các từ vựng liên quan đến công việc',
            ],
            [
                'lesson_id' => $lessonIds[4],
                'title' => 'Kiểm tra từ vựng về du lịch',
                'description' => 'Bài kiểm tra kiến thức về các từ vựng liên quan đến du lịch',
            ],
        ];

        foreach ($quizzes as $quiz) {
            Quiz::create($quiz);
        }
    }
}
