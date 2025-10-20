<?php
// app/Http/Controllers/Admin/CourseAdminController.php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course; 
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Support\RoleResolver;

class CourseAdminController extends Controller
{
    public function index()
    {
        $courses = Course::with(['category', 'teacher'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
                    
        // Lấy danh sách giảng viên từ NGUOIDUNG có role giảng viên
        $teacherId = RoleResolver::findRoleId(['giang-vien', 'teacher']);
        $teachers = User::whereHas('roles', function($query) use ($teacherId) {
            $query->where('QUYEN.maQuyen', $teacherId);
        })->get();

        $categories = Category::orderBy('tenDanhMuc')->get();

        return view('admin.courses', compact('courses', 'teachers', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenKH' => 'required|string|max:150',
            'maDanhMuc' => 'required|exists:danhmuc,maDanhMuc',
            'maND' => 'required|exists:nguoidung,maND',
            'hocPhi' => 'required|numeric|min:0',
            'moTa' => 'nullable|string|max:2000',
            'ngayBatDau' => 'nullable|date',
            'ngayKetThuc' => 'nullable|date|after:ngayBatDau',
            'hinhanh' => 'nullable|image|max:2048',
            'thoiHanNgay' => 'required|integer|min:1',
        ]);

        $data = $request->except('hinhanh');
        $data['slug'] = Str::slug($request->tenKH);
        $data['trangThai'] = 'DRAFT';

        // Upload hình ảnh
        if ($request->hasFile('hinhanh')) {
            $path = $request->file('hinhanh')->store('courses/images', 'public');
            $data['hinhanh'] = $path;
        }

        $course = Course::create($data);

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Thêm khóa học thành công');
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $request->validate([
            'tenKH' => 'required|string|max:150',
            'maDanhMuc' => 'required|exists:danhmuc,maDanhMuc',
            'maND' => 'required|exists:nguoidung,maND',
            'hocPhi' => 'required|numeric|min:0',
            'moTa' => 'nullable|string|max:2000',
            'ngayBatDau' => 'nullable|date',
            'ngayKetThuc' => 'nullable|date|after:ngayBatDau',
            'hinhanh' => 'nullable|image|max:2048',
            'thoiHanNgay' => 'required|integer|min:1',
            'trangThai' => 'required|in:DRAFT,PUBLISHED,ARCHIVED'
        ]);

        $data = $request->except('hinhanh');

        if ($request->hasFile('hinhanh')) {
            // Xóa ảnh cũ
            if ($course->hinhanh) {
                Storage::disk('public')->delete($course->hinhanh);
            }
            
            $path = $request->file('hinhanh')->store('courses/images', 'public');
            $data['hinhanh'] = $path;
        }

        $course->update($data);

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Cập nhật khóa học thành công');
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);

        // Xóa hình ảnh
        if ($course->hinhanh) {
            Storage::disk('public')->delete($course->hinhanh);
        }

        $course->delete();

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Xóa khóa học thành công');
    }
}