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

    public function miniTest(): BelongsTo
    {
        return $this->belongsTo(MiniTest::class, 'maMT', 'maMT');
    }
}

