<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'HOCVIEN';
    protected $primaryKey = 'maHV';
    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'maND',
        'hoTen',
        'ngaySinh',
        'ngayNhapHoc',
    ];

    protected $casts = [
        'ngaySinh'     => 'date',
        'ngayNhapHoc'  => 'date',
    ];

    // Hồ sơ học viên gắn với tài khoản user
    public function user()
    {
        return $this->belongsTo(User::class, 'maND', 'maND');
    }

    // Ghi danh vào các khoá học
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'maHV', 'maHV');
    }

    // Tiến độ học chi tiết từng bài
    public function lessonProgressEntries()
    {
        return $this->hasMany(LessonProgress::class, 'maHV', 'maHV');
    }

    // Kết quả mini-test theo từng chương
    public function miniTestResults()
    {
        return $this->hasMany(MiniTestResult::class, 'maHV', 'maHV');
    }
}