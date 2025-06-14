<?php

namespace App\Models;

use App\Models\Traits\Relation\TestQuestionRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestQuestion extends Model
{
    use HasFactory, TestQuestionRelation;
    protected $fillable = [
        'test_id',
        'question',
        'options',
        'correct_answer',
        'difficulty',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
    ];
}
