<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Promotion extends Model
{
    protected $table = 'KHUYEN_MAI';
    protected $primaryKey = 'maKM';
    protected $fillable = [
        'tenKM',
        'moTa',
        'loaiUuDai',
        'apDungCho',
        'giaTriUuDai',
        'ngayBatDau',
        'ngayKetThuc',
        'soLuongGioiHan',
        'trangThai',
        'created_by',
    ];
    protected $casts = [
        'giaTriUuDai' => 'decimal:2',
        'ngayBatDau' => 'date',
        'ngayKetThuc' => 'date',
        'apDungCho' => 'string',
    ];

    const TYPE_FIXED   = 'FIXED_DISCOUNT';
    const TYPE_PERCENT = 'PERCENT_DISCOUNT';
    const TYPE_GIFT    = 'GIFT';

    const TARGET_COMBO  = 'COMBO';
    const TARGET_COURSE = 'COURSE';
    const TARGET_BOTH   = 'BOTH';

    public function combos(): BelongsToMany
    {
        return $this->belongsToMany(Combo::class, 'KHUYEN_MAI_GOI', 'maKM', 'maGoi')
                    ->withPivot(['giaUuDai','created_at']);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'KHUYEN_MAI_KHOAHOC', 'maKM', 'maKH')
                    ->withPivot(['giaUuDai','created_at']);
    }

    public function createdBy() { return $this->belongsTo(User::class, 'created_by', 'maND'); }
}
