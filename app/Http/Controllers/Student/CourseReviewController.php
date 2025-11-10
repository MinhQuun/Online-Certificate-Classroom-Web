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
                return response()->json(['success' => false, 'message' => 'Báº¡n cáº§n Ä‘Äƒng nháº­p Ä‘á»ƒ Ä‘Ã¡nh giÃ¡.'], 403);
            }
            abort(403);
        }

        $student = Student::where('maND', $userId)->first();
        if (!$student) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'KhÃ´ng tÃ¬m tháº¥y thÃ´ng tin há»c viÃªn.'], 403);
            }
            abort(403);
        }

        $isEnrolled = DB::table('hocvien_khoahoc')
            ->where('maHV', $student->maHV)
            ->where('maKH', $course->maKH)
            ->where('trangThai', 'ACTIVE')
            ->exists();

        if (!$isEnrolled) {
            $message = 'Báº¡n cáº§n kÃ­ch hoáº¡t khÃ³a há»c trÆ°á»›c khi Ä‘Ã¡nh giÃ¡.';
            
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

        $message = 'Cáº£m Æ¡n báº¡n Ä‘Ã£ Ä‘Ã¡nh giÃ¡ khÃ³a há»c.';
        
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()
            ->route('student.courses.show', $course->slug)
            ->with('success', $message)
            ->with('success_title', 'ÄÃ¡nh giÃ¡ Ä‘Ã£ Ä‘Æ°á»£c ghi nháº­n');
    }
}
