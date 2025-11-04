<?php

namespace App\Http\Controllers\API\Student;

use App\Http\Controllers\API\Student\Concerns\HasLessonProgressFormatting;
use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\MiniTestResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LessonController extends Controller
{
    use HasLessonProgressFormatting;

    /**
     * Lấy nội dung chi tiết một bài học theo thông tin liên quan.
     */
    public function show(Request $request, int $lessonId): JsonResponse
    {
        $lesson = Lesson::query()
            ->with([
                'materials',
                'chapter' => function ($chapterQuery) {
                    $chapterQuery->with([
                        'course',
                        'miniTests' => fn ($miniTestQuery) => $miniTestQuery
                            ->visibleToStudents()
                            ->orderBy('thuTu')
                            ->with('materials'),
                    ]);
                },
            ])
            ->findOrFail($lessonId);

        $course = $lesson->chapter->course;
        $course->loadMissing([
            'teacher:maND,hoTen',
            'category:maDanhMuc,tenDanhMuc',
            'chapters.lessons',
        ])->loadCount('lessons');

        if ($course->trangThai !== 'PUBLISHED') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Bài học chưa được mở cho học viên.',
            ], 404);
        }

        $user = $request->user();
        $student = $user?->student;

        if (! $student) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tài khoản không có thông tin học viên.',
            ], 403);
        }

        $enrollment = Enrollment::query()
            ->where('maHV', $student->maHV)
            ->where('maKH', $course->maKH)
            ->where('trangThai', 'ACTIVE')
            ->first();

        $lesson->loadCount('discussions');

        $isPreviewLesson = $this->isPreviewLesson($lesson, $course);
        $canAccess = $enrollment !== null || $isPreviewLesson;

        if (! $canAccess) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Vui lòng kích hoạt khóa học trước khi học bài này.',
            ], 403);
        }

        $lessonProgress = null;
        $miniTestStats = collect();

        if ($enrollment) {
            $lessonProgress = LessonProgress::query()
                ->where('maHV', $student->maHV)
                ->where('maKH', $course->maKH)
                ->where('maBH', $lesson->maBH)
                ->first();

            $miniTestIds = $lesson->chapter->miniTests->pluck('maMT');

            if ($miniTestIds->isNotEmpty()) {
                $miniTestStats = MiniTestResult::query()
                    ->select('maMT', DB::raw('MAX(diem) as best_score'), DB::raw('COUNT(*) as attempts'))
                    ->where('maHV', $student->maHV)
                    ->whereIn('maMT', $miniTestIds)
                    ->where('is_fully_graded', true)
                    ->groupBy('maMT')
                    ->get()
                    ->keyBy('maMT');
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Lấy bài học thành công.',
            'data'    => [
                'lesson' => $this->transformLesson($lesson),
                'course' => $this->transformCourseSlim($course),
                'chapter'=> $this->transformChapter($lesson),
                'materials' => $lesson->materials->map(function ($material) {
                    return [
                        'id'          => $material->maTL,
                        'title'       => $material->tenTL,
                        'type'        => $material->loai,
                        'size'        => $material->kichThuoc,
                        'description' => $material->moTa,
                        'mime_type'   => $material->mime_type,
                        'download_url'=> $material->public_url,
                    ];
                })->values(),
                'mini_tests' => $lesson->chapter->miniTests->map(function ($miniTest) use ($miniTestStats) {
                    $stat = $miniTestStats->get($miniTest->maMT);

                    return [
                        'id'          => $miniTest->maMT,
                        'title'       => $miniTest->title ?? null,
                        'order'       => $miniTest->thuTu,
                        'skill'       => $miniTest->skill_type,
                        'time_limit'  => $miniTest->time_limit_min,
                        'attempts_allowed' => $miniTest->attempts_allowed,
                        'best_score'  => $stat?->best_score !== null ? (float) $stat->best_score : null,
                        'attempts_used' => $stat->attempts ?? 0,
                    ];
                })->values(),
                'progress' => $this->formatProgress($lessonProgress),
                'permissions' => [
                    'is_enrolled' => $enrollment !== null,
                    'can_access'  => $canAccess,
                    'is_preview'  => $isPreviewLesson,
                    'discussions_total' => $lesson->discussions_count,
                ],
            ],
        ]);
    }

    protected function transformLesson(Lesson $lesson): array
    {
        return [
            'id'          => $lesson->maBH,
            'title'       => $lesson->tieuDe,
            'description' => $lesson->moTa,
            'order'       => $lesson->thuTu,
            'type'        => $lesson->loai,
        ];
    }

    protected function transformChapter(Lesson $lesson): array
    {
        $chapter = $lesson->chapter;

        return [
            'id'            => $chapter->maChuong,
            'title'         => $chapter->tenChuong,
            'order'         => $chapter->thuTu,
            'lessons_count' => $chapter->lessons->count(),
        ];
    }

    protected function transformCourseSlim($course): array
    {
        return [
            'id'           => $course->maKH,
            'title'        => $course->tenKH,
            'slug'         => $course->slug,
            'cover_image'  => $course->cover_image_url,
            'lessons_total'=> $course->lessons_count ?? $course->lessons->count(),
            'price'        => [
                'original' => $course->original_price,
                'sale'     => $course->sale_price,
                'currency' => 'VND',
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
                        ];
                    })->values(),
                ];
            })->values(),
        ];
    }

    protected function isPreviewLesson(Lesson $lesson, $course): bool
    {
        $course->loadMissing('chapters.lessons');

        $firstChapter = $course->chapters->sortBy('thuTu')->first();
        $firstLesson = $firstChapter?->lessons?->sortBy('thuTu')->first();

        return $firstLesson && $lesson->maBH === $firstLesson->maBH;
    }

}
