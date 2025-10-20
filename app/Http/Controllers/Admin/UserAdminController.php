<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EnsureCustomerProfile;
use App\Support\RoleResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class UserAdminController extends Controller
{
    private function roleId(string $slug): ?string
    {
        return RoleResolver::findRoleId([$slug]);
    }

    private function normalizeRoleSlug(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if ($mapped = RoleResolver::map(Str::upper($value), null)) {
            return $mapped;
        }

        $slug = Str::slug($value);

        return match ($slug) {
            'admin', 'quan-tri-vien' => 'admin',
            'giang-vien', 'giangvien', 'giao-vien', 'teacher' => 'teacher',
            'hoc-vien', 'hocvien', 'hoc-sinh', 'student' => 'student',
            default => $slug,
        };
    }

    private function adminCount(): int
    {
        $adminId = $this->roleId('admin');

        if (!$adminId) {
            return 0;
        }

        return (int) DB::table('QUYEN_NGUOIDUNG')
            ->where('maQuyen', $adminId)
            ->count();
    }

    public function index(Request $request)
    {
        $q               = trim((string) $request->query('q', ''));
        $roleFilterInput = (string) $request->query('role', '');
        $roleFilter      = $this->normalizeRoleSlug($roleFilterInput);
        $roleFilterCode  = $roleFilter ? $this->roleId($roleFilter) : null;

        $roles = DB::table('QUYEN')
            ->selectRaw('maQuyen as MAQUYEN, tenQuyen as TENQUYEN')
            ->orderBy('maQuyen')
            ->get();

        $users = User::query()
            ->when($q !== '', function ($builder) use ($q) {
                $builder->where(function ($query) use ($q) {
                    $query->where('hoTen', 'like', "%$q%")
                        ->orWhere('email', 'like', "%$q%")
                        ->orWhere('sdt', 'like', "%$q%");
                });
            })
            ->when($roleFilterCode, function ($builder) use ($roleFilterCode) {
                $builder->whereHas('roles', function ($roleQuery) use ($roleFilterCode) {
                    $roleQuery->where('QUYEN.maQuyen', $roleFilterCode);
                });
            })
            ->with('roles')
            ->paginate(10)
            ->withQueryString();

        $adminId   = $this->roleId('admin');
        $teacherId = $this->roleId('teacher');
        $studentId = $this->roleId('student');

        $counts = [
            'total'   => (int) User::count(),
            'admin'   => $adminId ? (int) DB::table('QUYEN_NGUOIDUNG')->where('maQuyen', $adminId)->count() : 0,
            'teacher' => $teacherId ? (int) DB::table('QUYEN_NGUOIDUNG')->where('maQuyen', $teacherId)->count() : 0,
            'student' => $studentId ? (int) DB::table('QUYEN_NGUOIDUNG')->where('maQuyen', $studentId)->count() : 0,
        ];

        return view('admin.index', compact(
            'users',
            'roles',
            'q',
            'roleFilter',
            'roleFilterCode',
            'adminId',
            'teacherId',
            'studentId',
            'counts'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'min:2', 'max:255'],
            'email'    => ['required', 'email:rfc,dns', 'max:255', 'unique:nguoidung,email'],
            'password' => ['required', 'confirmed', Password::min(6)],
            'phone'    => ['nullable', 'regex:/^0\d{9}$/'],
            'MAQUYEN'  => ['required', 'exists:QUYEN,maQuyen'],
        ], [
            'name.required'      => 'Vui lòng nhập họ tên.',
            'name.min'           => 'Họ tên phải có ít nhất :min ký tự.',
            'email.required'     => 'Vui lòng nhập email.',
            'email.email'        => 'Email không hợp lệ.',
            'email.unique'       => 'Email đã được sử dụng.',
            'password.required'  => 'Vui lòng nhập mật khẩu.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.min'       => 'Mật khẩu phải có ít nhất :min ký tự.',
            'phone.regex'        => 'Số điện thoại gồm 10 chữ số và bắt đầu bằng 0.',
            'MAQUYEN.required'   => 'Vui lòng chọn quyền.',
            'MAQUYEN.exists'     => 'Quyền không hợp lệ.',
        ]);

        $user = User::create([
            'hoTen'     => $request->name,
            'email'     => $request->email,
            'sdt'       => $request->phone,
            'matKhau'   => Hash::make($request->password),
            'vaiTro'    => $request->MAQUYEN,
            'trangThai' => 'ACTIVE',
        ]);

        $user->roles()->sync([$request->MAQUYEN]);

        $roleName = DB::table('QUYEN')
            ->where('maQuyen', $request->MAQUYEN)
            ->value('tenQuyen');

        if ($this->normalizeRoleSlug($roleName) === 'student') {
            app(EnsureCustomerProfile::class)->handle($user);
        }

        return back()->with('success', 'Tạo người dùng thành công.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => ['required', 'string', 'min:2', 'max:255'],
            'email'    => ['required', 'email:rfc,dns', 'max:255', 'unique:nguoidung,email,' . $user->getKey() . ',maND'],
            'phone'    => ['nullable', 'regex:/^0\d{9}$/'],
            'password' => ['nullable', 'confirmed', Password::min(6)],
        ], [
            'name.required'      => 'Vui lòng nhập họ tên.',
            'name.min'           => 'Họ tên phải có ít nhất :min ký tự.',
            'email.required'     => 'Vui lòng nhập email.',
            'email.email'        => 'Email không hợp lệ.',
            'email.unique'       => 'Email đã được sử dụng.',
            'phone.regex'        => 'Số điện thoại gồm 10 chữ số và bắt đầu bằng 0.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.min'       => 'Mật khẩu phải có ít nhất :min ký tự.',
        ]);

        $data = [
            'hoTen' => $request->name,
            'email' => $request->email,
            'sdt'   => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['matKhau'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Cập nhật người dùng thành công.');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'MAQUYEN' => ['required', 'exists:QUYEN,maQuyen'],
        ], [
            'MAQUYEN.required' => 'Vui lòng chọn quyền.',
            'MAQUYEN.exists'   => 'Quyền không hợp lệ.',
        ]);

        $newRole   = $request->MAQUYEN;
        $adminId   = $this->roleId('admin');
        $studentId = $this->roleId('student');

        $currentRoleId = optional($user->roles()->first())->maQuyen;

        if ($currentRoleId === $adminId && $newRole !== $adminId && $this->adminCount() <= 1) {
            return back()->with('error', 'Đây là admin cuối cùng, không thể thay đổi.');
        }

        $user->roles()->sync([$newRole]);
        $user->update(['vaiTro' => $newRole]);

        if ($studentId && $newRole === $studentId) {
            app(EnsureCustomerProfile::class)->handle($user);
        }

        return back()->with('success', 'Cập nhật quyền thành công.');
    }

    public function destroy(User $user)
    {
        $adminId = $this->roleId('admin');

        $isAdmin = $adminId
            ? $user->roles()->where('QUYEN.maQuyen', $adminId)->exists()
            : false;

        if ($isAdmin) {
            return back()->with('error', 'Không thể xóa tài khoản có quyền admin.');
        }

        $user->roles()->detach();
        $user->delete();

        return back()->with('success', 'Đã xóa người dùng.');
    }
}