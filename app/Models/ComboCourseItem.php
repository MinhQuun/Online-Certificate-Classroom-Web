<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComboCourseItem extends Model
{
    protected $table = 'GOI_KHOA_HOC_CHITIET';
    public $timestamps = false;        // bảng chỉ có created_at (không updated_at)
    public $incrementing = false;      // khóa chính là (maGoi, maKH)
    protected $fillable = ['maGoi','maKH','thuTu','created_at'];

    public function combo()  { return $this->belongsTo(Combo::class,  'maGoi', 'maGoi'); }
    public function course() { return $this->belongsTo(Course::class, 'maKH',  'maKH'); }
}
