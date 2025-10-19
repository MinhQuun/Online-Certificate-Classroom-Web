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
        $q          = trim((string) $request->query('q', ''));
        $roleFilter = Str::lower((string) $request->query('role', ''));

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
            ->when($roleFilter !== '', function ($builder) use ($roleFilter) {
                $builder->whereHas('roles', function ($roleQuery) use ($roleFilter) {
                    $roleQuery->whereRaw('LOWER(QUYEN.tenQuyen) = ?', [$roleFilter])
                        ->orWhereRaw('LOWER(QUYEN.maQuyen) = ?', [$roleFilter]);
                });
            })
            ->with('roles')
            ->paginate(10)
            ->withQueryString();

        $adminId     = $this->roleId('admin');
        $nhanvienId  = $this->roleId('nhanvien');
        $khachhangId = $this->roleId('khachhang');

        $counts = [
            'admin'     => $adminId ? (int) DB::table('QUYEN_NGUOIDUNG')->where('maQuyen', $adminId)->count() : 0,
            'nhanvien'  => $nhanvienId ? (int) DB::table('QUYEN_NGUOIDUNG')->where('maQuyen', $nhanvienId)->count() : 0,
            'khachhang' => $khachhangId ? (int) DB::table('QUYEN_NGUOIDUNG')->where('maQuyen', $khachhangId)->count() : 0,
            'total'     => (int) User::count(),
        ];

        return view('admin.index', compact(
            'users',
            'roles',
            'q',
            'roleFilter',
            'adminId',
            'nhanvienId',
            'khachhangId',
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
            'phone.regex' => 'So dien thoai phai gom 10 chu so va bat dau bang 0.',
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

        if (Str::slug((string) $roleName) === 'khachhang') {
            app(EnsureCustomerProfile::class)->handle($user);
        }

        return back()->with('success', 'Tao nguoi dung thanh cong.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => ['required', 'string', 'min:2', 'max:255'],
            'email'    => ['required', 'email:rfc,dns', 'max:255', 'unique:nguoidung,email,' . $user->getKey() . ',maND'],
            'phone'    => ['nullable', 'regex:/^0\d{9}$/'],
            'password' => ['nullable', 'confirmed', Password::min(6)],
        ], [
            'phone.regex' => 'So dien thoai phai gom 10 chu so va bat dau bang 0.',
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

        return back()->with('success', 'Cap nhat nguoi dung thanh cong.');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'MAQUYEN' => ['required', 'exists:QUYEN,maQuyen'],
        ]);

        $newRole = $request->MAQUYEN;
        $adminId = $this->roleId('admin');
        $staffId = $this->roleId('nhanvien');
        $customerId = $this->roleId('khachhang');

        $currentRoleId = optional($user->roles()->first())->maQuyen;

        if ($currentRoleId === $customerId && $newRole !== $customerId) {
            return back()->with('error', 'Khach hang khong the doi sang quyen khac.');
        }

        if ($currentRoleId === $adminId && $newRole !== $adminId) {
            return back()->with('error', 'Tai khoan admin khong the doi sang quyen khac.');
        }

        if ($currentRoleId === $staffId && $newRole === $customerId) {
            return back()->with('error', 'Nhan vien khong the doi xuong khach hang.');
        }

        if ($currentRoleId === $adminId && $this->adminCount() <= 1) {
            return back()->with('error', 'Day la admin cuoi cung, khong the thay doi.');
        }

        $user->roles()->sync([$newRole]);
        $user->update(['vaiTro' => $newRole]);

        return back()->with('success', 'Cap nhat quyen thanh cong.');
    }

    public function destroy(User $user)
    {
        $adminId = $this->roleId('admin');
        $customerId = $this->roleId('khachhang');

        $isAdmin = $adminId
            ? $user->roles()->where('QUYEN.maQuyen', $adminId)->exists()
            : false;

        if ($isAdmin) {
            return back()->with('error', 'Khong the xoa tai khoan co quyen admin.');
        }

        $isCustomer = $customerId
            ? $user->roles()->where('QUYEN.maQuyen', $customerId)->exists()
            : false;

        if ($isCustomer && $user->khachHang && $user->khachHang->donHangs()->exists()) {
            return back()->with('error', 'Khach hang da co don hang, khong the xoa.');
        }

        $user->roles()->detach();
        $user->delete();

        return back()->with('success', 'Da xoa nguoi dung.');
    }
}

