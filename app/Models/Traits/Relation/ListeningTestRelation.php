<?php
namespace App\Models\Traits\Relation;
use App\Models\ListeningSection;
use App\Models\UserListeningResult;

trait ListeningTestRelation{
    public function sections()
  {
    return $this->hasMany(ListeningSection::class)->orderBy('order');
  }

  public function results()
  {
    return $this->hasMany(UserListeningResult::class);
  }
}