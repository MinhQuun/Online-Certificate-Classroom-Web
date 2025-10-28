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

    protected $fillable = [
        'maKH',
        'maChuong',
        'title',
        'thuTu',
        'max_score',
        'trongSo',
        'time_limit_min',
        'attempts_allowed',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
}

