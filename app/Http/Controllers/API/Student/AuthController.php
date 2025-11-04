<?php

namespace App\Http\Controllers\API\Student;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Xác thực tài khoản học viên và trả về API token.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'       => ['required', 'email'],
            'password'    => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:120'],
        ]);

        /** @var User|null $user */
        $user = User::where('email', $credentials['email'])->first();

        // Không tồn tại user hoặc sai mật khẩu
        if (! $user || ! $this->validateCredentials($user, $credentials['password'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Thông tin đăng nhập không đúng.',
            ], 401);
        }

        // Chỉ cho phép Học viên dùng mobile
        if (! $this->isStudent($user)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Chỉ tài khoản học viên mới được truy cập ứng dụng mobile.',
            ], 403);
        }

        $tokenName = $credentials['device_name'] ?? 'student_mobile';
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Đăng nhập thành công.',
            'data'    => [
                'token_type'   => 'Bearer',
                'access_token' => $token,
                'user'         => $this->formatUser($user),
            ],
        ]);
    }


    /**
     * Hủy token hiện tại của học viên.
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()?->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Đăng xuất thành công.',
        ]);
    }

    /**
     * Kiểm tra mật khẩu, hỗ trợ cả hash cũ (plain/md5) giống bên UserController.
     */
    private function validateCredentials(User $user, string $plainPassword): bool
    {
        $stored = (string) ($user->matKhau ?? $user->password ?? '');

        if ($stored === '') {
            return false;
        }

        $verified = false;

        // 1) Thử check như bcrypt/argon bình thường
        try {
            if (Hash::check($plainPassword, $stored)) {
                $verified = true;

                // Nếu hash cũ → rehash lại cho chuẩn
                if (Hash::needsRehash($stored)) {
                    $user->matKhau = Hash::make($plainPassword);
                    $user->save();
                }
            }
        } catch (\RuntimeException $e) {
            // Hash không tương thích (không phải bcrypt/argon)
        }

        // 2) Nếu chưa verify được → check kiểu legacy: plain hoặc md5
        if (! $verified) {
            $legacyMatches = $plainPassword === $stored
                || hash('md5', $plainPassword) === $stored;

            if ($legacyMatches) {
                // Nâng cấp lên bcrypt luôn
                $user->matKhau = Hash::make($plainPassword);
                $user->save();
                $verified = true;
            }
        }

        return $verified;
    }

    /**
     * Kiểm tra user có phải Học viên không.
     */
    private function isStudent(User $user): bool
    {
        return in_array($user->vaiTro, ['HOC_VIEN', 'STUDENT', 'student'], true);
    }

    protected function formatUser(User $user): array
    {
        return [
            'id'         => $user->maND,
            'student_id' => optional($user->student)->maHV,
            'full_name'  => $user->hoTen,
            'email'      => $user->email,
            'phone'      => $user->sdt,
            'role'       => $user->vaiTro,
        ];
    }
}
