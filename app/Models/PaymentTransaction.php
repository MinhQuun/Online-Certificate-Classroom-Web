<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    // Tên bảng thực tế trong MySQL
    protected $table = 'GIAODICH_VNPAY';

    // Khóa chính
    protected $primaryKey = 'id';

    // Laravel sẽ dùng created_at / updated_at có sẵn trong bảng
    public $timestamps = true;

    // Các cột được phép fill hàng loạt (mass assign)
    protected $fillable = [
        'maHV',                 // ID học viên
        'maKH',                 // ID khóa học
        'soTien',               // số tiền VND
        'txn_ref',              // mã giao dịch gửi sang VNPay
        'trangThai',            // PENDING / PAID / FAILED
        'vnp_response_code',    // mã phản hồi VNPay
        'vnp_transaction_no',   // mã giao dịch VNPay/ngân hàng
        'paid_at',              // thời điểm xác nhận thanh toán thành công
    ];

    // Ép kiểu (cast) cho một số cột
    protected $casts = [
        'soTien'   => 'decimal:2',
        'paid_at'  => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Học viên liên quan tới giao dịch
     * HOCVIEN có khóa chính maHV. :contentReference[oaicite:14]{index=14}
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'maHV', 'maHV');
    }

    /**
     * Khóa học liên quan tới giao dịch
     * KHOAHOC có khóa chính maKH. :contentReference[oaicite:15]{index=15}
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'maKH', 'maKH');
    }
}
