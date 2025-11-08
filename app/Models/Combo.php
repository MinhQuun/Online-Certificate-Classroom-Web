<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Combo extends Model
{
    protected $table = 'GOI_KHOA_HOC';
    protected $primaryKey = 'maGoi';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'tenGoi',
        'slug',
        'moTa',
        'gia',
        'giaGoc',
        'hinhanh',
        'ngayBatDau',
        'ngayKetThuc',
        'trangThai',
        'rating_avg',
        'rating_count',
        'created_by',
    ];

    protected $casts = [
        'gia' => 'decimal:2',
        'giaGoc' => 'decimal:2',
        'ngayBatDau' => 'date',
        'ngayKetThuc' => 'date',
        'rating_avg' => 'decimal:2',
    ];

    /* -------------------------------------------------
    | Scopes
    | -------------------------------------------------
    */

    public function scopePublished($query)
    {
        return $query->where('trangThai', 'PUBLISHED');
    }

    public function scopeAvailable($query)
    {
        $today = Carbon::today();

        return $query
            ->where('trangThai', 'PUBLISHED')
            ->where(function ($q) use ($today) {
                $q->whereNull('ngayBatDau')
                    ->orWhere('ngayBatDau', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('ngayKetThuc')
                    ->orWhere('ngayKetThuc', '>=', $today);
            });
    }

    /* -------------------------------------------------
    | Relations
    | -------------------------------------------------
    */

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'maND');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'GOI_KHOA_HOC_CHITIET', 'maGoi', 'maKH')
            ->withPivot(['thuTu', 'created_at'])
            ->orderByPivot('thuTu');
    }

    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class, 'KHUYEN_MAI_GOI', 'maGoi', 'maKM')
            ->withPivot(['giaUuDai', 'created_at']);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceComboItem::class, 'maGoi', 'maGoi');
    }

    public function vnpayTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'maGoi', 'maGoi');
    }

    /* -------------------------------------------------
    | Accessors & helpers
    | -------------------------------------------------
    */

    public function getCoverImageUrlAttribute(): string
    {
        if ($this->hinhanh) {
            if (Str::startsWith($this->hinhanh, ['http://', 'https://'])) {
                return $this->hinhanh;
            }

            if ($resolved = $this->resolveCoverAssetPath($this->hinhanh)) {
                return $resolved;
            }
        }

        if ($slugBanner = $this->resolveSlugBanner($this->slug)) {
            if ($resolved = $this->resolveCoverAssetPath($slugBanner)) {
                return $resolved;
            }
        }

        if ($resolved = $this->resolveCoverAssetPath('combo_khoahoc.png')) {
            return $resolved;
        }

        return asset('Assets/logo.png');
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->isCurrentlyAvailable();
    }

    public function getSalePriceAttribute(): int
    {
        $promotion = $this->active_promotion;
        $base = $this->castToInteger($this->gia);

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
        $reference = $this->giaGoc ?: $this->gia;

        if (!$reference && $this->relationLoaded('courses')) {
            $reference = $this->courses->sum('hocPhi');
        }

        return max(0, $this->castToInteger($reference));
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

    public function isCurrentlyAvailable(): bool
    {
        if ($this->trangThai !== 'PUBLISHED') {
            return false;
        }

        $today = Carbon::today();

        $started = !$this->ngayBatDau || $this->ngayBatDau->lte($today);
        $notEnded = !$this->ngayKetThuc || $this->ngayKetThuc->gte($today);

        return $started && $notEnded;
    }

    protected function promotionIsApplicable(Promotion $promotion, ?Carbon $today = null): bool
    {
        $today ??= Carbon::today();

        if (!in_array($promotion->apDungCho, [Promotion::TARGET_COMBO, Promotion::TARGET_BOTH], true)) {
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

    protected function resolveCoverAssetPath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $normalized = ltrim($path, '/');
        $directories = [
            '',
            'Assets/Combos/',
            'Assets/Combo/',
            'Assets/Images/Combos/',
            'Assets/Images/',
            'Assets/',
        ];

        foreach ($directories as $directory) {
            $relativePath = $directory
                ? rtrim($directory, '/') . '/' . $normalized
                : $normalized;
            $relativePath = str_replace('//', '/', $relativePath);

            if (file_exists(public_path($relativePath))) {
                return asset($relativePath);
            }
        }

        return null;
    }

    protected function resolveSlugBanner(?string $slug): ?string
    {
        if (!$slug) {
            return null;
        }

        $map = [
            'toeic-combo' => 'combo_khoahoc.png',
            'toeic-foundation-full-pack-405-600' => 'combo_toeic_foundation_405-600.jpg',
            'toeic-intermediate-full-pack-605-780' => 'combo_toeic_intermediate_605-780.jpg',
            'toeic-advanced-full-pack-785-990' => 'combo_toeic_advanced_785-990.jpg',
            'toeic-foundation-405-600' => 'combo_toeic_foundation_405-600.jpg',
            'toeic-intermediate-605-780' => 'combo_toeic_intermediate_605-780.jpg',
            'toeic-advanced-785-990' => 'combo_toeic_advanced_785-990.jpg',
        ];

        return $map[$slug] ?? null;
    }
}
