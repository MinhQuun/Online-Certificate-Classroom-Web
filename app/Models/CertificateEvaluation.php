<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateEvaluation extends Model
{
    use HasFactory;

    protected $table = 'chungchi_danhgia';   // bảng đánh giá chứng chỉ

    protected $guarded = [];

    // Bảng này chỉ có created_at, KHÔNG có updated_at
    public $timestamps = false;

    protected $casts = [
        'diem'      => 'decimal:2',
        'ngayCap'   => 'date',
        'created_at'=> 'datetime',
    ];

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class, 'maCC', 'maCC');
    }
}
