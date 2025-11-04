<?php

namespace App\Http\Controllers\API\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Trả về thông tin tài khoản và học viên đang đăng nhập.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->loadMissing('student');

        return response()->json([
            'status'  => 'success',
            'message' => 'Lấy thông tin tài khoản thành công.',
            'data'    => [
                'user'    => $this->transformUser($user),
                'student' => $this->transformStudent($user->student),
            ],
        ]);
    }

    /**
     * Cập nhật thông tin cá nhân (và mật khẩu nếu có).
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user()->loadMissing('student');

        $validated = $request->validate([
            'full_name'          => ['required', 'string', 'max:255'],
            'email'              => [
                'required',
                'email',
                'max:255',
                Rule::unique('nguoidung', 'email')->ignore($user->maND, 'maND'),
            ],
            'phone'              => ['nullable', 'string', 'max:20'],
            'date_of_birth'      => ['nullable', 'date'],
            'current_password'   => ['required_with:new_password', 'string'],
            'new_password'       => ['nullable', 'confirmed', Password::min(6)],
        ]);

        if ($request->filled('new_password') && ! Hash::check($validated['current_password'], $user->matKhau)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Mật khẩu hiện tại không đúng.',
            ], 422);
        }

        DB::transaction(function () use ($user, $validated, $request) {
            // Cập nhật bảng nguoidung
            $user->forceFill([
                'hoTen' => $validated['full_name'],
                'email' => $validated['email'],
                'sdt'   => $validated['phone'] ?? null,
            ])->save();

            if ($request->filled('new_password')) {
                $user->forceFill([
                    'matKhau' => Hash::make($request->input('new_password')),
                ])->save();
            }

            // Đồng bộ dữ liệu bảng HOCVIEN
            $user->student()->updateOrCreate(
                ['maND' => $user->maND],
                array_filter([
                    'hoTen'    => $validated['full_name'],
                    'ngaySinh' => $validated['date_of_birth'] ?? null,
                ], fn ($value) => $value !== null)
            );
        });

        $user->load('student');

        return response()->json([
            'status'  => 'success',
            'message' => 'Cập nhật thông tin thành công.',
            'data'    => [
                'user'    => $this->transformUser($user),
                'student' => $this->transformStudent($user->student),
            ],
        ]);
    }

    protected function transformUser($user): array
    {
        return [
            'id'         => $user->maND,
            'full_name'  => $user->hoTen,
            'email'      => $user->email,
            'phone'      => $user->sdt,
            'role'       => $user->vaiTro,
            'created_at' => optional($user->created_at)->toIso8601String(),
            'updated_at' => optional($user->updated_at)->toIso8601String(),
        ];
    }

    protected function transformStudent($student): ?array
    {
        if (! $student) {
            return null;
        }

        return [
            'id'          => $student->maHV,
            'user_id'     => $student->maND,
            'full_name'   => $student->hoTen,
            'date_of_birth' => optional($student->ngaySinh)->format('Y-m-d'),
            'joined_at'   => optional($student->ngayNhapHoc)->format('Y-m-d'),
        ];
    }
}
