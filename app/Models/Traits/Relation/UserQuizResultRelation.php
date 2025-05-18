<?php
namespace App\Models\Traits\Relation;

use App\Models\Quiz;
use App\Models\User;

trait UserQuizResultRelation{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the quiz that owns the result.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
