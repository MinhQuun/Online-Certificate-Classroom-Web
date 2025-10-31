<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MiniTestQuestion extends Model
{
    public const TYPE_SINGLE_CHOICE   = 'single_choice';
    public const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    public const TYPE_TRUE_FALSE      = 'true_false';
    public const TYPE_ESSAY           = 'essay';

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

    public function miniTest(): BelongsTo
    {
        return $this->belongsTo(MiniTest::class, 'maMT', 'maMT');
    }

    public function studentAnswers(): HasMany
    {
        return $this->hasMany(MiniTestStudentAnswer::class, 'maCauHoi', 'maCauHoi');
    }

    public function isEssay(): bool
    {
        return $this->loai === self::TYPE_ESSAY;
    }

    public function isChoice(): bool
    {
        return in_array($this->loai, [
            self::TYPE_SINGLE_CHOICE,
            self::TYPE_MULTIPLE_CHOICE,
            self::TYPE_TRUE_FALSE,
        ], true);
    }

    public function allowsMultipleSelections(): bool
    {
        return $this->loai === self::TYPE_MULTIPLE_CHOICE;
    }

    public function correctAnswers(): array
    {
        $raw = trim((string) $this->dapAnDung);

        if ($raw === '') {
            return [];
        }

        if ($this->allowsMultipleSelections()) {
            return array_values(array_filter(
                array_map('trim', explode(';', $raw)),
                fn ($value) => $value !== ''
            ));
        }

        if ($this->loai === self::TYPE_TRUE_FALSE) {
            return [strtoupper($raw)];
        }

        $parts = array_values(array_filter(
            array_map('trim', explode(';', $raw)),
            fn ($value) => $value !== ''
        ));

        return [$parts[0] ?? $raw];
    }

    /**
     * Kiểm tra đáp án trắc nghiệm.
     *
     * @param  string|array|null  $answer
     */
    public function checkAnswer(string|array|null $answer): bool
    {
        if (!$this->isChoice()) {
            return false;
        }

        $expected = $this->correctAnswers();

        if ($this->allowsMultipleSelections()) {
            $given = is_array($answer)
                ? $answer
                : array_map('trim', explode(';', (string) $answer));

            $given = array_values(array_filter($given, fn ($value) => $value !== ''));

            sort($expected);
            sort($given);

            return $expected === $given;
        }

        $value = is_array($answer) ? ($answer[0] ?? null) : $answer;

        if ($this->loai === self::TYPE_TRUE_FALSE) {
            $value = strtoupper((string) $value);
        }

        return (string) ($expected[0] ?? '') === (string) $value;
    }
}
