<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseReviewController extends Controller
{
    public function store(Request $request, Course $course): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'diemSo'  => ['required', 'integer', 'min:1', 'max:5'],
            'nhanxet' => ['nullable', 'string', 'max:1000'],
        ]);

        $userId = Auth::id();
        if (!$userId) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập để đánh giá.'], 403);
            }
            abort(403);
        }

        $student = Student::where('maND', $userId)->first();
        if (!$student) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy thông tin học viên.'], 403);
            }
            abort(403);
        }

        $isEnrolled = DB::table('HOCVIEN_KHOAHOC')
            ->where('maHV', $student->maHV)
            ->where('maKH', $course->maKH)
            ->where('trangThai', 'ACTIVE')
            ->exists();

        if (!$isEnrolled) {
            $message = 'Bạn cần kích hoạt khóa học trước khi đánh giá.';
            
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 200);
            }
            
            return redirect()
                ->back()
                ->withErrors(['review' => $message], 'review')
                ->withInput();
        }

        CourseReview::updateOrCreate(
            [
                'maHV' => $student->maHV,
                'maKH' => $course->maKH,
            ],
            [
                'diemSo'  => (int) $validated['diemSo'],
                'nhanxet' => $validated['nhanxet'] ?? null,
                'ngayDG'  => now(),
            ]
        );

        $message = 'Cảm ơn bạn đã đánh giá khóa học.';
        
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()
            ->route('student.courses.show', $course->slug)
            ->with('success', $message)
            ->with('success_title', 'Đánh giá đã được ghi nhận');
    }
}
