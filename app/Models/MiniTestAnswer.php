<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MiniTestAnswer extends Model
{
    protected $table = 'MINITEST_ANSWERS';
    protected $primaryKey = 'maDA';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'maCH',
        'thuTu',
        'noiDung',
        'isDung',
    ];

    protected $casts = [
        'isDung' => 'boolean',
    ];

    /**
     * Đáp án thuộc về câu hỏi nào
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(MiniTestQuestion::class, 'maCH', 'maCH');
    }
}
