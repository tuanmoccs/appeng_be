<?php

namespace Database\Seeders;

use App\Models\Lesson;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lessons = [
            [
                'title' => 'Từ vựng cơ bản',
                'description' => 'Học các từ vựng cơ bản trong tiếng Anh hàng ngày',
                'level' => 'beginner',
                'order' => 1,
            ],
            [
                'title' => 'Từ vựng về gia đình',
                'description' => 'Học các từ vựng liên quan đến gia đình và người thân',
                'level' => 'beginner',
                'order' => 2,
            ],
            [
                'title' => 'Từ vựng về thức ăn',
                'description' => 'Học các từ vựng liên quan đến thức ăn và đồ uống',
                'level' => 'beginner',
                'order' => 3,
            ],
            [
                'title' => 'Từ vựng về công việc',
                'description' => 'Học các từ vựng liên quan đến công việc và nghề nghiệp',
                'level' => 'intermediate',
                'order' => 4,
            ],
            [
                'title' => 'Từ vựng về du lịch',
                'description' => 'Học các từ vựng liên quan đến du lịch và khách sạn',
                'level' => 'intermediate',
                'order' => 5,
            ],
        ];

        foreach ($lessons as $lesson) {
            Lesson::create($lesson);
        }
    }
}
