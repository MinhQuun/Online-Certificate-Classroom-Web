<?php

namespace App\Http\Controllers\API\Student;

use App\Http\Controllers\Controller;
use App\Models\Combo;
use App\Models\Course;
use App\Support\Cart\StudentCart;
use App\Support\Cart\StudentComboCart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => 'Lấy giỏ hàng thành công.',
            'data'    => $this->buildCartPayload(),
        ]);
    }

    public function storeCourse(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_id' => ['required', 'integer', 'exists:khoahoc,maKH'],
        ]);

        /** @var Course $course */
        $course = Course::published()->findOrFail($validated['course_id']);

        $comboInCart = StudentComboCart::combos()->first(function (Combo $combo) use ($course) {
            return $combo->courses->contains('maKH', $course->maKH);
        });

        if ($comboInCart) {
            return response()->json([
                'status'  => 'info',
                'message' => 'Khóa học đã nằm trong combo "' . $comboInCart->tenGoi . '" ở giỏ hàng.',
                'data'    => $this->buildCartPayload(),
            ], 409);
        }

        $added = StudentCart::add($course->maKH);

        return response()->json([
            'status'  => 'success',
            'message' => $added
                ? 'Đã thêm khóa học vào giỏ hàng.'
                : 'Khóa học này đã có trong giỏ hàng.',
            'data'    => $this->buildCartPayload(),
        ], $added ? 201 : 200);
    }

    public function storeCombo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'combo_id' => ['required', 'integer', 'exists:goi_khoa_hoc,maGoi'],
        ]);

        /** @var Combo $combo */
        $combo = Combo::with('courses')
            ->where('maGoi', $validated['combo_id'])
            ->where('trangThai', '!=', 'ARCHIVED')
            ->firstOrFail();

        if (!$combo->isCurrentlyAvailable()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Combo này hiện chưa mở bán.',
            ], 422);
        }

        if (StudentComboCart::has($combo->maGoi)) {
            return response()->json([
                'status'  => 'info',
                'message' => 'Combo đã tồn tại trong giỏ hàng.',
                'data'    => $this->buildCartPayload(),
            ], 409);
        }

        $coursesInCombo = $combo->courses->pluck('maKH')->all();
        if (!empty($coursesInCombo)) {
            StudentCart::removeMany($coursesInCombo);
        }

        StudentComboCart::add($combo->maGoi);

        return response()->json([
            'status'  => 'success',
            'message' => 'Đã thêm combo vào giỏ hàng.',
            'data'    => $this->buildCartPayload(),
        ], 201);
    }

    public function destroyCourse(int $courseId): JsonResponse
    {
        if (!StudentCart::has($courseId)) {
            return response()->json([
                'status'  => 'info',
                'message' => 'Khóa học không có trong giỏ hàng.',
                'data'    => $this->buildCartPayload(),
            ], 404);
        }

        StudentCart::remove($courseId);

        return response()->json([
            'status'  => 'success',
            'message' => 'Đã xoá khóa học khỏi giỏ hàng.',
            'data'    => $this->buildCartPayload(),
        ]);
    }

    public function destroyCombo(int $comboId): JsonResponse
    {
        if (!StudentComboCart::has($comboId)) {
            return response()->json([
                'status'  => 'info',
                'message' => 'Combo không tồn tại trong giỏ hàng.',
                'data'    => $this->buildCartPayload(),
            ], 404);
        }

        StudentComboCart::remove($comboId);

        return response()->json([
            'status'  => 'success',
            'message' => 'Đã xoá combo khỏi giỏ hàng.',
            'data'    => $this->buildCartPayload(),
        ]);
    }

    public function destroySelected(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'selected_courses' => ['nullable', 'array'],
            'selected_courses.*' => ['integer'],
            'selected_combos' => ['nullable', 'array'],
            'selected_combos.*' => ['integer'],
        ]);

        $courses = $validated['selected_courses'] ?? [];
        $combos = $validated['selected_combos'] ?? [];

        if (empty($courses) && empty($combos)) {
            throw ValidationException::withMessages([
                'selected' => ['Vui lòng chọn ít nhất một mục để xoá.'],
            ]);
        }

        StudentCart::removeMany($courses);
        StudentComboCart::removeMany($combos);

        return response()->json([
            'status'  => 'success',
            'message' => 'Đã xoá các mục đã chọn khỏi giỏ hàng.',
            'data'    => $this->buildCartPayload(),
        ]);
    }

    public function clear(): JsonResponse
    {
        StudentCart::clear();
        StudentComboCart::clear();

        return response()->json([
            'status'  => 'success',
            'message' => 'Đã xoá toàn bộ giỏ hàng.',
            'data'    => $this->buildCartPayload(),
        ]);
    }

    protected function buildCartPayload(): array
    {
        $courses = StudentCart::courses()->map(function (Course $course) {
            return [
                'id'          => $course->maKH,
                'title'       => $course->tenKH,
                'slug'        => $course->slug,
                'price'       => (int) $course->hocPhi,
                'cover_image' => $course->cover_image_url,
                'teacher'     => $course->teacher?->hoTen,
            ];
        })->values();

        $combos = StudentComboCart::combos()->map(function (Combo $combo) {
            return [
                'id'             => $combo->maGoi,
                'title'          => $combo->tenGoi,
                'slug'           => $combo->slug,
                'price'          => (int) $combo->sale_price,
                'original_price' => (int) $combo->original_price,
                'cover_image'    => $combo->cover_image_url,
                'course_count'   => $combo->courses->count(),
            ];
        })->values();

        $courseTotal = $courses->sum('price');
        $comboTotal = $combos->sum('price');

        return [
            'courses' => $courses,
            'combos'  => $combos,
            'counts' => [
                'courses' => $courses->count(),
                'combos'  => $combos->count(),
                'total'   => $courses->count() + $combos->count(),
            ],
            'totals' => [
                'courses' => $courseTotal,
                'combos'  => $comboTotal,
                'grand'   => $courseTotal + $comboTotal,
            ],
        ];
    }
}
