<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'type',
        'total_questions',
        'time_limit',
        'passing_score',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(TestQuestion::class);
    }

    public function results()
    {
        return $this->hasMany(UserTestResult::class);
    }
}
