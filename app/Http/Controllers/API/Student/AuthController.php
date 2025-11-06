<?php

namespace App\Http\Controllers\API\Student;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EnsureCustomerProfile;
use App\Support\RoleResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'       => ['required', 'email'],
            'password'    => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:120'],
        ]);

        /** @var User|null $user */
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! $this->validateCredentials($user, $credentials['password'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Thông tin đăng nhập không đúng.',
            ], 401);
        }

        if (! $this->isStudent($user)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Chỉ tài khoản học viên mới được phép đăng nhập ứng dụng.',
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

    public function loginWithGoogle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_token'    => ['required_without:code', 'string'],
            'code'        => ['required_without:id_token', 'string'],
            'device_name' => ['nullable', 'string', 'max:120'],
        ]);

        try {
            $driver = Socialite::driver('google')->stateless();

            /** @var SocialiteUser $googleUser */
            $googleUser = $request->filled('id_token')
                ? $driver->userFromToken($validated['id_token'])
                : $driver->userFromCode($validated['code']);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status'  => 'error',
                'message' => 'Không thể xác thực Google, vui lòng thử lại.',
            ], 422);
        }

        $email = $googleUser->getEmail();
        $googleId = $googleUser->getId();

        if (! $email || ! $googleId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tài khoản Google không cung cấp đủ thông tin.',
            ], 422);
        }

        $user = $this->findOrCreateGoogleStudent($googleUser);

        if (! $this->isStudent($user)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tài khoản này không thuộc quyền học viên.',
            ], 403);
        }

        app(EnsureCustomerProfile::class)->handle($user);

        $tokenName = $validated['device_name'] ?? 'student_mobile_google';
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

    private function validateCredentials(User $user, string $plainPassword): bool
    {
        $stored = (string) ($user->matKhau ?? $user->password ?? '');

        if ($stored === '') {
            return false;
        }

        $verified = false;

        try {
            if (Hash::check($plainPassword, $stored)) {
                $verified = true;

                if (Hash::needsRehash($stored)) {
                    $user->matKhau = Hash::make($plainPassword);
                    $user->save();
                }
            }
        } catch (Throwable $e) {
            // Ignore invalid hash formats.
        }

        if (! $verified) {
            $legacyMatches = $plainPassword === $stored
                || hash('md5', $plainPassword) === $stored;

            if ($legacyMatches) {
                $user->matKhau = Hash::make($plainPassword);
                $user->save();
                $verified = true;
            }
        }

        return $verified;
    }

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
            'avatar'     => $user->avatar,
        ];
    }

    private function findOrCreateGoogleStudent(SocialiteUser $googleUser): User
    {
        $email = $googleUser->getEmail();
        $googleId = $googleUser->getId();
        $avatar = $googleUser->getAvatar();
        $name = $googleUser->getName() ?: $email;

        $existing = User::where('google_id', $googleId)
            ->orWhere('email', $email)
            ->first();

        return DB::transaction(function () use ($existing, $googleId, $email, $avatar, $name) {
            if ($existing) {
                $existing->forceFill([
                    'google_id'        => $googleId,
                    'email'            => $email,
                    'hoTen'            => $existing->hoTen ?: $name,
                    'avatar'           => $avatar ?? $existing->avatar,
                    'email_verified_at'=> $existing->email_verified_at ?: now(),
                    'trangThai'        => $existing->trangThai ?: 'ACTIVE',
                ])->save();

                return $existing;
            }

            $user = new User([
                'hoTen'            => $name,
                'email'            => $email,
                'google_id'        => $googleId,
                'avatar'           => $avatar,
                'matKhau'          => Hash::make(Str::random(40)),
                'vaiTro'           => 'HOC_VIEN',
                'trangThai'        => 'ACTIVE',
                'email_verified_at'=> now(),
            ]);
            $user->save();

            if ($roleId = RoleResolver::findRoleId(['student'])) {
                $user->assignRole($roleId);
            }

            return $user;
        });
    }
}
