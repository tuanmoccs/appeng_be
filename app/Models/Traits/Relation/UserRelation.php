<?php
namespace App\Models\Traits\Relation;

use App\Models\UserAchievement;
use App\Models\UserLessonProgress;
use App\Models\UserQuizResult;
use App\Models\UserStat;

trait UserRelation
{
    /**
     * Get the user's lesson progress.
     */
    public function lessonProgress()
    {
        return $this->hasMany(UserLessonProgress::class);
    }

    /**
     * Get the user's quiz results.
     */
    public function quizResults()
    {
        return $this->hasMany(UserQuizResult::class);
    }

    /**
     * Get the user's achievements.
     */
    public function achievements()
    {
        return $this->hasMany(UserAchievement::class);
    }

    /**
     * Get the user's stats.
     */
    public function stats()
    {
        return $this->hasOne(UserStat::class);
    }
}
