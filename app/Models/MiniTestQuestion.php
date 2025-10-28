<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MiniTestQuestion extends Model
{
    protected $table = 'MINITEST_QUESTIONS';
    protected $primaryKey = 'maCH';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'maMT',
        'thuTu',
        'noiDung',
        'image_url',
        'audio_url',
        'diem',
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
     * Các đáp án của câu hỏi
     */
    public function answers(): HasMany
    {
        return $this->hasMany(MiniTestAnswer::class, 'maCH', 'maCH')->orderBy('thuTu');
    }

    /**
     * Lấy đáp án đúng
     */
    public function correctAnswer(): HasMany
    {
        return $this->hasMany(MiniTestAnswer::class, 'maCH', 'maCH')
            ->where('isDung', true);
    }
}
