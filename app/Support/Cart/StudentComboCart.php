<?php

namespace App\Support\Cart;

use App\Models\Combo;
use Illuminate\Support\Collection;

class StudentComboCart
{
    public const SESSION_KEY = 'student_cart.combo_ids';

    public static function ids(): array
    {
        $ids = session(self::SESSION_KEY, []);

        return array_values(array_unique(array_map('intval', $ids)));
    }

    public static function count(): int
    {
        return count(self::ids());
    }

    public static function has(int $comboId): bool
    {
        return in_array($comboId, self::ids(), true);
    }

    public static function add(int $comboId): bool
    {
        $comboId = (int) $comboId;
        $ids = self::ids();

        if (!in_array($comboId, $ids, true)) {
            $ids[] = $comboId;
            session()->put(self::SESSION_KEY, $ids);

            return true;
        }

        return false;
    }

    public static function remove(int $comboId): void
    {
        $comboId = (int) $comboId;
        $ids = array_filter(self::ids(), fn (int $id) => $id !== $comboId);
        session()->put(self::SESSION_KEY, array_values($ids));
    }

    public static function removeMany(array $comboIds): void
    {
        if (empty($comboIds)) {
            return;
        }

        $comboIds = array_map('intval', $comboIds);
        $removeSet = array_flip($comboIds);

        $ids = array_filter(
            self::ids(),
            fn (int $id) => !array_key_exists($id, $removeSet)
        );

        session()->put(self::SESSION_KEY, array_values($ids));
    }

    public static function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public static function sync(array $comboIds): void
    {
        $normalized = array_values(array_unique(array_filter(array_map('intval', $comboIds))));
        session()->put(self::SESSION_KEY, $normalized);
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

