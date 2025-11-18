<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class StudentNotification extends Model
{
    protected $table = 'thongbao';
    protected $primaryKey = 'maTB';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'maND',
        'maKH',
        'maGoi',
        'tieuDe',
        'noiDung',
        'loai',
        'action_url',
        'action_label',
        'hinhAnh',
        'metadata',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'metadata' => 'array',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* -------------------------------------------------
     | Scopes
     | -------------------------------------------------
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('maND', $userId);
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('created_at')->orderByDesc($this->primaryKey);
    }

    /* -------------------------------------------------
     | Relations
     | -------------------------------------------------
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'maND', 'maND');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'maKH', 'maKH');
    }

    public function combo(): BelongsTo
    {
        return $this->belongsTo(Combo::class, 'maGoi', 'maGoi');
    }

    /* -------------------------------------------------
     | Helpers & accessors
     | -------------------------------------------------
     */
    public function markAsRead(): void
    {
        if ($this->is_read) {
            return;
        }

        $this->forceFill([
            'is_read' => true,
            'read_at' => Carbon::now(),
        ])->save();
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->hinhAnh) {
            $normalized = ltrim($this->hinhAnh, '/');

            if (Str::startsWith($normalized, ['http://', 'https://'])) {
                return $normalized;
            }

            $paths = [
                'Assets/Images/' . $normalized,
                'Assets/' . $normalized,
                $normalized,
            ];

            foreach ($paths as $path) {
                if (file_exists(public_path($path))) {
                    return asset($path);
                }
            }
        }

        if ($this->course) {
            return $this->course->cover_image_url;
        }

        if ($this->combo) {
            return $this->combo->cover_image_url;
        }

        return asset('Assets/logo.png');
    }

    public function getResolvedActionUrlAttribute(): ?string
    {
        if (!empty($this->action_url)) {
            return $this->action_url;
        }

        if ($this->course) {
            return route('student.courses.show', $this->course->slug);
        }

        if ($this->combo) {
            return route('student.combos.show', $this->combo->slug);
        }

        return route('student.courses.index');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->loai) {
            'GRADE' => 'Chấm điểm',
            'PROMOTION' => 'Khuyến mãi',
            'COURSE' => 'Hoạt động khóa học',
            default => 'Thông báo',
        };
    }

    public function getBadgeToneAttribute(): string
    {
        return match ($this->loai) {
            'PROMOTION' => 'accent',
            'GRADE' => 'highlight',
            'COURSE' => 'primary',
            default => 'muted',
        };
    }

    public function getTimeLabelAttribute(): string
    {
        if (!$this->created_at) {
            return '';
        }

        return $this->created_at->diffForHumans(now(), true) . ' trước';
    }
}
