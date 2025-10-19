<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\RoleResolver;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $adminId    = RoleResolver::findRoleId(['admin']);
        $staffId    = RoleResolver::findRoleId(['nhan-vien', 'nhanvien', 'giao-vu', 'staff']);
        $customerId = RoleResolver::findRoleId(['hoc-vien', 'hocvien', 'student', 'khach-hang', 'khachhang']);

        $counts = [
            'total'     => (int) User::count(),
            'admin'     => $adminId ? (int) DB::table('QUYEN_NGUOIDUNG')->where('maQuyen', $adminId)->count() : 0,
            'nhanvien'  => $staffId ? (int) DB::table('QUYEN_NGUOIDUNG')->where('maQuyen', $staffId)->count() : 0,
            'khachhang' => $customerId ? (int) DB::table('QUYEN_NGUOIDUNG')->where('maQuyen', $customerId)->count() : 0,
        ];

        return view('admin.dashboard', compact('counts'));
    }
}


