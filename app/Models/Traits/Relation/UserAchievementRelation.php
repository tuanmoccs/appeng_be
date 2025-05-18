<?php
namespace App\Models\Traits\Relation;

use App\Models\User;

trait UserAchievementRelation{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
