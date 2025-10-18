<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chapter extends Model
{
    protected $table = 'chuong';
    protected $primaryKey = 'maChuong';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'maKH', 'tenChuong', 'thuTu', 'moTa',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'maKH', 'maKH');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'maChuong', 'maChuong')->orderBy('thuTu');
    }

    public function miniTests(): HasMany
    {
        return $this->hasMany(MiniTest::class, 'maChuong', 'maChuong')->orderBy('thuTu');
    }
}
