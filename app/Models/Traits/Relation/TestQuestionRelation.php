<?php

namespace App\Models\Traits\Relation;

use App\Models\Test;
use App\Models\TestPassage;

trait TestQuestionRelation
{
  public function test()
  {
    return $this->belongsTo(Test::class);
  }
  public function passage()
  {
    return $this->belongsTo(TestPassage::class, 'passage_id');
  }
}
