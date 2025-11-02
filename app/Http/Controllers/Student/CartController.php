<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Combo;
use App\Models\Course;
use App\Support\Cart\StudentCart;
use App\Support\Cart\StudentComboCart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $courses = StudentCart::courses();
        $combos = StudentComboCart::combos();

        $courseTotal = (int) $courses->sum('hocPhi');
        $comboTotal = (int) $combos->sum(fn (Combo $combo) => $combo->sale_price);

        return view('Student.cart', [
            'courses' => $courses,
            'combos' => $combos,
            'courseTotal' => $courseTotal,
            'comboTotal' => $comboTotal,
            'total' => $courseTotal + $comboTotal,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => ['required', 'integer', 'exists:khoahoc,maKH'],
        ], [
            'course_id.required' => 'Khóa học không hợp lệ.',
            'course_id.exists' => 'Khóa học không hợp lệ.',
        ]);

        /** @var Course $course */
        $course = Course::published()->findOrFail($validated['course_id']);

        $comboInCart = StudentComboCart::combos()->first(function (Combo $combo) use ($course) {
            return $combo->courses->contains('maKH', $course->maKH);
        });

        if ($comboInCart) {
            return back()->with('info', 'Khóa học đã nằm trong combo "' . $comboInCart->tenGoi . '" ở giỏ hàng.');
        }

        $added = StudentCart::add($course->maKH);

        $message = $added
            ? 'Đã thêm khóa học vào giỏ hàng!'
            : 'Khóa học này đã có trong giỏ hàng.';

        return back()->with($added ? 'success' : 'info', $message);
    }

    public function storeCombo(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'combo_id' => ['required', 'integer', 'exists:GOI_KHOA_HOC,maGoi'],
        ], [
            'combo_id.required' => 'Combo không hợp lệ.',
            'combo_id.exists' => 'Combo không hợp lệ.',
        ]);

        /** @var Combo $combo */
        $combo = Combo::with('courses')
            ->where('maGoi', $validated['combo_id'])
            ->where('trangThai', '!=', 'ARCHIVED')
            ->firstOrFail();

        if (!$combo->isCurrentlyAvailable()) {
            return back()->with('info', 'Combo này hiện chưa mở bán.');
        }

        if (StudentComboCart::has($combo->maGoi)) {
            return back()->with('info', 'Combo đã có trong giỏ hàng của bạn.');
        }

        $coursesInCombo = $combo->courses->pluck('maKH')->all();
        if (!empty($coursesInCombo)) {
            StudentCart::removeMany($coursesInCombo);
        }

        StudentComboCart::add($combo->maGoi);

        return back()->with('success', 'Đã thêm combo vào giỏ hàng!');
    }

    public function destroy(Course $course): RedirectResponse
    {
        StudentCart::remove($course->maKH);

        return back()->with('success', 'Đã xoá khóa học khỏi giỏ hàng.');
    }

    public function destroyCombo(Combo $combo): RedirectResponse
    {
        StudentComboCart::remove($combo->maGoi);

        return back()->with('success', 'Đã xoá combo khỏi giỏ hàng.');
    }

    public function destroySelected(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*' => ['string'],
        ], [
            'items.required' => 'Vui lòng chọn ít nhất một mục để xoá.',
            'items.array' => 'Danh sách lựa chọn không hợp lệ.',
            'items.min' => 'Vui lòng chọn ít nhất một mục để xoá.',
            'items.*.string' => 'Mục không hợp lệ.',
        ]);

        $courseIds = [];
        $comboIds = [];

        foreach ($validated['items'] as $value) {
            if (!is_string($value)) {
                continue;
            }

            if (str_starts_with($value, 'combo:')) {
                $comboIds[] = (int) substr($value, 6);
                continue;
            }

            if (str_starts_with($value, 'course:')) {
                $courseIds[] = (int) substr($value, 7);
                continue;
            }

            if (is_numeric($value)) {
                $courseIds[] = (int) $value;
            }
        }

        $courseIds = array_values(array_intersect(StudentCart::ids(), $courseIds));
        $comboIds = array_values(array_intersect(StudentComboCart::ids(), $comboIds));

        if (empty($courseIds) && empty($comboIds)) {
            return back()->with('info', 'Các mục đã được xoá khỏi giỏ hàng trước đó.');
        }

        StudentCart::removeMany($courseIds);
        StudentComboCart::removeMany($comboIds);

        $removedCount = count($courseIds) + count($comboIds);

        $message = $removedCount === 1
            ? 'Đã xoá 1 mục khỏi giỏ hàng.'
            : "Đã xoá {$removedCount} mục khỏi giỏ hàng.";

        return back()->with('success', $message);
    }

    public function destroyAll(): RedirectResponse
    {
        StudentCart::clear();
        StudentComboCart::clear();

        return back()->with('success', 'Đã xoá toàn bộ giỏ hàng.');
    }
}

