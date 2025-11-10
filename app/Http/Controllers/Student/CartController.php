<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Combo;
use App\Models\Course;
use App\Support\Cart\StudentCart;
use App\Support\Cart\StudentComboCart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
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

    public function store(Request $request): RedirectResponse|JsonResponse
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
            $message = 'Khóa học đã nằm trong combo "' . $comboInCart->tenGoi . '" ở giỏ hàng.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 200);
            }
            return back()->with('info', $message);
        }

        $added = StudentCart::add($course->maKH);

        $message = $added
            ? 'Đã thêm khóa học vào giỏ hàng!'
            : 'Khóa học này đã có trong giỏ hàng.';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => $added,
                'message' => $message,
                'cartCount' => count(StudentCart::ids()) + count(StudentComboCart::ids())
            ]);
        }

        return back()->with($added ? 'success' : 'info', $message);
    }

    public function storeCombo(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'combo_id' => ['required', 'integer', 'exists:goi_khoa_hoc,maGoi'],
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
            $message = 'Combo này hiện chưa mở bán.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 200);
            }
            return back()->with('info', $message);
        }

        if (StudentComboCart::has($combo->maGoi)) {
            $message = 'Combo đã có trong giỏ hàng của bạn.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 200);
            }
            return back()->with('info', $message);
        }

        $coursesInCombo = $combo->courses->pluck('maKH')->all();
        if (!empty($coursesInCombo)) {
            StudentCart::removeMany($coursesInCombo);
        }

        StudentComboCart::add($combo->maGoi);

        $message = 'Đã thêm combo vào giỏ hàng!';
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cartCount' => count(StudentCart::ids()) + count(StudentComboCart::ids())
            ]);
        }

        return back()->with('success', $message);
    }

    public function destroy(string $courseId): RedirectResponse|JsonResponse
    {
        $courseId = (int)$courseId;

        if (!StudentCart::has($courseId)) {
            $message = 'Khóa học không có trong giỏ hàng.';
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 200);
            }
            return back()->with('info', $message);
        }

        StudentCart::remove($courseId);

        $message = 'Đã xóa khóa học khỏi giỏ hàng.';
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cartCount' => count(StudentCart::ids()) + count(StudentComboCart::ids())
            ]);
        }

        return redirect()->route('student.cart.index')->with('success', $message);
    }

    public function destroyCombo(string $comboId): RedirectResponse|JsonResponse
    {
        $comboId = (int)$comboId;

        if (!StudentComboCart::has($comboId)) {
            $message = 'Combo không có trong giỏ hàng.';
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 200);
            }
            return back()->with('info', $message);
        }

        StudentComboCart::remove($comboId);

        $message = 'Đã xóa combo khỏi giỏ hàng.';
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cartCount' => count(StudentCart::ids()) + count(StudentComboCart::ids())
            ]);
        }

        return redirect()->route('student.cart.index')->with('success', $message);
    }

    public function destroySelected(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'selected_courses' => ['nullable', 'array'],
            'selected_courses.*' => ['integer', 'exists:khoahoc,maKH'],
            'selected_combos' => ['nullable', 'array'],
            'selected_combos.*' => ['integer', 'exists:goi_khoa_hoc,maGoi'],
        ]);

        $selectedCourses = $validated['selected_courses'] ?? [];
        $selectedCombos = $validated['selected_combos'] ?? [];

        if (empty($selectedCourses) && empty($selectedCombos)) {
            $message = 'Vui lòng chọn ít nhất một mục để xóa.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 200);
            }
            return back()->with('warning', $message);
        }

        $removedCount = 0;

        if (!empty($selectedCourses)) {
            StudentCart::removeMany($selectedCourses);
            $removedCount += count($selectedCourses);
        }

        if (!empty($selectedCombos)) {
            StudentComboCart::removeMany($selectedCombos);
            $removedCount += count($selectedCombos);
        }

        $message = "Đã xóa {$removedCount} mục khỏi giỏ hàng.";
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'removedCount' => $removedCount,
                'cartCount' => count(StudentCart::ids()) + count(StudentComboCart::ids())
            ]);
        }

        return redirect()->route('student.cart.index')->with('success', $message);
    }

    public function destroyAll(): RedirectResponse|JsonResponse
    {
        StudentCart::clear();
        StudentComboCart::clear();

        $message = 'Đã xóa tất cả mục khỏi giỏ hàng.';
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cartCount' => 0
            ]);
        }

        return redirect()->route('student.cart.index')->with('success', $message);
    }
}

