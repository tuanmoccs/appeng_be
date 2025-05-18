<?php

namespace App\Models;

use App\Models\Traits\Relation\UserQuizResultRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuizResult extends Model
{
    use HasFactory, UserQuizResultRelation;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'total_questions',
        'answers',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'answers' => 'array',
    ];
}
