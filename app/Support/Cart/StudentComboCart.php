<?php

namespace App\Support\Cart;

use App\Models\Combo;
use Illuminate\Support\Collection;

class StudentComboCart
{
    public const SESSION_KEY = 'student_cart.combo_ids';

    public static function ids(): array
    {
        return CartStorage::ids('combo', self::SESSION_KEY);
    }

    public static function count(): int
    {
        return CartStorage::count('combo', self::SESSION_KEY);
    }

    public static function has(int $comboId): bool
    {
        return CartStorage::has('combo', self::SESSION_KEY, $comboId);
    }

    public static function add(int $comboId): bool
    {
        return CartStorage::add('combo', self::SESSION_KEY, $comboId);
    }

    public static function remove(int $comboId): void
    {
        CartStorage::remove('combo', self::SESSION_KEY, $comboId);
    }

    public static function removeMany(array $comboIds): void
    {
        CartStorage::removeMany('combo', self::SESSION_KEY, $comboIds);
    }

    public static function clear(): void
    {
        CartStorage::clear('combo', self::SESSION_KEY);
    }

    public static function sync(array $comboIds): void
    {
        CartStorage::sync('combo', self::SESSION_KEY, $comboIds);
    }

    public static function migrateSessionToUser(): void
    {
        CartStorage::migrateSessionToUser('combo', self::SESSION_KEY);
    }

    public static function combos(): Collection
    {
        $ids = self::ids();

        if (empty($ids)) {
            return collect();
        }

        $combos = Combo::with([
                'courses' => fn ($query) => $query->with('teacher'),
                'promotions',
            ])
            ->available()
            ->whereIn('maGoi', $ids)
            ->get()
            ->keyBy('maGoi');

        $validIds = [];

        $ordered = collect($ids)
            ->map(function (int $id) use ($combos, &$validIds) {
                $combo = $combos->get($id);

                if ($combo) {
                    $validIds[] = $id;
                }

                return $combo;
            })
            ->filter();

        if ($validIds !== $ids) {
            self::sync($validIds);
        }

        return $ordered->values();
    }
}
