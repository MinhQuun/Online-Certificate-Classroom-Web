<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateTemplate extends Model
{
    use HasFactory;

    protected $table = 'certificate_template';
    protected $primaryKey = 'maTemplate';

    protected $guarded = [];

    protected $casts = [
        'design_json' => 'array',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    public const STATUS_DRAFT    = 'DRAFT';
    public const STATUS_ACTIVE   = 'ACTIVE';
    public const STATUS_ARCHIVED = 'ARCHIVED';

    // Khóa học áp dụng template này
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'maKH', 'maKH');
    }

    // Admin tạo template
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'maND');
    }
}
