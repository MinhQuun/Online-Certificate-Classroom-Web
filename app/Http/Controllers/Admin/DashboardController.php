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
        // Resolve role codes (maQuyen) for the 3 roles used on the dashboard
        $adminId   = RoleResolver::findRoleId(['admin']);
        $teacherId = RoleResolver::findRoleId(['giang-vien', 'giangvien', 'teacher']);
        $studentId = RoleResolver::findRoleId(['hoc-vien', 'hocvien', 'student']);

        $counts = [
            'total'   => (int) User::count(),
            'admin'   => $adminId ? (int) DB::table('quyen_nguoidung')->where('maQuyen', $adminId)->count() : 0,
            'teacher' => $teacherId ? (int) DB::table('quyen_nguoidung')->where('maQuyen', $teacherId)->count() : 0,
            'student' => $studentId ? (int) DB::table('quyen_nguoidung')->where('maQuyen', $studentId)->count() : 0,
        ];

        $roleFilters = [
            'admin'   => 'admin',
            'teacher' => 'teacher',
            'student' => 'student',
        ];

        return view('Admin.dashboard', compact('counts', 'roleFilters'));
    }
}
