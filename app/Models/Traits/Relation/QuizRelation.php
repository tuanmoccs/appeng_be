<?php
namespace App\Models\Traits\Relation;

use App\Models\Lesson;
use App\Models\QuizQuestion;
use App\Models\UserQuizResult;

trait QuizRelation{
    /**
     * Get the lesson that owns the quiz.
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Get the questions for the quiz.
     */
    public function questions()
    {
        return $this->hasMany(QuizQuestion::class);
    }

    /**
     * Get the results for the quiz.
     */
    public function results()
    {
        return $this->hasMany(UserQuizResult::class);
    }
}
