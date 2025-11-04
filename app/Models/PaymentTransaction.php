<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    public const STATUS_PENDING = 'PENDING';
    public const STATUS_PAID = 'PAID';
    public const STATUS_FAILED = 'FAILED';

    // T?n b?ng th?c t? trong MySQL
    protected $table = 'GIAODICH_VNPAY';

    // Kh?a ch?nh
    protected $primaryKey = 'id';

    // Laravel s? d?ng created_at / updated_at c? s?n trong b?ng
    public $timestamps = true;

    // C?c c?t ???c ph?p fill h?ng lo?t (mass assign)
    protected $fillable = [
        'maHV',                 // ID h?c vi?n
        'maKH',                 // ID kh?a h?c
        'maGoi',                // ID g?i kh?a h?c
        'soTien',               // s? ti?n VND
        'maKM',
        'txn_ref',              // M? giao d?ch g?i sang VNPay
        'trangThai',            // PENDING / PAID / FAILED
        'vnp_response_code',    // M? ph?n h?i VNPay
        'vnp_transaction_no',   // M? giao d?ch VNPay/ng?n h?ng
        'paid_at',              // Th?i ?i?m x?c nh?n thanh to?n th?nh c?ng
        'maHD',                 // H?a ?n li?n quan
        'order_snapshot',
        'payment_url',
        'client_ip',
        'user_agent',
    ];

    // ?p ki?u (cast) cho m?t s? c?t
    protected $casts = [
        'soTien' => 'decimal:2',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'order_snapshot' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'maHV', 'maHV');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'maKH', 'maKH');
    }

    public function combo()
    {
        return $this->belongsTo(Combo::class, 'maGoi', 'maGoi');
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'maKM', 'maKM');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'maHD', 'maHD');
    }
}
