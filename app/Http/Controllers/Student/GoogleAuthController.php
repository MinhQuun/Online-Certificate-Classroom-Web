<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EnsureCustomerProfile;
use App\Support\RoleResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    private const REDIRECT_SESSION_KEY = 'auth.oauth.redirect';

    public function redirect(Request $request): RedirectResponse
    {
        $redirect = $this->sanitizeRedirect($request->query('redirect'));

        if ($redirect) {
            $request->session()->put(self::REDIRECT_SESSION_KEY, $redirect);
        } else {
            $request->session()->forget(self::REDIRECT_SESSION_KEY);
        }

        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            report($e);

            return $this->sendFailureResponse('Không thể đăng nhập bằng Google, vui lòng thử lại.');
        }

        $email = $googleUser->getEmail();
        $googleId = $googleUser->getId();

        if (!$email) {
            return $this->sendFailureResponse('Tài khoản Google không cung cấp email, vui lòng dùng tài khoản khác.');
        }

        $user = User::where('google_id', $googleId)
            ->orWhere('email', $email)
            ->first();

        DB::beginTransaction();
        try {
            if ($user) {
                $user->forceFill([
                    'google_id' => $googleId,
                    'email' => $email,
                    'hoTen' => $user->hoTen ?: ($googleUser->getName() ?: $email),
                    'avatar' => $googleUser->getAvatar() ?: $user->avatar,
                    'email_verified_at' => $user->email_verified_at ?: now(),
                    'trangThai' => $user->trangThai ?: 'ACTIVE',
                ])->save();
            } else {
                $user = new User([
                    'hoTen' => $googleUser->getName() ?: $email,
                    'email' => $email,
                    'google_id' => $googleId,
                    'avatar' => $googleUser->getAvatar(),
                    'matKhau' => Hash::make(Str::password(40)),
                    'vaiTro' => 'HOC_VIEN',
                    'trangThai' => 'ACTIVE',
                    'email_verified_at' => now(),
                ]);
                $user->save();

                if ($roleId = RoleResolver::findRoleId(['student'])) {
                    $user->assignRole($roleId);
                }
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            return $this->sendFailureResponse('Không thể đăng nhập bằng Google, vui lòng thử lại sau.');
        }

        Auth::login($user, true);

        $role = RoleResolver::resolve($user);
        if ($role === 'student') {
            app(EnsureCustomerProfile::class)->handle($user);
        }

        $redirect = $this->sanitizeRedirect(
            $request->session()->pull(self::REDIRECT_SESSION_KEY)
                ?? $request->query('redirect')
        );

        if ($role === 'admin' && Route::has('admin.dashboard')) {
            return redirect()->route('admin.dashboard')->with([
                'success_title' => 'Đăng nhập thành công!',
                'success' => 'Xin chào quản trị viên, bạn đã đăng nhập bằng Google.',
            ]);
        }

        if ($role === 'teacher' && Route::has('teacher.dashboard')) {
            return redirect()->route('teacher.dashboard')->with([
                'success_title' => 'Đăng nhập thành công!',
                'success' => 'Chào mừng giảng viên quay lại hệ thống.',
            ]);
        }

        if ($role === 'student' && $redirect) {
            return redirect()->to($redirect)->with([
                'success_title' => 'Đăng nhập thành công!',
                'success' => 'Bạn đã đăng nhập bằng Google.',
            ]);
        }

        return redirect()->route('student.courses.index')->with([
            'success_title' => 'Đăng nhập thành công!',
            'success' => 'Bạn đã đăng nhập bằng Google.',
        ]);
    }

    private function sendFailureResponse(string $message): RedirectResponse
    {
        return redirect()->route('student.courses.index')
            ->with('error', $message);
    }

    private function sanitizeRedirect(?string $target): ?string
    {
        if (!$target) {
            return null;
        }

        $target = trim($target);

        if ($target === '') {
            return null;
        }

        if (Str::startsWith($target, ['http://', 'https://'])) {
            $targetHost = parse_url($target, PHP_URL_HOST);
            $appHost = parse_url(config('app.url'), PHP_URL_HOST);
            $currentHost = request()->getHost();
            $allowedHosts = array_filter([$appHost, $currentHost]);

            if ($targetHost && in_array($targetHost, $allowedHosts, true)) {
                return $target;
            }

            return null;
        }

        if (Str::startsWith($target, '//')) {
            return null;
        }

        return Str::startsWith($target, '/')
            ? $target
            : '/' . ltrim($target, '/');
    }
}
