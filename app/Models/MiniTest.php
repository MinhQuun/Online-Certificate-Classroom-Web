<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MiniTest extends Model
{
    protected $table = 'chuong_minitest';
    protected $primaryKey = 'maMT';
    public $incrementing = true;
    protected $keyType = 'int';

    public const SKILL_LISTENING = 'LISTENING';
    public const SKILL_READING   = 'READING';
    public const SKILL_WRITING   = 'WRITING';
    public const SKILL_SPEAKING  = 'SPEAKING';

    protected $fillable = [
        'maKH',
        'maChuong',
        'title',
        'skill_type',
        'thuTu',
        'max_score',
        'trongSo',
        'time_limit_min',
        'attempts_allowed',
        'is_active',
        'is_published',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'time_limit_min' => 'integer',
        'attempts_allowed' => 'integer',
        'max_score' => 'decimal:2',
        'trongSo' => 'decimal:2',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class, 'maChuong', 'maChuong');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'maKH', 'maKH');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(MiniTestMaterial::class, 'maMT', 'maMT');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(MiniTestQuestion::class, 'maMT', 'maMT')->orderBy('thuTu');
    }

    public function results(): HasMany
    {
        return $this->hasMany(MiniTestResult::class, 'maMT', 'maMT');
    }

    public function isSpeaking(): bool
    {
        return $this->skill_type === self::SKILL_SPEAKING;
    }

    public function isWriting(): bool
    {
        return $this->skill_type === self::SKILL_WRITING;
    }

    public function isListening(): bool
    {
        return $this->skill_type === self::SKILL_LISTENING;
    }

    public function isReading(): bool
    {
        return $this->skill_type === self::SKILL_READING;
    }

    public function setWeightAttribute($value): void
    {
        $this->attributes['trongSo'] = $value;
    }

    public function getWeightAttribute(): float
    {
        return (float) ($this->attributes['trongSo'] ?? 0);
    }
}
