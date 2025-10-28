<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $table = 'HOCVIEN_KHOAHOC';
    public $timestamps = true;

    // không có cột id tự tăng
    protected $primaryKey = null;
    public $incrementing = false;
    protected $keyType = 'string'; // để Laravel khỏi ép về int

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'maHV',
        'maKH',
        'ngayNhapHoc',
        'trangThai',               // PENDING / ACTIVE / EXPIRED
        'activated_at',
        'expires_at',
        'progress_percent',        // % tổng tiến độ
        'video_progress_percent',  // % số video đã xem xong
        'avg_minitest_score',      // điểm TB mini-test
        'last_lesson_id',          // bài gần nhất học
    ];

    protected $casts = [
        'progress_percent'        => 'integer',
        'video_progress_percent'  => 'integer',
        'avg_minitest_score'      => 'decimal:2',
        'ngayNhapHoc'             => 'date',
        'activated_at'            => 'datetime',
        'expires_at'              => 'datetime',
    ];

    // Học viên
    public function student()
    {
        return $this->belongsTo(Student::class, 'maHV', 'maHV');
    }

    // Khoá học
    public function course()
    {
        return $this->belongsTo(Course::class, 'maKH', 'maKH');
    }

    // Bài học cuối cùng mà học viên đã mở
    public function lastLesson()
    {
        return $this->belongsTo(Lesson::class, 'last_lesson_id', 'maBH');
    }

    // Tiến độ chi tiết (LessonProgress). Khi query nhớ lọc maKH nữa.
    public function lessonProgressEntries()
    {
        return $this->hasMany(LessonProgress::class, 'maHV', 'maHV');
    }

    // Kết quả mini-test trong khóa này
    public function miniTestResults()
    {
        return $this->hasMany(MiniTestResult::class, 'maHV', 'maHV');
    }
}