<?php

namespace App\Models;

use App\Models\Traits\Relation\TestRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory, TestRelation;
    protected $fillable = [
        'title',
        'description',
        'type',
        'total_questions',
        'time_limit',
        'passing_score',
        'is_active',
    ];

    protected $casts = [
        'total_questions' => 'integer',
        'time_limit' => 'integer',
        'passing_score' => 'integer',
        'is_active' => 'boolean',
    ];
}
