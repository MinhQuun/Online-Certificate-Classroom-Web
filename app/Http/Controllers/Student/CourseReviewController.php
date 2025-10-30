<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseReviewController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'diemSo'  => ['required', 'integer', 'min:1', 'max:5'],
            'nhanxet' => ['nullable', 'string', 'max:1000'],
        ]);

        $userId = Auth::id();
        if (!$userId) {
            abort(403);
        }

        $student = Student::where('maND', $userId)->first();
        if (!$student) {
            abort(403);
        }

        $isEnrolled = DB::table('HOCVIEN_KHOAHOC')
            ->where('maHV', $student->maHV)
            ->where('maKH', $course->maKH)
            ->where('trangThai', 'ACTIVE')
            ->exists();

        if (!$isEnrolled) {
            return redirect()
                ->back()
                ->withErrors(
                    ['review' => 'Ban can kich hoat khoa hoc truoc khi danh gia.'],
                    'review'
                )
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

        return redirect()
            ->route('student.courses.show', $course->slug)
            ->with([
                'review_status'  => 'success',
                'review_message' => 'Cam on ban da danh gia khoa hoc.',
            ]);
    }
}
