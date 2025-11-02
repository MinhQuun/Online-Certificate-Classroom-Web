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

    public function destroySelected(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*' => ['integer'],
        ], [
            'items.required' => 'Vui lòng chọn ít nhất 1 khóa học để xoá.',
            'items.array' => 'Danh sách khóa học không hợp lệ.',
            'items.min' => 'Vui lòng chọn ít nhất 1 khóa học để xoá.',
            'items.*.integer' => 'Khóa học không hợp lệ.',
        ]);

        $selectedIds = array_map('intval', $validated['items']);
        $existingIds = StudentCart::ids();
        $toRemove = array_values(array_intersect($existingIds, $selectedIds));

        if (empty($toRemove)) {
            return back()->with('info', 'Các khóa học đã được xoá khỏi giỏ hàng trước đó.');
        }

        StudentCart::removeMany($toRemove);

        $removedCount = count($toRemove);
        $message = $removedCount === 1
            ? 'Đã xoá 1 khóa học khỏi giỏ hàng.'
            : "Đã xoá {$removedCount} khóa học khỏi giỏ hàng.";

        return back()->with('success', $message);
    }

    public function destroyAll(): RedirectResponse
    {
        StudentCart::clear();

        return back()->with('success', 'Đã xoá tất cả khóa học khỏi giỏ hàng.');
    }
}
