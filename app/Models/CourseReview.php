<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseReview extends Model
{
    protected $table = 'DANHGIAKH';
    protected $primaryKey = 'maDG';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'maHV',
        'maKH',
        'diemSo',
        'ngayDG',
        'nhanxet',
    ];

    protected $casts = [
        'diemSo' => 'float',
        'ngayDG' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'maKH', 'maKH');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'maHV', 'maHV');
    }
}

