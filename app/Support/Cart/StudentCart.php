<?php

namespace App\Support\Cart;

use App\Models\Course;
use Illuminate\Support\Collection;

class StudentCart
{
    public const SESSION_KEY = 'student_cart.ids';

    public static function ids(): array
    {
        return CartStorage::ids('course', self::SESSION_KEY);
    }

    public static function count(): int
    {
        return CartStorage::count('course', self::SESSION_KEY);
    }

    public static function has(int $courseId): bool
    {
        return CartStorage::has('course', self::SESSION_KEY, $courseId);
    }

    public static function add(int $courseId): bool
    {
        return CartStorage::add('course', self::SESSION_KEY, $courseId);
    }

    public static function remove(int $courseId): void
    {
        CartStorage::remove('course', self::SESSION_KEY, $courseId);
    }

    public static function removeMany(array $courseIds): void
    {
        CartStorage::removeMany('course', self::SESSION_KEY, $courseIds);
    }

    public static function clear(): void
    {
        CartStorage::clear('course', self::SESSION_KEY);
    }

    public static function sync(array $courseIds): void
    {
        CartStorage::sync('course', self::SESSION_KEY, $courseIds);
    }

    public static function migrateSessionToUser(): void
    {
        CartStorage::migrateSessionToUser('course', self::SESSION_KEY);
    }

    public static function courses(): Collection
    {
        $ids = self::ids();

        if (empty($ids)) {
            return collect();
        }

        $courses = Course::published()
            ->whereIn('maKH', $ids)
            ->with('teacher')
            ->get()
            ->keyBy('maKH');

        return collect($ids)
            ->map(fn (int $id) => $courses->get($id))
            ->filter();
    }
}
