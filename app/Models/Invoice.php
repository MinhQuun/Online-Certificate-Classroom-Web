<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'HOADON';
    protected $primaryKey = 'maHD';
    public $timestamps = true;

    protected $fillable = [
        'maHV',
        'maTT',
        'maND',
        'ngayLap',
        'tongTien',
        'ghiChu',
        'trangThai',
        'loai',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'maHV', 'maHV');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'maHD', 'maHD');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'maTT', 'maTT');
    }
}
