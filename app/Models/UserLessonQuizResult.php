<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLessonQuizResult extends Model
{
  protected $fillable = [
    'user_id',
    'lesson_id',
    'score',
    'total_questions',
    'correct_answers',
    'time_taken',
    'answers',
    'is_passed',
    'attempt_number',
  ];

  protected $casts = [
    'answers' => 'array',
    'is_passed' => 'boolean',
    'score' => 'integer',
    'total_questions' => 'integer',
    'correct_answers' => 'integer',
  ];

  // Relationships
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function lesson()
  {
    return $this->belongsTo(Lesson::class);
  }

  // Helper methods
  public function getScorePercentage()
  {
    if ($this->total_questions == 0) {
      return 0;
    }
    return round(($this->correct_answers / $this->total_questions) * 100, 1);
  }

  public function isPassing()
  {
    return $this->score >= 80;
  }
}
