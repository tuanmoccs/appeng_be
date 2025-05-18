<?php
namespace App\Models\Traits\Relation;

use App\Models\Lesson;

trait WordRelation
{
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
