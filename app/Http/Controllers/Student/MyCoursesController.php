<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyCoursesController extends Controller
{
    /**
     * Hiển thị danh sách khóa học đã đăng ký của học viên
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Lấy student record
        $student = $user->student;
        
        if (!$student) {
            return redirect()->route('student.courses.index')
                ->with('error', 'Bạn chưa có thông tin học viên.');
        }

        // Lấy filter status từ query string
        $status = $request->get('status', 'all');
        
        // Query enrollments
        $query = Enrollment::with(['course.category', 'course.teacher'])
            ->where('maHV', $student->maHV);
        
        // Filter theo status
        if ($status !== 'all') {
            $query->where('trangThai', strtoupper($status));
        }
        
        // Sắp xếp theo ngày nhập học mới nhất
        $enrollments = $query->orderBy('ngayNhapHoc', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        // Đếm số lượng theo từng trạng thái
        $counts = [
            'all' => Enrollment::where('maHV', $student->maHV)->count(),
            'active' => Enrollment::where('maHV', $student->maHV)->where('trangThai', 'ACTIVE')->count(),
            'pending' => Enrollment::where('maHV', $student->maHV)->where('trangThai', 'PENDING')->count(),
            'expired' => Enrollment::where('maHV', $student->maHV)->where('trangThai', 'EXPIRED')->count(),
        ];
        
        return view('Student.my-courses', compact('enrollments', 'status', 'counts'));
    }
}
