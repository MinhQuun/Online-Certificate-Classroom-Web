<?php

namespace App\Support\Cart;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartStorage
{
    public static function ids(string $type, string $sessionKey): array
    {
        if (!self::usesPersistentStore()) {
            return array_values(array_unique(array_map('intval', self::readSession($sessionKey))));
        }

        return self::query()
            ->where('user_id', Auth::id())
            ->where('item_type', $type)
            ->orderBy('id')
            ->pluck('item_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    public static function count(string $type, string $sessionKey): int
    {
        if (!self::usesPersistentStore()) {
            return count(self::readSession($sessionKey));
        }

        return (int) self::query()
            ->where('user_id', Auth::id())
            ->where('item_type', $type)
            ->count();
    }

    public static function has(string $type, string $sessionKey, int $itemId): bool
    {
        if (!self::usesPersistentStore()) {
            return in_array($itemId, self::readSession($sessionKey), true);
        }

        return self::query()
            ->where('user_id', Auth::id())
            ->where('item_type', $type)
            ->where('item_id', $itemId)
            ->exists();
    }

    public static function add(string $type, string $sessionKey, int $itemId): bool
    {
        if (!self::usesPersistentStore()) {
            $ids = self::readSession($sessionKey);
            if (!in_array($itemId, $ids, true)) {
                $ids[] = $itemId;
                self::writeSession($sessionKey, array_values($ids));

                return true;
            }

            return false;
        }

        $inserted = self::query()
            ->where('user_id', Auth::id())
            ->where('item_type', $type)
            ->where('item_id', $itemId)
            ->doesntExist();

        if ($inserted) {
            self::query()->insert([
                'user_id'    => Auth::id(),
                'item_type'  => $type,
                'item_id'    => $itemId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return true;
        }

        return false;
    }

    public static function remove(string $type, string $sessionKey, int $itemId): void
    {
        if (!self::usesPersistentStore()) {
            $ids = array_filter(self::readSession($sessionKey), fn (int $id) => $id !== $itemId);
            self::writeSession($sessionKey, array_values($ids));

            return;
        }

        self::query()
            ->where('user_id', Auth::id())
            ->where('item_type', $type)
            ->where('item_id', $itemId)
            ->delete();
    }

    public static function removeMany(string $type, string $sessionKey, array $itemIds): void
    {
        if (empty($itemIds)) {
            return;
        }

        $itemIds = array_map('intval', $itemIds);

        if (!self::usesPersistentStore()) {
            $removeSet = array_flip($itemIds);
            $ids = array_filter(
                self::readSession($sessionKey),
                fn (int $id) => !array_key_exists($id, $removeSet)
            );

            self::writeSession($sessionKey, array_values($ids));

            return;
        }

        self::query()
            ->where('user_id', Auth::id())
            ->where('item_type', $type)
            ->whereIn('item_id', $itemIds)
            ->delete();
    }

    public static function clear(string $type, string $sessionKey): void
    {
        if (!self::usesPersistentStore()) {
            self::forgetSession($sessionKey);
            return;
        }

        self::query()
            ->where('user_id', Auth::id())
            ->where('item_type', $type)
            ->delete();
    }

    public static function sync(string $type, string $sessionKey, array $itemIds): void
    {
        $itemIds = array_values(array_unique(array_map('intval', $itemIds)));

        if (!self::usesPersistentStore()) {
            self::writeSession($sessionKey, $itemIds);
            return;
        }

        $userId = Auth::id();

        DB::transaction(function () use ($type, $itemIds, $userId) {
            self::query()
                ->where('user_id', $userId)
                ->where('item_type', $type)
                ->whereNotIn('item_id', $itemIds)
                ->delete();

            if (empty($itemIds)) {
                return;
            }

            $existing = self::query()
                ->select('item_id')
                ->where('user_id', $userId)
                ->where('item_type', $type)
                ->whereIn('item_id', $itemIds)
                ->pluck('item_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $missing = array_diff($itemIds, $existing);

            if (empty($missing)) {
                return;
            }

            $now = now();
            $rows = array_map(fn (int $id) => [
                'user_id'    => $userId,
                'item_type'  => $type,
                'item_id'    => $id,
                'created_at' => $now,
                'updated_at' => $now,
            ], $missing);

            self::query()->insert($rows);
        });
    }

    public static function migrateSessionToUser(string $type, string $sessionKey): void
    {
        if (!self::usesPersistentStore()) {
            return;
        }

        $sessionIds = array_values(array_unique(array_map('intval', self::readSession($sessionKey))));

        if (empty($sessionIds)) {
            return;
        }

        $userId = Auth::id();
        $now = now();

        DB::transaction(function () use ($type, $sessionIds, $userId, $now) {
            $existing = self::query()
                ->where('user_id', $userId)
                ->where('item_type', $type)
                ->whereIn('item_id', $sessionIds)
                ->pluck('item_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $missing = array_diff($sessionIds, $existing);

            if (empty($missing)) {
                return;
            }

            $rows = array_map(fn (int $id) => [
                'user_id'    => $userId,
                'item_type'  => $type,
                'item_id'    => $id,
                'created_at' => $now,
                'updated_at' => $now,
            ], $missing);

            self::query()->insert($rows);
        });

        self::forgetSession($sessionKey);
    }

    protected static function usesPersistentStore(): bool
    {
        return Auth::check();
    }

    protected static function query(): Builder
    {
        return DB::table('student_cart_items');
    }

    protected static function readSession(string $key): array
    {
        try {
            return session($key, []);
        } catch (\RuntimeException $exception) {
            return [];
        }
    }

    protected static function writeSession(string $key, array $value): void
    {
        try {
            session()->put($key, $value);
        } catch (\RuntimeException $exception) {
            // Session store not available (CLI/API stateless)
        }
    }

    protected static function forgetSession(string $key): void
    {
        try {
            session()->forget($key);
        } catch (\RuntimeException $exception) {
            // Ignore when session store missing
        }
    }
}
