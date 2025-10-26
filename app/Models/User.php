<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'nguoidung';
    protected $primaryKey = 'maND';
    protected $keyType = 'int';

    protected $fillable = [
        'hoTen',
        'name',
        'email',
        'sdt',
        'phone',
        'matKhau',
        'password',
        'chuyenMon',
        'vaiTro',
        'trangThai',
    ];

    protected $hidden = [
        'matKhau',
        'password',
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

    public function setNameAttribute($value): void
    {
        $this->attributes['hoTen'] = $value;
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->sdt;
    }

    public function setPhoneAttribute($value): void
    {
        $this->attributes['sdt'] = $value;
    }

    /**
     * Roles that belong to the user via QUYEN_NGUOIDUNG pivot.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'QUYEN_NGUOIDUNG',
            'maND',
            'maQuyen',
            $this->getKeyName(),
            'maQuyen'
        );
    }

    public function assignRole(string $roleId): void
    {
        if (!$this->exists) return;

        // 1) Ghi vào bảng pivot
        DB::table('QUYEN_NGUOIDUNG')->updateOrInsert(
            ['maND' => $this->getKey(), 'maQuyen' => $roleId],
            []
        );

        // 2) Map mã quyền -> ENUM của cột vaiTro
        $roleEnum = $this->mapRoleIdToEnum($roleId); // "0003" -> "GIANG_VIEN"

        if ($roleEnum !== null) {
            $this->forceFill(['vaiTro' => $roleEnum])->save();
        }
    }

    /** Map mã quyền (ID) sang giá trị ENUM của cột vaiTro */
    protected function mapRoleIdToEnum(string $roleId): ?string
    {
        // CẬP NHẬT map này theo dữ liệu bảng Role của bạn
        $map = [
            '0001' => 'ADMIN',
            '0002' => 'GIAO_VU',
            '0003' => 'GIANG_VIEN',
            '0004' => 'HOC_VIEN',
        ];
        return $map[$roleId] ?? null;
    }
}
