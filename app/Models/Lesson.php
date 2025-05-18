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
        'level',
        'duration',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'content' => 'array',
    ];
}
