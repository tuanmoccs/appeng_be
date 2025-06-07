<?php

namespace App\Models\Traits\Relation;

use App\Models\User;
use App\Models\Test;

trait UserTestResultRelation
{
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function test()
  {
    return $this->belongsTo(Test::class);
  }
}
