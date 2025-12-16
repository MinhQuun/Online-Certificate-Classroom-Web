<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
    use HasFactory;

    protected $table = 'tiendo_hoctap';
    protected $primaryKey = 'id';
    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'maHV',
        'maKH',
        'maBH',
        'trangThai',                // NOT_STARTED / IN_PROGRESS / COMPLETED
        'thoiGianHoc',
        'lanXemCuoi',
        'soLanXem',
        'video_progress_seconds',
        'video_duration_seconds',
        'completed_at',
        'demo_passed_by',
        'demo_passed_at',
        'demo_pass_reason',
        'ghiChu',
    ];

    protected $casts = [
        'thoiGianHoc'            => 'integer',
        'soLanXem'               => 'integer',
        'video_progress_seconds' => 'integer',
        'video_duration_seconds' => 'integer',
        'is_video_completed'     => 'boolean',
        'lanXemCuoi'             => 'datetime',
        'completed_at'           => 'datetime',
        'demo_passed_by'         => 'integer',
        'demo_passed_at'         => 'datetime',
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

    // Bài học
    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'maBH', 'maBH');
    }

    // Enrollment tương ứng (hv trong khoá)
    // (Composite key nên khi dùng bạn thường where maHV + maKH)
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'maHV', 'maHV');
    }
}
