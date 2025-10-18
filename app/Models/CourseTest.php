<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseTest extends Model
{
    protected $table = 'test';
    protected $primaryKey = 'maTest';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'maKH',
        'dotTest',
        'title',
        'time_limit_min',
        'total_questions',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'maKH', 'maKH');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(CourseTestMaterial::class, 'maTest', 'maTest');
    }
}

