<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserListeningResult extends Model
{
  use HasFactory;

  protected $fillable = [
    'user_id',
    'listening_test_id',
    'score',
    'total_questions',
    'correct_answers',
    'time_taken',
    'completed',
    'answers',
  ];

  protected $casts = [
    'score' => 'float',
    'total_questions' => 'integer',
    'correct_answers' => 'integer',
    'time_taken' => 'integer',
    'completed' => 'boolean',
    'answers' => 'array',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function test()
  {
    return $this->belongsTo(ListeningTest::class, 'listening_test_id');
  }
}
