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

        return (int) DB::table('quyen_nguoidung')
            ->where('maQuyen', $adminId)
            ->count();
    }

    public function index(Request $request)
    {
        $q               = trim((string) $request->query('q', ''));
        $roleFilterInput = (string) $request->query('role', '');
        $roleFilter      = $this->normalizeRoleSlug($roleFilterInput);
        $roleFilterCode  = $roleFilter ? $this->roleId($roleFilter) : null;

        $roles = DB::table('quyen')
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
                    $roleQuery->where('quyen.maQuyen', $roleFilterCode);
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
            'admin'   => $adminId ? (int) DB::table('quyen_nguoidung')->where('maQuyen', $adminId)->count() : 0,
            'teacher' => $teacherId ? (int) DB::table('quyen_nguoidung')->where('maQuyen', $teacherId)->count() : 0,
            'student' => $studentId ? (int) DB::table('quyen_nguoidung')->where('maQuyen', $studentId)->count() : 0,
        ];

        return view('Admin.index', compact(
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
            'MAQUYEN'  => ['required', 'exists:quyen,maQuyen'],
            'chuyenMon' => ['nullable', 'string', 'max:255'], // Validation cho chuyên môn
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
            'chuyenMon.max'      => 'Chuyên môn không được vượt quá :max ký tự.',
        ]);

        // Lấy tên quyền từ MAQUYEN để ánh xạ thành vaiTro
        $roleName = DB::table('quyen')
            ->where('maQuyen', $request->MAQUYEN)
            ->value('tenQuyen');

        // Ánh xạ tenQuyen thành vaiTro hợp lệ
        $vaiTro = match (Str::slug($roleName)) {
            'admin', 'quan-tri-vien' => 'ADMIN',
            'giang-vien', 'giao-vien', 'teacher' => 'GIANG_VIEN',
            'hoc-vien', 'hoc-sinh', 'student' => 'HOC_VIEN',
            default => 'HOC_VIEN', // Mặc định là HOC_VIEN nếu không khớp
        };

        $userData = [
            'hoTen'     => $request->name,
            'email'     => $request->email,
            'sdt'       => $request->phone,
            'matKhau'   => Hash::make($request->password),
            'vaiTro'    => $vaiTro,
            'trangThai' => 'ACTIVE',
        ];

        // Thêm chuyenMon nếu vaiTro là GIANG_VIEN
        if ($vaiTro === 'GIANG_VIEN' && $request->filled('chuyenMon')) {
            $userData['chuyenMon'] = $request->chuyenMon;
        }

        $user = User::create($userData);

        $user->roles()->sync([$request->MAQUYEN]);

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
            'MAQUYEN'  => ['required', 'exists:quyen,maQuyen'], // Thêm validation cho MAQUYEN
            'chuyenMon' => ['nullable', 'string', 'max:255'],   // Thêm validation cho chuyenMon
        ], [
            'name.required'      => 'Vui lòng nhập họ tên.',
            'name.min'           => 'Họ tên phải có ít nhất :min ký tự.',
            'email.required'     => 'Vui lòng nhập email.',
            'email.email'        => 'Email không hợp lệ.',
            'email.unique'       => 'Email đã được sử dụng.',
            'phone.regex'        => 'Số điện thoại gồm 10 chữ số và bắt đầu bằng 0.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.min'       => 'Mật khẩu phải có ít nhất :min ký tự.',
            'MAQUYEN.required'   => 'Vui lòng chọn quyền.',
            'MAQUYEN.exists'     => 'Quyền không hợp lệ.',
            'chuyenMon.max'      => 'Chuyên môn không được vượt quá :max ký tự.',
        ]);

        // Lấy tên quyền từ MAQUYEN để ánh xạ thành vaiTro
        $roleName = DB::table('quyen')
            ->where('maQuyen', $request->MAQUYEN)
            ->value('tenQuyen');

        // Ánh xạ tenQuyen thành vaiTro hợp lệ
        $vaiTro = match (Str::slug($roleName)) {
            'admin', 'quan-tri-vien' => 'ADMIN',
            'giang-vien', 'giao-vien', 'teacher' => 'GIANG_VIEN',
            'hoc-vien', 'hoc-sinh', 'student' => 'HOC_VIEN',
            default => $user->vaiTro, // Giữ nguyên vaiTro cũ nếu không khớp
        };

        $data = [
            'hoTen'     => $request->name,
            'email'     => $request->email,
            'sdt'       => $request->phone,
            'vaiTro'    => $vaiTro, // Cập nhật vaiTro dựa trên quyền mới
        ];

        // Cập nhật mật khẩu nếu có
        if ($request->filled('password')) {
            $data['matKhau'] = Hash::make($request->password);
        }

        // Thêm chuyenMon nếu vaiTro là GIANG_VIEN
        if ($vaiTro === 'GIANG_VIEN' && $request->filled('chuyenMon')) {
            $data['chuyenMon'] = $request->chuyenMon;
        } elseif ($vaiTro !== 'GIANG_VIEN') {
            $data['chuyenMon'] = null; // Xóa chuyenMon nếu không phải GIANG_VIEN
        }

        $user->update($data);
        $user->roles()->sync([$request->MAQUYEN]);

        return back()->with('success', 'Cập nhật người dùng thành công.');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'MAQUYEN' => ['required', 'exists:quyen,maQuyen'],
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
            ? $user->roles()->where('quyen.maQuyen', $adminId)->exists()
            : false;

        if ($isAdmin) {
            return back()->with('error', 'Không thể xóa tài khoản có quyền admin.');
        }

        $user->roles()->detach();
        $user->delete();

        return back()->with('success', 'Đã xóa người dùng.');
    }
}
