<?php

namespace App\Models;

use App\Models\Traits\Relation\QuizQuestionRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory, QuizQuestionRelation;
    protected $fillable = [
        'quiz_id',
        'question',
        'options',
        'correct_answer',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'options' => 'array',
    ];
}
