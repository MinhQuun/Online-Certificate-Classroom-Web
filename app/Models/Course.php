<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
