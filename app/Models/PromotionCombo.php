<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionCombo extends Model
{
    protected $table = 'KHUYEN_MAI_GOI';
    public $timestamps = false;
    public $incrementing = false; // khóa chính (maKM, maGoi)
    protected $fillable = ['maKM','maGoi','giaUuDai','created_at'];

    public function promotion() { return $this->belongsTo(Promotion::class, 'maKM', 'maKM'); }
    public function combo()      { return $this->belongsTo(Combo::class,      'maGoi', 'maGoi'); }
}
