<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MiniTestMaterial extends Model
{
    protected $table = 'minitest_tailieu';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'maMT',
        'tenTL',
        'loai',
        'mime_type',
        'visibility',
        'public_url',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function miniTest(): BelongsTo
    {
        return $this->belongsTo(MiniTest::class, 'maMT', 'maMT');
    }

    public function getNameAttribute(): string
    {
        return $this->attributes['tenTL'] ?? '';
    }

    public function setNameAttribute(string $value): void
    {
        $this->attributes['tenTL'] = $value;
    }

    public function getTypeAttribute(): string
    {
        return $this->attributes['loai'] ?? '';
    }

    public function setTypeAttribute(string $value): void
    {
        $this->attributes['loai'] = $value;
    }

    public function getIcon(): string
    {
        return match (strtolower($this->type)) {
            'pdf'      => 'bi bi-file-earmark-pdf',
            'audio',
            'mp3'      => 'bi bi-file-earmark-music',
            'image',
            'jpg',
            'jpeg',
            'png'      => 'bi bi-image',
            'zip',
            'rar'      => 'bi bi-file-zip',
            default    => 'bi bi-file-earmark-text',
        };
    }
}
