<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Database\Seeder;

class UserAchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $achievements = [
            [
                'user_id' => 1,
                'title' => 'Achievement 1',
                'description' => 'Hoàn thành bài học đầu tiên',
                'achievement_type' => 'lessons_completed',
            ],
            [
                'user_id' => 1,
                'title' => 'Achievement 2',
                'description' => 'Hoàn thành 5 bài học',
                'achievement_type' => 'lessons_completed',
            ],
            [
                'user_id' => 1,
                'title' => 'Achievement 3',
                'description' => 'Học 100 từ vựng',
                'achievement_type' => 'lessons_completed',
            ],
            [
                'user_id' => 1,
                'title' => 'Achievement 4',
                'description' => 'Đạt điểm tuyệt đối trong 3 bài kiểm tra',
                'achievement_type' => 'lessons_completed',
            ],
            [
                'user_id' => 1,
                'title' => 'Achievement 5',
                'description' => 'Học tập 7 ngày liên tiếp',
                'achievement_type' => 'lessons_completed',
            ],
            [
                'user_id' => 1,
                'title' => 'Achievement 6',
                'description' => 'Hoàn thành tất cả các bài học và kiểm tra',
                'achievement_type' => 'lessons_completed',
            ],
        ];

        foreach ($achievements as $achievement) {
            UserAchievement::create($achievement);
        }
    }
}
