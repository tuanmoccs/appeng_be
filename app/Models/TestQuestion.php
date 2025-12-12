<?php

namespace App\Models;

use App\Models\Traits\Relation\TestQuestionRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestQuestion extends Model
{
    use HasFactory, TestQuestionRelation;
    protected $fillable = [
        'test_id',
        'passage_id',
        'type',
        'question',
        'options',
        'correct_answer',
        'difficulty',
        'order'
    ];

    protected $casts = [
        'options' => 'array',
        'order' => 'integer'
    ];

    public function isStandalone(): bool
    {
        return $this->type === 'standalone' || is_null($this->passage_id);
    }

    // Check nếu là câu hỏi có passage
    public function hasPassage(): bool
    {
        return $this->type === 'passage' && !is_null($this->passage_id);
    }
}
