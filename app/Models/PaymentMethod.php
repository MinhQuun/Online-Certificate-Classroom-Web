<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $table = 'PHUONGTHUCTHANHTOAN';
    protected $primaryKey = 'maTT';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'maTT',
        'tenPhuongThuc',
    ];
}

