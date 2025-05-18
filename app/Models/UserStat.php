<?php

namespace App\Models;

use App\Models\Traits\Relation\UserStatRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStat extends Model
{
    use HasFactory, UserStatRelation;

    protected $fillable = [
        'user_id',
        'words_learned',
        'lessons_completed',
        'quizzes_completed',
        'streak_days',
        'last_activity_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_activity_at' => 'datetime',
    ];
}
