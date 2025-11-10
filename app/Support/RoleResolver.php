<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleResolver
{
    /**
     * Resolve the friendly slug for the user's primary role.
     */
    public static function resolve(User $user): string
    {
        $role = DB::table('quyen_nguoidung')
            ->join('quyen', 'quyen.maQuyen', '=', 'quyen_nguoidung.maQuyen')
            ->select('quyen_nguoidung.maQuyen as code', 'quyen.tenQuyen as name')
            ->where('quyen_nguoidung.maND', $user->getKey())
            ->first();

        if ($role) {
            if ($slug = self::map($role->code, $role->name)) {
                return $slug;
            }
        }

        return self::map(null, $user->vaiTro) ?? 'student';
    }

    /**
     * Determine whether the given user should be treated as an admin.
     */
    public static function isAdmin(User $user): bool
    {
        return self::resolve($user) === 'admin';
    }

    /**
     * Try to find the role id that matches one of the preferred slug candidates.
     */
    public static function findRoleId(array $preferredSlugs): ?string
    {
        if (empty($preferredSlugs)) {
            return null;
        }

        $normalized = collect($preferredSlugs)
            ->filter()
            ->map(fn ($slug) => Str::slug($slug))
            ->unique()
            ->values();

        if ($normalized->isEmpty()) {
            return null;
        }

        $roles = DB::table('quyen')->get();

        foreach ($roles as $role) {
            [$code, $name] = self::extractRoleFields($role);

            if (!$code && !$name) {
                continue;
            }

            $slug = self::map($code, $name);
            if ($code && $slug && $normalized->contains($slug)) {
                return $code;
            }
        }

        return null;
    }

    /**
     * Convert raw role identifiers into a normalized slug.
     */
    public static function map(?string $code, ?string $name): ?string
    {
        $code = $code ? strtoupper($code) : null;

        $mapByCode = [
            'Q001' => 'admin',
            'Q002' => 'teacher',
            'Q003' => 'student',
        ];

        if ($code && isset($mapByCode[$code])) {
            return $mapByCode[$code];
        }

        if (!$name) {
            return null;
        }

        $slug = Str::slug($name);

        return match ($slug) {
            'admin', 'quan-tri-vien', 'giao-vu', 'nhan-vien', 'staff' => 'admin',
            'giang-vien', 'teacher' => 'teacher',
            'hoc-vien', 'hocvien', 'hoc-sinh', 'student', 'khach-hang', 'khachhang' => 'student',
            default => null,
        };
    }

    /**
     * Safely extract maQuyen/tenQuyen regardless of database column casing.
     *
     * @return array{0: ?string, 1: ?string}
     */
    private static function extractRoleFields(object $role): array
    {
        $attributes = array_change_key_case((array) $role, CASE_LOWER);

        $code = $attributes['maQUYEN'] ?? null;
        $name = $attributes['tenQUYEN'] ?? null;

        return [
            $code !== null ? (string) $code : null,
            $name !== null ? (string) $name : null,
        ];
    }
}
