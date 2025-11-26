<?php

namespace App\Http\Controllers\API\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\MiniTestResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    public function overview(Request $request): JsonResponse
    {
        $user = $request->user()->loadMissing('student');

        if (! $user->student) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tài khoản chưa có hồ sơ học viên.',
            ], 403);
        }

        $enrollments = Enrollment::query()
            ->with([
                'course' => function ($query) {
                    $query->with([
                        'teacher:maND,hoTen',
                        'category:maDanhMuc,tenDanhMuc',
                        'chapters.lessons',
                    ]);
                },
                'lastLesson:maBH,tieuDe',
            ])
            ->where('maHV', $user->student->maHV)
            ->where('trangThai', 'ACTIVE')
            ->orderByDesc('activated_at')
            ->orderByDesc('created_at')
            ->get();

        if ($enrollments->isEmpty()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Không có dữ liệu tiến độ.',
                'data'    => [
                    'summary' => [
                        'total_courses'        => 0,
                        'average_progress'     => null,
                        'total_learning_hours' => 0,
                    ],
                    'courses' => [],
                ],
            ]);
        }

        $courseIds = $enrollments->pluck('maKH')->unique()->values();

        $lessonProgressByCourse = LessonProgress::query()
            ->where('maHV', $user->student->maHV)
            ->whereIn('maKH', $courseIds)
            ->get()
            ->groupBy('maKH');

        $bestMiniTests = MiniTestResult::query()
            ->select('maKH', 'maMT', DB::raw('MAX(diem) as best_score'))
            ->where('maHV', $user->student->maHV)
            ->whereIn('maKH', $courseIds)
            ->where('is_fully_graded', true)
            ->groupBy('maKH', 'maMT')
            ->get()
            ->groupBy('maKH');

        $courseSnapshots = [];
        $progressAccumulator = [];
        $totalLearningSeconds = 0;

        foreach ($enrollments as $enrollment) {
            $lessonProgressCollection = $lessonProgressByCourse->get($enrollment->maKH, collect());
            $miniTestResults = $bestMiniTests->get($enrollment->maKH, collect());

            $snapshot = $this->buildCourseProgressSnapshot(
                $enrollment,
                $lessonProgressCollection,
                $miniTestResults
            );

            $courseSnapshots[] = $snapshot;

            if ($enrollment->progress_percent !== null) {
                $progressAccumulator[] = (int) $enrollment->progress_percent;
            }

            $totalLearningSeconds += $snapshot['metrics']['total_learning_seconds'];
        }

        $summary = [
            'total_courses'        => count($courseSnapshots),
            'average_progress'     => ! empty($progressAccumulator)
                ? (int) round(array_sum($progressAccumulator) / count($progressAccumulator))
                : null,
            'total_learning_hours' => $this->formatSecondsToHours($totalLearningSeconds),
        ];

        return response()->json([
            'status'  => 'success',
            'message' => 'Lấy dữ liệu tiến độ thành công.',
            'data'    => [
                'summary' => $summary,
                'courses' => $courseSnapshots,
            ],
        ]);
    }

    protected function buildCourseProgressSnapshot(
        Enrollment $enrollment,
        Collection $lessonProgressCollection,
        Collection $miniTestResults
    ): array {
        $course = $enrollment->course;
        $lessonProgressCollection = $lessonProgressCollection ?? collect();
        $miniTestResults = $miniTestResults ?? collect();

        $lessonsTotal = $course->chapters
            ? $course->chapters->flatMap(fn ($chapter) => $chapter->lessons)->count()
            : 0;

        $lessonsCompleted = $lessonProgressCollection
            ->where('trangThai', 'COMPLETED')
            ->count();

        $learningSeconds = $lessonProgressCollection->reduce(function ($carry, $progress) {
            $progressSeconds = (int) ($progress->video_progress_seconds ?? 0);

            if ($progressSeconds <= 0) {
                $progressSeconds = (int) ($progress->thoiGianHoc ?? 0);
            }

            return $carry + $progressSeconds;
        }, 0);
        $lastLesson = $enrollment->lastLesson;

        return [
            'course' => [
                'id'      => $course->maKH,
                'title'   => $course->tenKH,
                'slug'    => $course->slug,
                'cover'   => $course->cover_image_url,
                'category'=> $course->category ? [
                    'id'   => $course->category->maDanhMuc,
                    'name' => $course->category->tenDanhMuc,
                ] : null,
                'teacher' => $course->teacher ? [
                    'id'   => $course->teacher->maND,
                    'name' => $course->teacher->hoTen,
                ] : null,
            ],
            'metrics' => [
                'overall_percent' => (int) ($enrollment->progress_percent ?? 0),
                'video_percent'   => (int) ($enrollment->video_progress_percent ?? 0),
                'lessons_total'   => $lessonsTotal,
                'lessons_done'    => $lessonsCompleted,
                'total_learning_seconds' => $learningSeconds,
                'total_learning_readable'=> $this->formatSecondsReadable($learningSeconds),
                'best_minitest_score'    => $miniTestResults->isNotEmpty()
                    ? round($miniTestResults->max('best_score'), 2)
                    : null,
            ],
            'timeline' => [
                'enrolled_at'  => optional($enrollment->created_at)->toIso8601String(),
                'activated_at' => optional($enrollment->activated_at)->toIso8601String(),
                'expires_at'   => optional($enrollment->expires_at)->toIso8601String(),
            ],
            'progress' => [
                'last_lesson' => $lastLesson ? [
                    'id'    => $lastLesson->maBH,
                    'title' => $lastLesson->tieuDe,
                ] : null,
                'status'      => $enrollment->trangThai,
            ],
        ];
    }

    protected function formatSecondsReadable(int $seconds): string
    {
        if ($seconds <= 0) {
            return '0s';
        }

        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $secs = $seconds % 60;

        $parts = [];

        if ($hours > 0) {
            $parts[] = $hours . 'h';
        }

        if ($hours > 0 || $minutes > 0) {
            $parts[] = ($hours > 0 ? str_pad($minutes, 2, '0', STR_PAD_LEFT) : $minutes) . 'm';
        }

        $parts[] = str_pad($secs, 2, '0', STR_PAD_LEFT) . 's';

        return implode(' ', $parts);
    }

    protected function formatSecondsToHours(int $seconds): float
    {
        if ($seconds <= 0) {
            return 0.0;
        }

        return round($seconds / 3600, 2);
    }
}
