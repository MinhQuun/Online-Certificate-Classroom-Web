<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Certificate extends Model
{
    use HasFactory;

    protected $table = 'chungchi';
    protected $primaryKey = 'maCC';

    // Cho phép mass-assign tất cả cột (code sẽ control dữ liệu)
    protected $guarded = [];

    protected $casts = [
        'issued_at'  => 'datetime',
        'revoked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // --------- Constant cho type + status + issue mode ---------
    public const TYPE_COURSE = 'COURSE';
    public const TYPE_COMBO  = 'COMBO';

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_ISSUED  = 'ISSUED';
    public const STATUS_REVOKED = 'REVOKED';

    public const ISSUE_MODE_AUTO   = 'AUTO';
    public const ISSUE_MODE_MANUAL = 'MANUAL';

    // --------- Relations ---------

    // Học viên nhận chứng chỉ
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'maHV', 'maHV');
    }

    // Khóa học (nếu loại chứng chỉ là COURSE)
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'maKH', 'maKH');
    }

    // Combo (nếu loại chứng chỉ là COMBO)
    public function combo(): BelongsTo
    {
        return $this->belongsTo(Combo::class, 'maGoi', 'maGoi');
    }

    // Người cấp chứng chỉ (admin / hệ thống)
    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by', 'maND');
    }

    // Người thu hồi chứng chỉ
    public function revokedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoked_by', 'maND');
    }

    // Thông tin đánh giá kèm theo chứng chỉ
    public function evaluation(): HasOne
    {
        return $this->hasOne(CertificateEvaluation::class, 'maCC', 'maCC');
    }
}
