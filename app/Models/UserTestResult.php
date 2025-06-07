<?php

namespace App\Models;

use App\Models\Traits\Relation\UserTestResultRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTestResult extends Model
{
    use HasFactory, UserTestResultRelation;

    protected $fillable = [
        'user_id',
        'test_id',
        'score',
        'total_questions',
        'time_taken',
        'answers',
        'passed',
    ];

    protected $casts = [
        'answers' => 'array',
        'passed' => 'boolean',
    ];
}
