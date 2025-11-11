<?php

namespace App\Http\Controllers\API\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Lấy danh sách khóa học đang mở cho học viên.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 12);
        $perPage = max(1, min($perPage, 50)); // giới hạn cho mobile

        $query = Course::query()
            ->published()
            ->with([
                'teacher:maND,hoTen',
                'category:maDanhMuc,tenDanhMuc',
            ])
            ->withCount('lessons')
            ->orderByDesc('maKH');

        $keyword = trim((string) ($request->query('q') ?? $request->query('search', '')));
        if ($keyword !== '') {
            $query->where(function ($builder) use ($keyword) {
                $builder->where('tenKH', 'like', "%{$keyword}%")
                    ->orWhere('slug', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('maDanhMuc', $request->integer('category_id'));
        }

        $categorySlug = trim((string) ($request->query('category') ?? $request->query('category_slug', '')));
        if ($categorySlug !== '') {
            $query->whereHas('category', function ($builder) use ($categorySlug) {
                $builder->where('slug', $categorySlug);
            });
        }

        $paginator = $query->paginate($perPage);

        $items = $paginator->getCollection()
            ->map(fn (Course $course) => $this->transformCourseSummary($course))
            ->values();

        return response()->json([
            'status'  => 'success',
            'message' => 'Lấy danh sách khóa học thành công.',
            'data'    => [
                'items' => $items,
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page'     => $paginator->perPage(),
                    'last_page'    => $paginator->lastPage(),
                    'total'        => $paginator->total(),
                ],
            ],
        ]);
    }

    /**
     * Lấy chi tiết một khóa học (chapters, lessons...).
     */
    public function show(int $courseId): JsonResponse
    {
        $course = Course::query()
            ->published()
            ->with([
                'teacher:maND,hoTen',
                'category:maDanhMuc,tenDanhMuc',
                'chapters.lessons',
                'miniTests' => fn ($query) => $query->visibleToStudents()->orderBy('thuTu'),
                'promotions',
            ])
            ->findOrFail($courseId);

        return response()->json([
            'status'  => 'success',
            'message' => 'Lấy chi tiết khóa học thành công.',
            'data'    => $this->transformCourseDetail($course),
        ]);
    }

    /**
     * Liệt kê các khóa học sinh viên đã đăng ký theo tiến độ.
     */
    public function myCourses(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user?->student;

        if (! $student) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tài khoản chưa có thông tin học viên.',
            ], 404);
        }

        $statusFilter = Str::lower((string) $request->query('status', 'all'));
        $statusCode = match ($statusFilter) {
            'active'  => 'ACTIVE',
            'pending' => 'PENDING',
            'expired' => 'EXPIRED',
            default   => null,
        };

        if (! $statusCode && $statusFilter !== 'all') {
            $statusFilter = 'all';
        }

        $query = Enrollment::query()
            ->with([
                'course' => fn ($courseQuery) => $courseQuery
                    ->with([
                        'teacher:maND,hoTen',
                        'category:maDanhMuc,tenDanhMuc',
                    ])
                    ->withCount('lessons'),
                'lastLesson',
            ])
            ->where('maHV', $student->maHV);

        if ($statusCode) {
            $query->where('trangThai', $statusCode);
        }

        $enrollments = $query
            ->orderByDesc('ngayNhapHoc')
            ->orderByDesc('created_at')
            ->get();

        $statusCounts = Enrollment::query()
            ->where('maHV', $student->maHV)
            ->selectRaw('trangThai, COUNT(*) as total')
            ->groupBy('trangThai')
            ->pluck('total', 'trangThai');

        $summary = [
            'total'   => (int) $statusCounts->sum(),
            'active'  => (int) ($statusCounts['ACTIVE'] ?? 0),
            'pending' => (int) ($statusCounts['PENDING'] ?? 0),
            'expired' => (int) ($statusCounts['EXPIRED'] ?? 0),
        ];
        $summary['all'] = $summary['total'];

        $items = $enrollments
            ->map(fn (Enrollment $enrollment) => $this->transformEnrollment($enrollment))
            ->values();

        return response()->json([
            'status'  => 'success',
            'message' => 'Lấy danh sách khóa học đã đăng ký thành công.',
            'data'    => [
                'items' => $items,
                'summary' => $summary,
                'filters' => [
                    'status' => $statusFilter,
                ],
            ],
        ]);
    }

    protected function transformCourseSummary(Course $course): array
    {
        return [
            'id'                => $course->maKH,
            'title'             => $course->tenKH,
            'slug'              => $course->slug,
            'cover_image'       => $course->cover_image_url,
            'short_description' => $this->shortenDescription($course->moTa),
            'price'             => [
                'original' => $course->original_price,
                'sale'     => $course->sale_price,
                'currency' => 'VND',
                'saving'   => $course->saving_amount,
            ],
            'lessons_count'     => $course->lessons_count ?? $course->lessons()->count(),
            'teacher'           => $course->teacher ? [
                'id'   => $course->teacher->maND,
                'name' => $course->teacher->hoTen,
            ] : null,
            'category'          => $course->category ? [
                'id'   => $course->category->maDanhMuc,
                'name' => $course->category->tenDanhMuc,
            ] : null,
        ];
    }

    protected function transformCourseDetail(Course $course): array
    {
        $course->loadMissing(['chapters.lessons']);

        return [
            'id'           => $course->maKH,
            'title'        => $course->tenKH,
            'slug'         => $course->slug,
            'description'  => $course->moTa,
            'cover_image'  => $course->cover_image_url,
            'duration_days'=> $course->thoiHanNgay,
            'start_date'   => $this->formatDate($course->ngayBatDau),
            'end_date'     => $this->formatDate($course->ngayKetThuc),
            'price'        => [
                'original' => $course->original_price,
                'sale'     => $course->sale_price,
                'currency' => 'VND',
                'saving'   => $course->saving_amount,
            ],
            'category'     => $course->category ? [
                'id'   => $course->category->maDanhMuc,
                'name' => $course->category->tenDanhMuc,
            ] : null,
            'teacher'      => $course->teacher ? [
                'id'   => $course->teacher->maND,
                'name' => $course->teacher->hoTen,
            ] : null,
            'chapters'     => $course->chapters->map(function ($chapter) {
                return [
                    'id'      => $chapter->maChuong,
                    'title'   => $chapter->tenChuong,
                    'order'   => $chapter->thuTu,
                    'lessons' => $chapter->lessons->map(function ($lesson) {
                        return [
                            'id'    => $lesson->maBH,
                            'title' => $lesson->tieuDe,
                            'order' => $lesson->thuTu,
                            'type'  => $lesson->loai,
                        ];
                    })->values(),
                ];
            })->values(),
            'mini_tests'   => $course->miniTests->map(function ($miniTest) {
                return [
                    'id'        => $miniTest->maMT,
                    'title'     => $miniTest->title,
                    'order'     => $miniTest->thuTu,
                    'skill'     => $miniTest->skill_type,
                    'time_limit'=> $miniTest->time_limit_min,
                    'attempts_allowed' => $miniTest->attempts_allowed,
                ];
            })->values(),
            'active_promotion' => $course->active_promotion ? [
                'id'           => $course->active_promotion->maKM,
                'name'         => $course->active_promotion->tenKM ?? null,
                'type'         => $course->active_promotion->loaiUuDai,
                'value'        => $course->active_promotion->giaTriUuDai,
                'expires_at'   => $this->formatDate($course->active_promotion->ngayKetThuc ?? null),
            ] : null,
        ];
    }

    protected function transformEnrollment(Enrollment $enrollment): array
    {
        $course = $enrollment->course;

        return [
            'enrollment_id' => sprintf('%s:%s', $enrollment->maHV, $enrollment->maKH),
            'status'        => $enrollment->trangThai,
            'course'        => $course ? $this->transformCourseSummary($course) : null,
            'progress'      => [
                'percent_overall' => (int) ($enrollment->progress_percent ?? 0),
                'percent_video'   => (int) ($enrollment->video_progress_percent ?? 0),
                'avg_minitest'    => $enrollment->avg_minitest_score !== null
                    ? (float) $enrollment->avg_minitest_score
                    : null,
                'last_lesson'     => $enrollment->lastLesson ? [
                    'id'    => $enrollment->lastLesson->maBH,
                    'title' => $enrollment->lastLesson->tieuDe,
                ] : null,
            ],
            'timeline'      => [
                'enrolled_at' => $this->formatDate($enrollment->ngayNhapHoc),
                'activated_at'=> $this->formatDateTime($enrollment->activated_at),
                'expires_at'  => $this->formatDateTime($enrollment->expires_at),
                'updated_at'  => $this->formatDateTime($enrollment->updated_at ?? null),
            ],
        ];
    }

    protected function shortenDescription(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $plain = trim(strip_tags($value));

        return Str::limit($plain, 180);
    }

    protected function formatDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $date = $value instanceof Carbon ? $value : Carbon::parse($value);

        return $date->format('Y-m-d');
    }

    protected function formatDateTime($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $datetime = $value instanceof Carbon ? $value : Carbon::parse($value);

        return $datetime->toIso8601String();
    }
}
