<?php

namespace App\Models;

use App\Models\Traits\Relation\UserAchievementRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAchievement extends Model
{
    use HasFactory, UserAchievementRelation;

    protected $fillable = [
        'user_id',
        'achievement_type',
        'title',
        'description',
        'achieved_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'achieved_at' => 'datetime',
    ];
}
