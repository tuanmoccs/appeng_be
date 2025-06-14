<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListeningSection extends Model
{
  use HasFactory;

  protected $fillable = [
    'listening_test_id',
    'title',
    'instructions',
    'audio_file',
    'audio_duration',
    'question_type',
    'order',
  ];

  protected $casts = [
    'audio_duration' => 'integer',
    'order' => 'integer',
  ];

  public function test()
  {
    return $this->belongsTo(ListeningTest::class, 'listening_test_id');
  }

  public function questions()
  {
    return $this->hasMany(ListeningQuestion::class);
  }
}
