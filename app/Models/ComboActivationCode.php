<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ComboActivationCode extends Model
{
    protected $table = 'ma_kich_hoat_combo';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'maHV',
        'maGoi',
        'maHD',
        'code',
        'trangThai',
        'generated_at',
        'sent_at',
        'used_at',
        'expires_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'sent_at'      => 'datetime',
        'used_at'      => 'datetime',
        'expires_at'   => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'maHV', 'maHV');
    }

    public function combo()
    {
        return $this->belongsTo(Combo::class, 'maGoi', 'maGoi');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'maHD', 'maHD');
    }

    public function isStillValid(): bool
    {
        if (!$this->expires_at) {
            return true;
        }

        return Carbon::now()->lte($this->expires_at);
    }
}
