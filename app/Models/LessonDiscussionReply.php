<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonDiscussionReply extends Model
{
    protected $table = 'HOIDAP_BAIHOC_PHANHOI';

    protected $fillable = [
        'discussion_id',
        'maND',
        'noiDung',
        'parent_reply_id',
        'is_official',
    ];

    protected $casts = [
        'is_official' => 'boolean',
    ];

    public function discussion(): BelongsTo
    {
        return $this->belongsTo(LessonDiscussion::class, 'discussion_id', 'id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'maND', 'maND');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_reply_id', 'id');
    }
}

