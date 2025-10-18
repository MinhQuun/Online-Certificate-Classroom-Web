<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    protected $table = 'tailieuhoctap';
    protected $primaryKey = 'maTL';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'maBH', 'tenTL', 'loai', 'kichThuoc', 'moTa', 'mime_type', 'visibility', 'public_url',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'maBH', 'maBH');
    }
}

