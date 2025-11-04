<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionCourse extends Model
{
    protected $table = 'KHUYEN_MAI_KHOAHOC';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = ['maKM','maKH','giaUuDai','created_at'];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'maKM', 'maKM');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'maKH', 'maKH');
    }
}
