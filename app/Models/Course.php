<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;

class Course extends Model
{
    protected $table = 'khoahoc';
    protected $primaryKey = 'maKH';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'maDanhMuc','maND','tenKH','slug','hocPhi','moTa',
        'ngayBatDau','ngayKetThuc','hinhanh','thoiHanNgay','trangThai',
    ];

    // (tuỳ chọn) giúp format ngày/thời gian & tránh lỗi so sánh
    protected $casts = [
        'ngayBatDau' => 'date',
        'ngayKetThuc'=> 'date',
        'hocPhi'     => 'integer',
        'thoiHanNgay'=> 'integer',
    ];

    /* ---------------- Relations ---------------- */

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class, 'maKH', 'maKH')->orderBy('thuTu');
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(
            Lesson::class,
            Chapter::class,
            'maKH',      // Chapter foreign key referencing Course
            'maChuong',  // Lesson foreign key referencing Chapter
            'maKH',      // Local key on Course
            'maChuong'   // Local key on Chapter
        )->orderBy('thuTu');
    }

    public function miniTests(): HasMany
    {
        return $this->hasMany(MiniTest::class, 'maKH', 'maKH')->orderBy('thuTu');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CourseReview::class, 'maKH', 'maKH');
    }

    public function category(): BelongsTo
    {
        // withDefault để tránh null khi hiển thị blade
        return $this->belongsTo(Category::class, 'maDanhMuc', 'maDanhMuc')
                    ->withDefault(['tenDanhMuc' => '(Không rõ)']);
    }

    public function teacher(): BelongsTo
    {
        // withDefault để tránh null khi hiển thị blade
        return $this->belongsTo(User::class, 'maND', 'maND')
                    ->withDefault(['hoTen' => '(Chưa gán)']);
    }

    /* ---------------- Scopes ---------------- */

    public function scopePublished($query)
    {
        return $query->where('trangThai', 'PUBLISHED');
    }

    /* ---------------- Accessors ---------------- */

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
            'toeic_speaking.png'  => 'Assets/Images/toeic-noi.png',
            'toeic_writing.png'   => 'Assets/Images/toeic-viet.png',
            'toeic_listening.png' => 'Assets/Images/toeic-nghe.png',
            'toeic_reading.png'   => 'Assets/Images/toeic-doc.png',
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

    public function getStartDateLabelAttribute(): string
    {
        return $this->formatDisplayDate($this->ngayBatDau) ?? 'Đang cập nhật';
    }

    public function getEndDateLabelAttribute(): string
    {
        return $this->formatDisplayDate($this->ngayKetThuc) ?? 'Đang cập nhật';
    }

    protected function formatDisplayDate($value): ?string
    {
        if (!$value) {
            return null;
        }

        $date = $value instanceof Carbon ? $value : Carbon::parse($value);

        return $date->format('d/m/Y');
    }

    public function getDefaultSkillTypeAttribute(): ?string
    {
        return $this->inferDefaultSkillType();
    }

    protected function inferDefaultSkillType(): ?string
    {
        $haystack = strtolower(trim(($this->slug ?? '') . ' ' . ($this->tenKH ?? '')));

        $map = [
            'listening' => MiniTest::SKILL_LISTENING,
            'speaking' => MiniTest::SKILL_SPEAKING,
            'reading' => MiniTest::SKILL_READING,
            'writing' => MiniTest::SKILL_WRITING,
        ];

        foreach ($map as $keyword => $skillType) {
            if ($keyword !== '' && str_contains($haystack, $keyword)) {
                return $skillType;
            }
        }

        return null;
    }
}
