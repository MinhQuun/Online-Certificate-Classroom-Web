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
        'auto_graded_score',
        'essay_score',
        'is_fully_graded',
        'nhanxet',
        'nop_luc',
        'completed_at',
        'graded_at',
    ];

    protected $casts = [
        'attempt_no' => 'integer',
        'diem'       => 'decimal:2',
        'auto_graded_score' => 'decimal:2',
        'essay_score' => 'decimal:2',
        'is_fully_graded' => 'boolean',
        'nop_luc'    => 'datetime',
        'completed_at' => 'datetime',
        'graded_at' => 'datetime',
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
}