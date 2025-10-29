<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MiniTestQuestion extends Model
{
    protected $table = 'MINITEST_QUESTIONS';
    protected $primaryKey = 'maCauHoi';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'maMT',
        'thuTu',
        'loai',
        'noiDungCauHoi',
        'phuongAnA',
        'phuongAnB',
        'phuongAnC',
        'phuongAnD',
        'dapAnDung',
        'giaiThich',
        'diem',
        'audio_url',
        'image_url',
        'pdf_url',
    ];

    protected $casts = [
        'diem' => 'decimal:2',
    ];

    /**
     * Câu hỏi thuộc về mini-test nào
     */
    public function miniTest(): BelongsTo
    {
        return $this->belongsTo(MiniTest::class, 'maMT', 'maMT');
    }

    /**
     * Câu trả lời của học viên cho câu hỏi này
     */
    public function studentAnswers(): HasMany
    {
        return $this->hasMany(MiniTestStudentAnswer::class, 'maCauHoi', 'maCauHoi');
    }

    /**
     * Kiểm tra xem câu hỏi có phải là essay (tự luận) không
     */
    public function isEssay(): bool
    {
        return $this->loai === 'essay';
    }

    /**
     * Kiểm tra đáp án có đúng không
     */
    public function checkAnswer(string $answer): bool
    {
        if ($this->isEssay()) {
            return false; // Essay không tự động chấm
        }

        $correctAnswers = explode(';', $this->dapAnDung ?? '');
        $studentAnswers = explode(';', $answer);

        sort($correctAnswers);
        sort($studentAnswers);

        return $correctAnswers === $studentAnswers;
    }
}
