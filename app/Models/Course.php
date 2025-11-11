<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class, 'khuyen_mai_khoahoc', 'maKH', 'maKM')
                    ->withPivot(['giaUuDai','created_at']);
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

    public function scopeArchived($query)
    {
        return $query->where('trangThai', 'ARCHIVED');
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

    public function getSalePriceAttribute(): int
    {
        $promotion = $this->active_promotion;
        $base = $this->castToInteger($this->hocPhi);

        if (!$promotion || !$this->promotionIsApplicable($promotion)) {
            return max(0, $base);
        }

        if ($promotion->pivot && $promotion->pivot->giaUuDai) {
            return max(0, $this->castToInteger($promotion->pivot->giaUuDai));
        }

        $promotionValue = $this->castToInteger($promotion->giaTriUuDai);

        if ($promotion->loaiUuDai === Promotion::TYPE_FIXED) {
            return max(0, $base - $promotionValue);
        }

        if ($promotion->loaiUuDai === Promotion::TYPE_PERCENT) {
            $discount = round($base * ($promotionValue / 100));

            return max(0, $base - (int) $discount);
        }

        return max(0, $base);
    }

    public function getOriginalPriceAttribute(): int
    {
        return max(0, $this->castToInteger($this->hocPhi));
    }

    public function getSavingAmountAttribute(): int
    {
        return max(0, $this->original_price - $this->sale_price);
    }

    public function getSavingPercentAttribute(): int
    {
        if ($this->original_price <= 0) {
            return 0;
        }

        return (int) round(($this->saving_amount / $this->original_price) * 100);
    }

    public function getActivePromotionAttribute(): ?Promotion
    {
        if (!$this->relationLoaded('promotions')) {
            $this->load('promotions');
        }

        $today = Carbon::today();

        return $this->promotions
            ->filter(fn (Promotion $promotion) => $this->promotionIsApplicable($promotion, $today))
            ->sortByDesc(fn (Promotion $promotion) => $promotion->pivot?->created_at ?? $promotion->created_at)
            ->first();
    }

    protected function promotionIsApplicable(Promotion $promotion, ?Carbon $today = null): bool
    {
        $today ??= Carbon::today();

        if (!in_array($promotion->apDungCho, [Promotion::TARGET_COURSE, Promotion::TARGET_BOTH], true)) {
            return false;
        }

        if ($promotion->trangThai !== 'ACTIVE') {
            return false;
        }

        if ($promotion->ngayBatDau && Carbon::parse($promotion->ngayBatDau)->gt($today)) {
            return false;
        }

        if ($promotion->ngayKetThuc && Carbon::parse($promotion->ngayKetThuc)->lt($today)) {
            return false;
        }

        if ($promotion->soLuongGioiHan !== null && $promotion->soLuongGioiHan <= 0) {
            return false;
        }

        return true;
    }

    protected function castToInteger($value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        if ($value instanceof \Stringable) {
            $value = (string) $value;
        }

        if (is_int($value)) {
            return $value;
        }

        if (is_float($value)) {
            return (int) round($value);
        }

        if (is_string($value)) {
            $trimmed = trim($value);

            if ($trimmed === '') {
                return 0;
            }

            if (is_numeric($trimmed)) {
                return (int) round((float) $trimmed);
            }

            $filtered = preg_replace('/[^\d.,-]/', '', $trimmed) ?: '0';

            $isNegative = Str::startsWith($filtered, '-');
            $filtered = ltrim($filtered, '+-');
            $filtered = str_replace(',', '.', $filtered);

            if (substr_count($filtered, '.') > 1) {
                $segments = explode('.', $filtered);
                $fraction = array_pop($segments);
                $filtered = implode('', $segments) . '.' . $fraction;
            }

            $fractionLength = null;
            if (($pos = strrpos($filtered, '.')) !== false) {
                $fractionLength = strlen(substr($filtered, $pos + 1));
            }

            if ($fractionLength === null || $fractionLength === 0 || $fractionLength > 2) {
                $filtered = str_replace('.', '', $filtered);
            }

            $numeric = $filtered === '' ? '0' : $filtered;

            if ($isNegative && $numeric !== '0') {
                $numeric = '-' . $numeric;
            }

            return (int) round((float) $numeric);
        }

        return (int) round((float) $value);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'maKH', 'maKH');
    }

    public function certificateTemplate()
    {
        return $this->hasOne(CertificateTemplate::class, 'maKH', 'maKH');
    }

}
