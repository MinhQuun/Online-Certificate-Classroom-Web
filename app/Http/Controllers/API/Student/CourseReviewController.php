<?php

namespace App\Http\Controllers\API\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseReviewController extends Controller
{
    public function index(Request $request, Course $course): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);
        $perPage = max(1, min($perPage, 25));

        $reviews = CourseReview::query()
            ->with(['student.user' => fn ($query) => $query->select('maND', 'hoTen')])
            ->where('maKH', $course->maKH)
            ->orderByDesc('ngayDG')
            ->paginate($perPage);

        return response()->json([
            'status'  => 'success',
            'message' => 'Lấy danh sách đánh giá thành công.',
            'data'    => [
                'items' => $reviews->getCollection()->map(function (CourseReview $review) {
                    return [
                        'id'         => $review->maDG,
                        'score'      => (float) $review->diemSo,
                        'comment'    => $review->nhanxet,
                        'created_at' => optional($review->ngayDG ?? $review->created_at)->toIso8601String(),
                        'student'    => $review->student ? [
                            'id'   => $review->student->maHV,
                            'name' => $review->student->hoTen ?? $review->student->user?->hoTen,
                        ] : null,
                    ];
                })->values(),
                'pagination' => [
                    'current_page' => $reviews->currentPage(),
                    'per_page'     => $reviews->perPage(),
                    'total'        => $reviews->total(),
                    'last_page'    => $reviews->lastPage(),
                ],
                'summary' => [
                    'average' => (float) $course->rating_avg,
                    'count'   => (int) $course->rating_count,
                ],
            ],
        ]);
    }

    public function store(Request $request, Course $course): JsonResponse
    {
        $user = $request->user()->loadMissing('student');

        if (! $user->student) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tài khoản chưa có hồ sơ học viên.',
            ], 403);
        }

        $validated = $request->validate([
            'score'   => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $enrolled = Enrollment::query()
            ->where('maHV', $user->student->maHV)
            ->where('maKH', $course->maKH)
            ->where('trangThai', 'ACTIVE')
            ->exists();

        if (! $enrolled) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Bạn cần kích hoạt khóa học trước khi đánh giá.',
            ], 403);
        }

        $review = DB::transaction(function () use ($course, $user, $validated) {
            CourseReview::updateOrCreate(
                [
                    'maHV' => $user->student->maHV,
                    'maKH' => $course->maKH,
                ],
                [
                    'diemSo'  => (int) $validated['score'],
                    'nhanxet' => $validated['comment'] ?? null,
                    'ngayDG'  => now(),
                ]
            );

            $metrics = CourseReview::query()
                ->where('maKH', $course->maKH)
                ->selectRaw('COUNT(*) as total_reviews, AVG(diemSo) as avg_score')
                ->first();

            $course->forceFill([
                'rating_count' => (int) ($metrics->total_reviews ?? 0),
                'rating_avg'   => $metrics->avg_score ? round((float) $metrics->avg_score, 2) : null,
            ])->save();

            return CourseReview::query()
                ->with('student.user')
                ->where('maHV', $user->student->maHV)
                ->where('maKH', $course->maKH)
                ->latest('ngayDG')
                ->first();
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Đã ghi nhận đánh giá khóa học.',
            'data'    => [
                'id'         => $review?->maDG,
                'score'      => (float) $review?->diemSo,
                'comment'    => $review?->nhanxet,
                'created_at' => optional($review?->ngayDG ?? $review?->created_at)->toIso8601String(),
            ],
        ], 201);
    }
}
