<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Course extends Model
{
    protected $table = 'khoahoc';
    protected $primaryKey = 'maKH';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'maDanhMuc', 'tenKH', 'slug', 'hocPhi', 'moTa', 'ngayBatDau', 'ngayKetThuc', 'hinhanh', 'thoiHanNgay', 'trangThai',
    ];

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class, 'maKH', 'maKH')->orderBy('thuTu');
    }

    public function finalTests(): HasMany
    {
        return $this->hasMany(CourseTest::class, 'maKH', 'maKH')->orderBy('maTest');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'maDanhMuc', 'maDanhMuc');
    }

    public function scopePublished($query)
    {
        return $query->where('trangThai', 'PUBLISHED');
    }

    public function getCoverImageUrlAttribute(): string
    {
        if (!$this->hinhanh) {
            return asset('Assets/logo.png');
        }

        if (Str::startsWith($this->hinhanh, ['http://', 'https://'])) {
            return $this->hinhanh;
        }

        $normalized = ltrim($this->hinhanh, '/');

        $aliases = [
            'toeic_speaking.png' => 'Assets/Images/toeic-noi.png',
            'toeic_writing.png'  => 'Assets/Images/toeic-viet.png',
            'toeic_listening.png' => 'Assets/Images/toeic-nghe.png',
            'toeic_reading.png'  => 'Assets/Images/toeic-doc.png',
        ];

        $candidatePaths = [];

        if (isset($aliases[$normalized])) {
            $candidatePaths[] = $aliases[$normalized];
        }

        $candidatePaths[] = 'Assets/Images/' . $normalized;
        $candidatePaths[] = 'Assets/' . $normalized;
        $candidatePaths[] = $normalized;

        $candidatePaths = array_unique($candidatePaths);

        foreach ($candidatePaths as $relativePath) {
            if (file_exists(public_path($relativePath))) {
                return asset($relativePath);
            }
        }

        return asset('Assets/' . $normalized);
    }
}
