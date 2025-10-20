<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\EnsureCustomerProfile;
use App\Support\RoleResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'min:2', 'max:255'],
            'email'    => ['required', 'email:rfc,dns', 'max:255', 'unique:nguoidung,email'],
            'password' => ['required', 'confirmed', Password::min(6)],
            'phone'    => ['nullable', 'regex:/^0\d{9}$/'],
        ], [
            'name.required'     => 'Vui lòng nhập họ tên.',
            'name.min'          => 'Họ tên phải có ít nhất :min ký tự.',
            'email.required'    => 'Vui lòng nhập email.',
            'email.email'       => 'Email không hợp lệ.',
            'email.unique'      => 'Email đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.confirmed'=> 'Xác nhận mật khẩu không khớp.',
            'password.min'      => 'Mật khẩu phải có ít nhất :min ký tự.',
            'phone.regex'       => 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            /** @var User $user */
            $user = User::create([
                'hoTen'     => $validated['name'],
                'email'     => $validated['email'],
                'sdt'       => $validated['phone'] ?? null,
                'matKhau'   => Hash::make($validated['password']),
                'vaiTro'    => 'HOC_VIEN',
                'trangThai' => 'ACTIVE',
            ]);

            if ($roleId = RoleResolver::findRoleId(['student'])) {
                $user->assignRole($roleId);
            }

            app(EnsureCustomerProfile::class)->handle($user);

            Auth::login($user);
            $request->session()->regenerate();

            $target = $this->sanitizeRedirect($request->input('redirect'))
                ?? route('student.courses.index');

            return redirect()
                ->to($target)
                ->with('success', 'Đăng ký thành công!');
        });
    }

    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            $role = RoleResolver::resolve($user);

            if ($role === 'admin' && Route::has('admin.dashboard')) {
                return redirect()->route('admin.dashboard');
            }

            if ($role === 'teacher' && Route::has('teacher.dashboard')) {
                return redirect()->route('teacher.dashboard');
            }

            return redirect()->route('student.courses.index');
        }

        return view('auth.login', [
            'redirect' => $request->query('redirect'),
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'Vui lòng nhập email.',
            'email.email'       => 'Email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        /** @var User|null $user */
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !$this->validateCredentials($user, $credentials['password'])) {
            return back()
                ->withErrors(['email' => 'Email hoặc mật khẩu không đúng.'])
                ->with('error', 'Email hoặc mật khẩu không đúng.')
                ->onlyInput('email');
        }

        Auth::login($user);
        $request->session()->regenerate();

        $role = RoleResolver::resolve($user);

        if ($role === 'student') {
            app(EnsureCustomerProfile::class)->handle($user);
        }

        $redirect = $this->sanitizeRedirect(
            $request->input('redirect', $request->query('redirect'))
        );

        if ($role === 'student' && $redirect) {
            return redirect()->to($redirect)->with('success', 'Đăng nhập thành công!');
        }

        if ($role === 'admin' && Route::has('admin.dashboard')) {
            return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công!');
        }

        if ($role === 'teacher' && Route::has('teacher.dashboard')) {
            return redirect()->route('teacher.dashboard')->with('success', 'Đăng nhập thành công!');
        }

        return redirect()
            ->intended(route('student.courses.index'))
            ->with('success', 'Đăng nhập thành công!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('student.courses.index')
            ->with('info', 'Bạn đã đăng xuất.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email:rfc,dns', 'max:255', 'unique:nguoidung,email,' . $user->getKey() . ',maND'],
            'phone' => ['nullable', 'regex:/^0\d{9}$/'],
        ], [
            'name.required'  => 'Vui lòng nhập họ tên.',
            'name.min'       => 'Họ tên phải có ít nhất :min ký tự.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email'    => 'Email không hợp lệ.',
            'email.unique'   => 'Email đã được sử dụng.',
            'phone.regex'    => 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.',
        ]);

        $data = [
            'hoTen' => $validated['name'],
            'email' => $validated['email'],
            'sdt'   => $validated['phone'],
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Password::min(6)],
            ], [
                'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
                'password.min'       => 'Mật khẩu phải có ít nhất :min ký tự.',
            ]);

            $data['matKhau'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($user->wasChanged('hoTen')) {
            app(EnsureCustomerProfile::class)->handle($user);
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'Cập nhật người dùng thành công!');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Đã xoá người dùng!');
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
            $targetHost  = parse_url($target, PHP_URL_HOST);
            $appHost     = parse_url(config('app.url'), PHP_URL_HOST);
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

        return Str::startsWith($target, '/') ? $target : '/' . ltrim($target, '/');
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
        } catch (\RuntimeException $e) {
            // Hash không tương thích (không phải bcrypt/argon)
        }

        if (!$verified) {
            $legacyMatches = $plainPassword === $stored || hash('md5', $plainPassword) === $stored;

            if ($legacyMatches) {
                $user->matKhau = Hash::make($plainPassword);
                $user->save();
                $verified = true;
            }
        }

        return $verified;
    }
}

