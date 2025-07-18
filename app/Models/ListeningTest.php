<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ListeningTestRelation;
use App\Models\Traits\Relation\ListeningTestRelation as RelationListeningTestRelation;

class ListeningTest extends Model
{
  use HasFactory, RelationListeningTestRelation;

  protected $fillable = [
    'title',
    'description',
    'type',
    'time_limit',
    'passing_score',
    'total_questions',
    'is_active',
  ];

  protected $casts = [
    'is_active' => 'boolean',
  ];

  // Update total questions count
  public function updateTotalQuestions()
  {
    $totalQuestions = $this->sections()
      ->withCount('questions')
      ->get()
      ->sum('questions_count');

    $this->update(['total_questions' => $totalQuestions]);

    return $totalQuestions;
  }

  // Get all questions from all sections
  public function getAllQuestions()
  {
    return ListeningQuestion::whereHas('section', function ($query) {
      $query->where('listening_test_id', $this->id);
    })->orderBy('listening_section_id')->orderBy('order')->get();
  }

  // Check if test is ready (has questions)
  public function isReady()
  {
    return $this->total_questions > 0;
  }
}
