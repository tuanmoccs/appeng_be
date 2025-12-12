<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestPassage extends Model
{
  protected $fillable = [
    'test_id',
    'title',
    'content',
    'order'
  ];

  protected $casts = [
    'order' => 'integer'
  ];

  public function test(): BelongsTo
  {
    return $this->belongsTo(Test::class);
  }

  public function questions(): HasMany
  {
    return $this->hasMany(TestQuestion::class, 'passage_id')->orderBy('order');
  }
}
