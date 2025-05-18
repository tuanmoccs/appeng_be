<?php
namespace App\Models\Traits\Relation;

use App\Models\Quiz;

trait QuizQuestionRelation{
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
