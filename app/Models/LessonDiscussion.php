<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class LessonDiscussion extends Model
{
    protected $table = 'HOIDAP_BAIHOC';

    protected $fillable = [
        'maBH',
        'maND',
        'noiDung',
        'status',
        'is_pinned',
        'is_locked',
        'reply_count',
        'last_replied_at',
    ];

    protected $casts = [
        'is_pinned'       => 'boolean',
        'is_locked'       => 'boolean',
        'reply_count'     => 'integer',
        'last_replied_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (LessonDiscussion $discussion) {
            if (empty($discussion->last_replied_at)) {
                $discussion->last_replied_at = Carbon::now();
            }
        });
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'maBH', 'maBH');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'maND', 'maND');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(LessonDiscussionReply::class, 'discussion_id', 'id')
            ->orderBy('created_at');
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('status', '!=', 'HIDDEN');
    }

    public function isResolved(): bool
    {
        return $this->status === 'RESOLVED';
    }

    public function isHidden(): bool
    {
        return $this->status === 'HIDDEN';
    }

    public function markResolved(bool $resolved = true): void
    {
        $this->status = $resolved ? 'RESOLVED' : 'OPEN';
        $this->save();
    }

    public function hide(): void
    {
        $this->status = 'HIDDEN';
        $this->save();
    }
}

