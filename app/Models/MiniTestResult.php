<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniTestResult extends Model
{
    use HasFactory;

    protected $table = 'KETQUA_MINITEST';
    protected $primaryKey = 'maKQDG';
    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'maMT',
        'maHV',
        'maKH',
        'attempt_no',
        'diem',
        'nhanxet',
        'nop_luc',
    ];

    protected $casts = [
        'attempt_no' => 'integer',
        'diem'       => 'decimal:2',
        'nop_luc'    => 'datetime',
    ];

    // Mini-test nào
    public function miniTest()
    {
        return $this->belongsTo(MiniTest::class, 'maMT', 'maMT');
    }

    // Học viên nào
    public function student()
    {
        return $this->belongsTo(Student::class, 'maHV', 'maHV');
    }

    // Thuộc khoá học nào
    public function course()
    {
        return $this->belongsTo(Course::class, 'maKH', 'maKH');
    }

    // Enrollment tương ứng
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'maHV', 'maHV');
    }
}