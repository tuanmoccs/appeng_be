<?php

namespace App\Models;

use App\Models\Traits\Relation\WordRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    use HasFactory, WordRelation;
    protected $fillable = [
        'word',
        'translation',
        'pronunciation',
        'image_url',
        'audio_url',
        'example_sentence',
        'lesson_id',
    ];
}
