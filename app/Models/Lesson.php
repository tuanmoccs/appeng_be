<?php

namespace App\Models;

use App\Models\Traits\Relation\LessonRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory, LessonRelation;

    protected $fillable = [
        'title',
        'description',
        'content',
        'quiz',
        'level',
        'duration',
        'order',
        'is_locked',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'content' => 'array',
        'quiz' => 'array',
        'is_locked' => 'boolean',
        'duration' => 'integer',
        'order' => 'integer',
    ];
    public function hasQuiz()
    {
        return !empty($this->quiz) && isset($this->quiz['questions']);
    }

    public function getQuizPassingScore()
    {
        return $this->quiz['passing_score'] ?? 80;
    }

    public function countQuizQuestions()
    {
        return count($this->quiz['questions'] ?? []);
    }
}
