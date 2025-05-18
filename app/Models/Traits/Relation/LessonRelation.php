<?php
namespace App\Models\Traits\Relation;

use App\Models\Quiz;
use App\Models\UserLessonProgress;
use App\Models\Word;

trait LessonRelation{
    /**
     * Get the words for the lesson.
     */
    public function words()
    {
        return $this->hasMany(Word::class);
    }

    /**
     * Get the quizzes for the lesson.
     */
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    /**
     * Get the progress records for the lesson.
     */
    public function progress()
    {
        return $this->hasMany(UserLessonProgress::class);
    }
}
