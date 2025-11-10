<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceComboItem extends Model
{
    protected $table = 'cthd_goi';
    public $timestamps = false;
    public $incrementing = false; // khóa chính (maHD, maGoi)
    protected $fillable = ['maHD','maGoi','soLuong','donGia','maKM'];
    protected $casts = [
        'soLuong' => 'int',
        'donGia'  => 'decimal:2',
        'thanhTien' => 'decimal:2',  // cột computed trong DB
    ];

    public function invoice()   { return $this->belongsTo(Invoice::class, 'maHD',  'maHD'); }
    public function combo()     { return $this->belongsTo(Combo::class,   'maGoi', 'maGoi'); }
    public function promotion() { return $this->belongsTo(Promotion::class,'maKM', 'maKM'); }
}
