<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use App\Support\RoleResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query()->with(['category','teacher']);

        // Search theo tên/mã
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($x) use ($q) {
                $x->where('tenKH', 'like', "%$q%")
                    ->orWhere('maKH', 'like', "%$q%");
            });
        }

        // Lọc danh mục
        if ($request->filled('category_id')) {
            $query->where('maDanhMuc', $request->category_id);
        }

        // Lọc trạng thái
        if ($request->filled('status')) {
            $query->where('trangThai', $request->status);
        }

        $courses = $query->orderByDesc('created_at')
                            ->paginate(10)
                            ->withQueryString();

        // Danh sách giảng viên theo roles
        $teacherRoleId = RoleResolver::findRoleId(['giang-vien','teacher']);
        $teachers = User::whereHas('roles', function ($q) use ($teacherRoleId) {
                $q->where('QUYEN.maQuyen', $teacherRoleId);
            })
            ->orderBy('hoTen')
            ->get(['maND','hoTen']);

        $categories = Category::orderBy('tenDanhMuc')->get(['maDanhMuc','tenDanhMuc']);

        return view('admin.courses', compact('courses','teachers','categories'));
    }

    public function store(Request $request)
    {
        // Lấy danh sách giảng viên hợp lệ theo roles
        $teacherRoleId = RoleResolver::findRoleId(['giang-vien','teacher']);
        $teacherIds = User::whereHas('roles', function ($q) use ($teacherRoleId) {
                $q->where('QUYEN.maQuyen', $teacherRoleId);
            })
            ->pluck('maND')
            ->toArray();

        $request->validate([
            'tenKH'       => 'required|string|max:150',
            'maDanhMuc'   => 'required|exists:danhmuc,maDanhMuc',
            'maND'        => ['required','exists:nguoidung,maND','in:'.implode(',', $teacherIds)],
            'hocPhi'      => 'required|numeric|min:0',
            'moTa'        => 'nullable|string|max:2000',
            'ngayBatDau'  => 'nullable|date',
            'ngayKetThuc' => 'nullable|date|after:ngayBatDau',
            'hinhanh'     => 'nullable|image|max:2048',
            'thoiHanNgay' => 'required|integer|min:1',
        ]);

        $data = $request->except('hinhanh');
        $data['slug'] = Str::slug($request->tenKH);
        $data['trangThai'] = 'DRAFT';

        // Upload ảnh -> public/Assets (viết hoa A để khớp accessor)
        if ($request->hasFile('hinhanh')) {
            $file = $request->file('hinhanh');
            $fileName = time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                        .'.'.$file->getClientOriginalExtension();
            $file->move(public_path('Assets'), $fileName);
            $data['hinhanh'] = 'Assets/'.$fileName; // lưu đường dẫn tương đối
        }

        $course = Course::create($data);

        // Tạo thư mục trên R2 theo slug tên khoá (không bắt buộc)
        try {
            $directoryName = Str::slug($course->tenKH);
            Storage::disk('r2')->makeDirectory($directoryName);
        } catch (\Exception $e) {
            \Log::error('Lỗi tạo thư mục trên R2: '.$e->getMessage());
        }

        return redirect()->route('admin.courses.index')
            ->with('success', 'Thêm khóa học thành công');
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        // Lấy danh sách giảng viên hợp lệ theo roles
        $teacherRoleId = RoleResolver::findRoleId(['giang-vien','teacher']);
        $teacherIds = User::whereHas('roles', function ($q) use ($teacherRoleId) {
                $q->where('QUYEN.maQuyen', $teacherRoleId);
            })
            ->pluck('maND')
            ->toArray();

        $request->validate([
            'tenKH'       => 'required|string|max:150',
            'maDanhMuc'   => 'required|exists:danhmuc,maDanhMuc',
            'maND'        => ['required','exists:nguoidung,maND','in:'.implode(',', $teacherIds)],
            'hocPhi'      => 'required|numeric|min:0',
            'moTa'        => 'nullable|string|max:2000',
            'ngayBatDau'  => 'nullable|date',
            'ngayKetThuc' => 'nullable|date|after:ngayBatDau',
            'hinhanh'     => 'nullable|image|max:2048',
            'thoiHanNgay' => 'required|integer|min:1',
            'trangThai'   => 'required|in:DRAFT,PUBLISHED,ARCHIVED',
        ]);

        $data = $request->except('hinhanh');

        // Thay ảnh (nếu có)
        if ($request->hasFile('hinhanh')) {
        if ($course->hinhanh) {
            $old = public_path($course->hinhanh);
            if (file_exists($old)) @unlink($old);
        }
        $file = $request->file('hinhanh');
        $fileName = time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension();
        $file->move(public_path('Assets'), $fileName);
        $data['hinhanh'] = 'Assets/'.$fileName;
    }

        // Cập nhật phần còn lại
        $course->fill($data);

        // Gán rõ ràng trạng thái (đề phòng bị lọc mất ở $fillable sau này)
        $course->trangThai = $request->input('trangThai', $course->trangThai);

        $course->save();

        return redirect()->route('admin.courses.index')
                        ->with('success', 'Cập nhật khóa học thành công');
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);

        // Xóa ảnh local
        if ($course->hinhanh) {
            $imagePath = public_path($course->hinhanh);
            if (file_exists($imagePath)) @unlink($imagePath);
        }

        // Xóa thư mục R2 (nếu có)
        try {
            $directoryName = Str::slug($course->tenKH);
            Storage::disk('r2')->deleteDirectory($directoryName);
        } catch (\Exception $e) {
            \Log::error('Lỗi xóa thư mục trên R2: '.$e->getMessage());
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Xóa khóa học thành công');
    }
}
