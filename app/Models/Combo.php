<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Combo extends Model
{
    protected $table = 'GOI_KHOA_HOC';
    protected $primaryKey = 'maGoi';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'tenGoi','slug','moTa','gia','giaGoc','hinhanh',
        'ngayBatDau','ngayKetThuc','trangThai','rating_avg','rating_count','created_by'
    ];

    protected $casts = [
        'gia' => 'decimal:2',
        'giaGoc' => 'decimal:2',
        'ngayBatDau' => 'date',
        'ngayKetThuc' => 'date',
        'rating_avg' => 'decimal:2',
    ];

    // Admin tạo gói
    public function creator() { return $this->belongsTo(User::class, 'created_by', 'maND'); }

    // Danh sách khóa học trong combo (qua bảng GOI_KHOA_HOC_CHITIET)
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'GOI_KHOA_HOC_CHITIET', 'maGoi', 'maKH')
                    ->withPivot(['thuTu','created_at']);
    }

    // Liên kết khuyến mãi áp dụng cho combo (KHUYEN_MAI_GOI)
    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class, 'KHUYEN_MAI_GOI', 'maGoi', 'maKM')
                    ->withPivot(['giaUuDai','created_at']);
    }

    // Dòng hóa đơn combo (CTHD_GOI)
    public function invoiceItems() { return $this->hasMany(InvoiceComboItem::class, 'maGoi', 'maGoi'); }

    // Giao dịch VNPay (nếu mua gói)
    public function vnpayTransactions() { return $this->hasMany(PaymentTransaction::class, 'maGoi', 'maGoi'); }
}
