<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ActivationCode extends Model
{
    protected $table = 'MA_KICH_HOAT';
    protected $primaryKey = 'id';
    public $timestamps = true; // created_at / updated_at

    protected $fillable = [
        'maHV',
        'maKH',
        'maHD',
        'code',
        'trangThai',
        'generated_at',
        'sent_at',
        'used_at',
        'expires_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'sent_at'      => 'datetime',
        'used_at'      => 'datetime',
        'expires_at'   => 'datetime',
    ];

    // --- Quan hệ tiện dụng ---

    // Học viên (bảng HOCVIEN) -> trong code bạn đặt là Student.php
    public function student()
    {
        return $this->belongsTo(Student::class, 'maHV', 'maHV');
    }

    // Khóa học (bảng KHOAHOC) -> model của bạn là Course.php
    public function course()
    {
        return $this->belongsTo(Course::class, 'maKH', 'maKH');
    }

    // Hóa đơn (bảng HOADON) -> model gợi ý đặt tên là Invoice.php
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'maHD', 'maHD');
    }

    // Enrollment (HOCVIEN_KHOAHOC) -> model của bạn là Enrollment.php
    // Eloquent không support composite key, nên mình dùng helper thay vì relation "chuẩn".
    public function enrollment()
    {
        return Enrollment::where('maHV', $this->maHV)
            ->where('maKH', $this->maKH)
            ->first();
    }

    // Helper: code còn hạn dùng không?
    public function isStillValid(): bool
    {
        if (!$this->expires_at) {
            return true;
        }
        return Carbon::now()->lte($this->expires_at);
    }
}
