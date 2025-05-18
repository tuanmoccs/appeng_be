<?php
namespace App\Models\Traits\Relation;

use App\Models\User;

trait UserStatRelation{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
