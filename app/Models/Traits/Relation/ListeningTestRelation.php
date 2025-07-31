<?php

namespace App\Models\Traits\Relation;

use App\Models\ListeningSection;
use App\Models\UserListeningResult;
use App\Models\UserTestResult;
use Illuminate\Support\Facades\Auth;

trait ListeningTestRelation
{
  public function sections()
  {
    return $this->hasMany(ListeningSection::class)->orderBy('order');
  }

  public function results()
  {
    return $this->hasMany(UserListeningResult::class);
  }
  public function userLatestResult()
  {
    return $this->hasOne(UserListeningResult::class)
      ->where('user_id', Auth::id())
      ->latest('created_at');
  }
}
