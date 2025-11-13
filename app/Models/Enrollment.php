<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    use HasFactory;

    protected $table = 'hocvien_khoahoc';
    public $timestamps = true;

    protected $primaryKey = null;
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'maHV',
        'maKH',
        'ngayNhapHoc',
        'trangThai',
        'activated_at',
        'expires_at',
        'progress_percent',
        'video_progress_percent',
        'avg_minitest_score',
        'last_lesson_id',
        'maGoi',
        'maKM',
        'completed_at',
    ];

    protected $casts = [
        'progress_percent' => 'integer',
        'video_progress_percent' => 'integer',
        'avg_minitest_score' => 'decimal:2',
        'ngayNhapHoc' => 'date',
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'maGoi' => 'integer',
        'maKM' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'maHV', 'maHV');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'maKH', 'maKH');
    }

    public function lastLesson()
    {
        return $this->belongsTo(Lesson::class, 'last_lesson_id', 'maBH');
    }

    public function lessonProgressEntries(): HasMany
    {
        return $this->hasMany(LessonProgress::class, 'maHV', 'maHV');
    }

    public function miniTestResults(): HasMany
    {
        return $this->hasMany(MiniTestResult::class, 'maHV', 'maHV');
    }

    public function courseProgressEntries(): HasMany
    {
        return $this->hasMany(LessonProgress::class, 'maHV', 'maHV')
            ->where('maKH', $this->maKH);
    }

    public function courseMiniTestResults(): HasMany
    {
        return $this->hasMany(MiniTestResult::class, 'maHV', 'maHV')
            ->where('maKH', $this->maKH);
    }

    public function combo()
    {
        return $this->belongsTo(Combo::class, 'maGoi', 'maGoi');
    }
}
