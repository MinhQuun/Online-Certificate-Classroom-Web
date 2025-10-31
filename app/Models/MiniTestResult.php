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
        'status',
        'diem',
        'auto_graded_score',
        'essay_score',
        'is_fully_graded',
        'nhanxet',
        'started_at',
        'expires_at',
        'nop_luc',
        'completed_at',
        'graded_at',
        'submitted_late',
        'time_spent_sec',
    ];

    protected $casts = [
        'attempt_no' => 'integer',
        'diem'       => 'decimal:2',
        'auto_graded_score' => 'decimal:2',
        'essay_score' => 'decimal:2',
        'is_fully_graded' => 'boolean',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'nop_luc'    => 'datetime',
        'completed_at' => 'datetime',
        'graded_at' => 'datetime',
        'submitted_late' => 'boolean',
        'time_spent_sec' => 'integer',
    ];

    public const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    public const STATUS_SUBMITTED   = 'SUBMITTED';
    public const STATUS_EXPIRED     = 'EXPIRED';

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

    // Các câu trả lời của học viên
    public function studentAnswers()
    {
        return $this->hasMany(MiniTestStudentAnswer::class, 'maKQDG', 'maKQDG');
    }

    // Kiểm tra đã chấm xong chưa
    public function isFullyGraded(): bool
    {
        return $this->is_fully_graded;
    }

    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isSubmitted(): bool
    {
        return in_array($this->status, [self::STATUS_SUBMITTED, self::STATUS_EXPIRED], true);
    }

    public function started(): bool
    {
        return !is_null($this->started_at);
    }
}
