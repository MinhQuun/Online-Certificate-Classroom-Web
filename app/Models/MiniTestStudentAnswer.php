<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class MiniTestStudentAnswer extends Model
{
    protected $table = 'minitest_student_answers';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'maKQDG',
        'maCauHoi',
        'maHV',
        'answer_choice',
        'answer_text',
        'answer_audio_url',
        'audio_duration_sec',
        'audio_mime',
        'audio_size_kb',
        'is_correct',
        'diem',
        'teacher_feedback',
        'graded_at',
        'graded_by',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'diem' => 'decimal:2',
        'audio_duration_sec' => 'integer',
        'audio_size_kb' => 'integer',
        'graded_at' => 'datetime',
    ];

    public function result(): BelongsTo
    {
        return $this->belongsTo(MiniTestResult::class, 'maKQDG', 'maKQDG');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(MiniTestQuestion::class, 'maCauHoi', 'maCauHoi');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'maHV', 'maHV');
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by', 'maND');
    }

    public function isGraded(): bool
    {
        return !is_null($this->diem) && !is_null($this->graded_at);
    }
}
