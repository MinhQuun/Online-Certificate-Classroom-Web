<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    protected $table = 'baihoc';
    protected $primaryKey = 'maBH';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'maChuong', 'tieuDe', 'moTa', 'thuTu', 'loai',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class, 'maChuong', 'maChuong');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class, 'maBH', 'maBH');
    }
}

