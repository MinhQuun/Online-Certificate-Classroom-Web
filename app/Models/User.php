<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'nguoidung';
    protected $primaryKey = 'maND';
    protected $keyType = 'int';

    protected $fillable = [
        'hoTen',
        'email',
        'sdt',
        'matKhau',
        'chuyenMon',
        'vaiTro',
        'trangThai',
    ];

    protected $hidden = [
        'matKhau',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->matKhau;
    }

    public function getPasswordAttribute(): ?string
    {
        return $this->matKhau;
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes['matKhau'] = $value;
    }

    public function getNameAttribute(): ?string
    {
        return $this->hoTen;
    }

    public function assignRole(string $roleId): void
    {
        if (!$this->exists) {
            return;
        }

        DB::table('QUYEN_NGUOIDUNG')->updateOrInsert(
            [
                'maND' => $this->getKey(),
                'maQuyen' => $roleId,
            ],
            []
        );
    }
}
