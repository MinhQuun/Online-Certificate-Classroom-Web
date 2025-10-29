<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MiniTestStudentAnswer extends Model
{
    protected $table = 'MINITEST_STUDENT_ANSWERS';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'maKQDG',
        'maCauHoi',
        'maHV',
        'answer_choice',
        'answer_text',
        'is_correct',
        'diem',
        'teacher_feedback',
        'graded_at',
        'graded_by',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'diem' => 'decimal:2',
        'graded_at' => 'datetime',
    ];

    /**
     * Thuộc kết quả mini-test nào
     */
    public function result(): BelongsTo
    {
        return $this->belongsTo(MiniTestResult::class, 'maKQDG', 'maKQDG');
    }

    /**
     * Câu hỏi nào
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(MiniTestQuestion::class, 'maCauHoi', 'maCauHoi');
    }

    /**
     * Học viên nào
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'maHV', 'maHV');
    }

    /**
     * Giảng viên chấm điểm
     */
    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by', 'maND');
    }

    /**
     * Kiểm tra đã được chấm chưa
     */
    public function isGraded(): bool
    {
        return !is_null($this->diem) && !is_null($this->graded_at);
    }
}
