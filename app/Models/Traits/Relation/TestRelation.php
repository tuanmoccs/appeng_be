<?php

namespace App\Models\Traits\Relation;

use App\Models\TestQuestion;
use App\Models\UserTestResult;
use Illuminate\Support\Facades\Auth;

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
  public function userLatestResult()
  {
    return $this->hasOne(UserTestResult::class)
      ->where('user_id', Auth::id())
      ->latest('created_at');
  }
}
