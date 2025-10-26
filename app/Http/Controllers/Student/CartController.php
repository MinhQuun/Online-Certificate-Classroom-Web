<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Support\Cart\StudentCart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $courses = StudentCart::courses();
        $total = $courses->sum('hocPhi');

        return view('Student.cart', [
            'courses' => $courses,
            'total'   => $total,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => ['required', 'integer', 'exists:khoahoc,maKH'],
        ], [
            'course_id.required' => 'Khóa học không hợp lệ.',
            'course_id.exists'   => 'Khóa học không hợp lệ.',
        ]);

        /** @var Course $course */
        $course = Course::published()->findOrFail($validated['course_id']);

        $added = StudentCart::add($course->maKH);

        $message = $added
            ? 'Đã thêm khóa học vào giỏ hàng!'
            : 'Khóa học này đã có trong giỏ hàng.';

        $flashType = $added ? 'success' : 'info';

        return back()->with($flashType, $message);
    }

    public function destroy(Course $course): RedirectResponse
    {
        StudentCart::remove($course->maKH);

        return back()->with('success', 'Đã xoá khóa học khỏi giỏ hàng.');
    }
}