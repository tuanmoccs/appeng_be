<?php

namespace App\Models;

use App\Models\Traits\Relation\QuizRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory, QuizRelation;
    protected $fillable = [
        'title',
        'description',
        'lesson_id',
    ];
}
