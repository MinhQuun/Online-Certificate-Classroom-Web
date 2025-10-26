<?php

namespace App\Support\Cart;

use App\Models\Course;
use Illuminate\Support\Collection;

class StudentCart
{
    public const SESSION_KEY = 'student_cart.ids';

    public static function ids(): array
    {
        $ids = session(self::SESSION_KEY, []);

        return array_values(array_unique(array_map('intval', $ids)));
    }

    public static function count(): int
    {
        return count(self::ids());
    }

    public static function has(int $courseId): bool
    {
        return in_array($courseId, self::ids(), true);
    }

    public static function add(int $courseId): bool
    {
        $ids = self::ids();

        if (!in_array($courseId, $ids, true)) {
            $ids[] = $courseId;
            session()->put(self::SESSION_KEY, $ids);

            return true;
        }

        return false;
    }

    public static function remove(int $courseId): void
    {
        $ids = array_filter(self::ids(), fn (int $id) => $id !== $courseId);
        session()->put(self::SESSION_KEY, array_values($ids));
    }

    public static function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public static function sync(array $courseIds): void
    {
        $normalized = array_values(array_unique(array_filter(array_map('intval', $courseIds))));
        session()->put(self::SESSION_KEY, $normalized);
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
