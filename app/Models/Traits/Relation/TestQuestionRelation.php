<?php

namespace App\Models\Traits\Relation;

use App\Models\Test;

trait TestQuestionRelation
{
  public function test()
  {
    return $this->belongsTo(Test::class);
  }
}
