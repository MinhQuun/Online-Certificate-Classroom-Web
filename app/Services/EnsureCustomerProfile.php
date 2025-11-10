<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EnsureCustomerProfile
{
    /**
     * Ensure that the current user has a linked record inside hocvien.
     */
    public function handle(User $user): void
    {
        if (!$user->exists) {
            return;
        }

        $now = Carbon::now();

        $profile = DB::table('hocvien')
            ->where('maND', $user->getKey())
            ->first();

        if ($profile) {
            // Update cached name in student profile when user renamed in registration form.
            DB::table('hocvien')
                ->where('maND', $user->getKey())
                ->update([
                    'hoTen' => $user->hoTen,
                    'updated_at' => $now,
                ]);

            return;
        }

        DB::table('hocvien')->insert([
            'maND' => $user->getKey(),
            'hoTen' => $user->hoTen,
            'ngayNhapHoc' => $now->toDateString(),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}