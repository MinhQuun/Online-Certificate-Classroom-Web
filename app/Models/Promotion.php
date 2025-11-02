<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Promotion extends Model
{
    protected $table = 'KHUYEN_MAI';
    protected $primaryKey = 'maKM';
    protected $fillable = [
        'tenKM','moTa','loaiUuDai','giaTriUuDai','ngayBatDau','ngayKetThuc',
        'soLuongGioiHan','trangThai','created_by'
    ];
    protected $casts = [
        'giaTriUuDai' => 'decimal:2',
        'ngayBatDau' => 'date',
        'ngayKetThuc' => 'date',
    ];

    const TYPE_FIXED   = 'FIXED_DISCOUNT';
    const TYPE_PERCENT = 'PERCENT_DISCOUNT';

    public function combos(): BelongsToMany
    {
        return $this->belongsToMany(Combo::class, 'KHUYEN_MAI_GOI', 'maKM', 'maGoi')
                    ->withPivot(['giaUuDai','created_at']);
    }

    public function createdBy() { return $this->belongsTo(User::class, 'created_by', 'maND'); }
}
