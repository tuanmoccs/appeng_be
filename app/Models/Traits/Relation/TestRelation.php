<?php

namespace App\Models\Traits\Relation;

use App\Models\TestQuestion;
use App\Models\UserTestResult;

trait TestRelation
{
  public function questions()
  {
    return $this->hasMany(TestQuestion::class);
  }

  public function results()
  {
    return $this->hasMany(UserTestResult::class);
  }
}
