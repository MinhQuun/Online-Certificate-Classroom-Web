<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $table = 'cthd';
    public $incrementing = false;
    public $timestamps = false;

    protected $primaryKey = null; // vì PK là (maHD, maKH) composite
    protected $fillable = [
        'maHD', 'maKH', 'soLuong', 'donGia'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'maHD', 'maHD');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'maKH', 'maKH');
    }
}
