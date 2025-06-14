<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListeningQuestion extends Model
{
  use HasFactory;

  protected $fillable = [
    'listening_section_id',
    'question',
    'options',
    'correct_answer',
    'audio_file',
    'audio_start_time',
    'audio_end_time',
    'order',
  ];

  protected $casts = [
    'options' => 'array',
    'audio_start_time' => 'integer',
    'audio_end_time' => 'integer',
    'order' => 'integer',
  ];

  public function section()
  {
    return $this->belongsTo(ListeningSection::class, 'listening_section_id');
  }
}
