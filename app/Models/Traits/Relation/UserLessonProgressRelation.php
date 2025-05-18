<?php
namespace App\Models\Traits\Relation;

use App\Models\Lesson;
use App\Models\User;

trait UserLessonProgressRelation{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lesson that owns the progress.
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
