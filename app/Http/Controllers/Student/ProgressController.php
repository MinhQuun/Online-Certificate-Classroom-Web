<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\MiniTestResult;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        if (!$userId) {
            return redirect()->route('login', ['redirect' => $request->fullUrl()]);
        }

        $student = DB::table('hocvien')->where('maND', $userId)->first();

        if (!$student) {
            return redirect()
                ->route('student.courses.index')
                ->with('toast', [
                    'type' => 'warning',
                    'message' => 'Tài khoản chưa gắn với hồ sơ học viên. Vui lòng liên hệ quản trị viên.',
                ]);
        }

        $enrollments = Enrollment::with([
            'course' => function ($courseQuery) {
                $courseQuery->with([
                    'category',
                    'teacher',
                    'chapters' => function ($chapterQuery) {
                        $chapterQuery->orderBy('thuTu')->with([
                            'lessons' => fn($lessonQuery) => $lessonQuery->orderBy('thuTu'),
                            'miniTests' => fn($miniTestQuery) => $miniTestQuery
                                ->visibleToStudents()
                                ->orderBy('thuTu'),
                        ]);
                    },
                ]);
            },
            'lastLesson',
        ])
            ->where('maHV', $student->maHV)
            ->where('trangThai', 'ACTIVE')          // Chỉ ACTIVE
            ->whereNotNull('activated_at')
            ->orderByDesc('activated_at')
            ->orderByDesc('created_at')
            ->get();

        $courseIds = $enrollments->pluck('maKH')->unique()->values();

        if ($courseIds->isEmpty()) {
            return view('Student.lesson-progress', [
                'student' => $student,
                'enrollments' => collect(),
                'snapshots' => [],
                'overviewMetrics' => [
                    'totalCourses' => 0,
                    'averageProgress' => null,
                    'totalLearningSeconds' => 0,
                    'totalLearningReadable' => '0m',
                ],
            ]);
        }

        $lessonBaseStats = $this->fetchLessonBaseStats($courseIds);
        $lessonAggregates = $this->fetchLessonAggregates($student->maHV, $courseIds);
        $lessonProgressByLesson = LessonProgress::where('maHV', $student->maHV)
            ->whereIn('maKH', $courseIds)
            ->get()
            ->keyBy('maBH');

        $miniTestMeta = $this->fetchMiniTestMeta($courseIds);
        $bestMiniTestResults = $this->fetchBestMiniTestResults($student->maHV, $courseIds);
        $miniTestResultsByCourse = $bestMiniTestResults->groupBy('maKH');
        $miniTestResultsByMiniTest = $bestMiniTestResults->keyBy('maMT');

        $snapshots = [];
        $overallPercents = [];
        $totalLearningSeconds = 0;

        foreach ($enrollments as $enrollment) {
            $snapshot = $this->buildCourseSnapshot(
                $enrollment,
                $lessonBaseStats,
                $lessonAggregates,
                $lessonProgressByLesson,
                $miniTestMeta,
                $miniTestResultsByCourse,
                $miniTestResultsByMiniTest
            );
            $snapshots[] = $snapshot;

            if ($snapshot['metrics']['overall_percent'] !== null) {
                $overallPercents[] = $snapshot['metrics']['overall_percent'];
            }

            $totalLearningSeconds += $snapshot['metrics']['total_learning_seconds'];
        }

        $overviewMetrics = [
            'totalCourses' => $enrollments->count(),
            'averageProgress' => !empty($overallPercents)
                ? (int) round(array_sum($overallPercents) / count($overallPercents))
                : null,
            'totalLearningSeconds' => $totalLearningSeconds,
            'totalLearningReadable' => $this->formatSeconds($totalLearningSeconds),
        ];

        return view('Student.lesson-progress', [
            'student' => $student,
            'enrollments' => $enrollments,
            'snapshots' => $snapshots,
            'overviewMetrics' => $overviewMetrics,
        ]);
    }

    private function fetchLessonBaseStats(Collection $courseIds): Collection
    {
        if ($courseIds->isEmpty()) {
            return collect();
        }

        return DB::table('chuong as ch')
            ->join('baihoc as bh', 'bh.maChuong', '=', 'ch.maChuong')
            ->whereIn('ch.maKH', $courseIds)
            ->groupBy('ch.maKH')
            ->select('ch.maKH')
            ->selectRaw('COUNT(bh.maBH) as total_lessons')
            ->selectRaw("SUM(CASE WHEN bh.loai = 'video' THEN 1 ELSE 0 END) as total_video_lessons")
            ->get()
            ->keyBy('maKH');
    }

    private function fetchLessonAggregates(int $studentId, Collection $courseIds): Collection
    {
        if ($courseIds->isEmpty()) {
            return collect();
        }

        return LessonProgress::query()
            ->select('maKH')
            ->selectRaw('COUNT(*) as tracked_lessons')
            ->selectRaw("SUM(CASE WHEN trangThai = 'COMPLETED' THEN 1 ELSE 0 END) as completed_lessons")
            ->selectRaw('SUM(CASE WHEN is_video_completed = 1 THEN 1 ELSE 0 END) as completed_videos')
            ->selectRaw('SUM(COALESCE(thoiGianHoc, 0)) as total_learning_seconds')
            ->selectRaw('SUM(COALESCE(soLanXem, 0)) as total_lesson_views')
            ->selectRaw('SUM(COALESCE(video_progress_seconds, 0)) as total_video_progress')
            ->selectRaw('SUM(COALESCE(video_duration_seconds, 0)) as total_video_duration')
            ->selectRaw('MAX(lanXemCuoi) as latest_view_at')
            ->selectRaw('MAX(completed_at) as latest_completed_at')
            ->where('maHV', $studentId)
            ->whereIn('maKH', $courseIds)
            ->groupBy('maKH')
            ->get()
            ->keyBy('maKH');
    }

    private function fetchMiniTestMeta(Collection $courseIds): Collection
    {
        if ($courseIds->isEmpty()) {
            return collect();
        }

        return DB::table('chuong_minitest as mt')
            ->select('mt.maKH')
            ->selectRaw('COUNT(DISTINCT mt.maMT) as total_minitests')
            ->where('mt.is_active', 1)
            ->where('mt.is_published', 1)
            ->whereIn('mt.maKH', $courseIds)
            ->groupBy('mt.maKH')
            ->get()
            ->keyBy('maKH');
    }

    private function fetchBestMiniTestResults(int $studentId, Collection $courseIds): Collection
    {
        if ($courseIds->isEmpty()) {
            return collect();
        }

        return MiniTestResult::query()
            ->select('maMT', 'maKH')
            ->selectRaw('MAX(diem) as best_score')
            ->selectRaw('MAX(nop_luc) as last_attempt_at')
            ->where('maHV', $studentId)
            ->whereIn('maKH', $courseIds)
            ->groupBy('maMT', 'maKH')
            ->get();
    }

    private function buildCourseSnapshot(
        Enrollment $enrollment,
        Collection $lessonBaseStats,
        Collection $lessonAggregates,
        Collection $lessonProgressByLesson,
        Collection $miniTestMeta,
        Collection $miniTestResultsByCourse,
        Collection $miniTestResultsByMiniTest
    ): array {
        $course = $enrollment->course;
        $courseId = $course->maKH;

        $base = $lessonBaseStats->get($courseId);
        $aggregate = $lessonAggregates->get($courseId);

        $totalLessons = (int) ($base->total_lessons ?? $course->chapters->sum(fn($chapter) => $chapter->lessons->count()));
        $totalVideoLessons = (int) ($base->total_video_lessons ?? $course->chapters->sum(fn($chapter) => $chapter->lessons->where('loai', 'video')->count()));

        $completedLessons = (int) ($aggregate->completed_lessons ?? 0);
        $completedVideos = (int) ($aggregate->completed_videos ?? 0);
        $totalLearningSeconds = (int) ($aggregate->total_learning_seconds ?? 0);
        $totalViews = (int) ($aggregate->total_lesson_views ?? 0);
        $videoProgressSeconds = (int) ($aggregate->total_video_progress ?? 0);
        $videoDurationSeconds = (int) ($aggregate->total_video_duration ?? 0);
        $displayLearningSeconds = $videoProgressSeconds > 0
            ? $videoProgressSeconds
            : $totalLearningSeconds;

        $lessonPercent = $totalLessons > 0
            ? (int) round(($completedLessons / $totalLessons) * 100)
            : null;

        $videoPercent = null;
        if ($totalVideoLessons > 0) {
            $videoPercent = (int) round(($completedVideos / $totalVideoLessons) * 100);
        } elseif ($videoDurationSeconds > 0) {
            $videoPercent = (int) round(($videoProgressSeconds / $videoDurationSeconds) * 100);
        }
        if ($videoPercent !== null) {
            $videoPercent = min(100, $videoPercent);
        }

        $miniTestSummary = $miniTestMeta->get($courseId);
        $totalMiniTests = (int) ($miniTestSummary->total_minitests ?? $course->chapters->sum(fn($chapter) => $chapter->miniTests->count()));
        $miniResults = $miniTestResultsByCourse->get($courseId, collect());
        $completedMiniTests = $miniResults instanceof Collection ? $miniResults->count() : 0;

        $miniPercent = $totalMiniTests > 0
            ? (int) round(($completedMiniTests / $totalMiniTests) * 100)
            : null;

        $avgMiniScore = ($completedMiniTests > 0 && $miniResults instanceof Collection)
            ? round($miniResults->avg('best_score'), 2)
            : null;

        $weights = [];
        if ($videoPercent !== null) {
            $weights[] = ['weight' => 0.65, 'value' => $videoPercent];
        } elseif ($lessonPercent !== null) {
            $weights[] = ['weight' => 0.65, 'value' => $lessonPercent];
        }
        if ($miniPercent !== null) {
            $weights[] = ['weight' => 0.35, 'value' => $miniPercent];
        }

        $weightSum = array_sum(array_column($weights, 'weight'));
        $overallPercent = $weightSum > 0
            ? (int) round(array_reduce($weights, function ($carry, $item) {
                return $carry + ($item['value'] * $item['weight']);
            }, 0) / $weightSum)
            : ($lessonPercent ?? null);

        $latestLessonView = $this->toCarbon($aggregate->latest_view_at ?? null);
        $latestLessonCompleted = $this->toCarbon($aggregate->latest_completed_at ?? null);
        $latestMiniTest = null;
        if ($miniResults instanceof Collection && $miniResults->isNotEmpty()) {
            $latestMiniTestValue = $miniResults->max('last_attempt_at');
            $latestMiniTest = $this->toCarbon($latestMiniTestValue);
        }

        $latestActivity = collect([
            $latestLessonCompleted,
            $latestLessonView,
            $latestMiniTest,
            $enrollment->updated_at,
        ])
            ->filter()
            ->sortByDesc(fn($value) => $value instanceof Carbon ? $value->timestamp : 0)
            ->first();

        $nextLesson = $this->resolveNextLesson($course, $lessonProgressByLesson);
        $chapterSummaries = $this->buildChapterSummaries($course, $lessonProgressByLesson, $miniTestResultsByMiniTest);

        return [
            'course' => $course,
            'enrollment' => $enrollment,
            'metrics' => [
                'overall_percent' => $overallPercent !== null ? min(100, max(0, $overallPercent)) : null,
                'lesson_percent' => $lessonPercent !== null ? min(100, max(0, $lessonPercent)) : null,
                'video_percent' => $videoPercent !== null ? min(100, max(0, $videoPercent)) : null,
                'mini_percent' => $miniPercent !== null ? min(100, max(0, $miniPercent)) : null,
                'avg_mini_score' => $avgMiniScore,
                'total_lessons' => $totalLessons,
                'completed_lessons' => $completedLessons,
                'total_videos' => $totalVideoLessons,
                'completed_videos' => $completedVideos,
                'total_minitests' => $totalMiniTests,
                'completed_minitests' => $completedMiniTests,
                'total_learning_seconds' => $displayLearningSeconds,
                'total_learning_readable' => $this->formatSeconds($displayLearningSeconds),
                'total_lesson_views' => $totalViews,
                'latest_activity' => $latestActivity,
                'latest_activity_for_humans' => $latestActivity ? $latestActivity->diffForHumans() : null,
                'latest_minitest_at' => $latestMiniTest,
            ],
            'video_progress' => [
                'watched_seconds' => $videoProgressSeconds,
                'duration_seconds' => $videoDurationSeconds,
                'watched_readable' => $this->formatSeconds($videoProgressSeconds),
            ],
            'nextLesson' => $nextLesson,
            'chapters' => $chapterSummaries,
        ];
    }

    private function resolveNextLesson($course, Collection $lessonProgressByLesson): ?array
    {
        $orderedLessons = collect();
        foreach ($course->chapters as $chapter) {
            foreach ($chapter->lessons as $lesson) {
                $orderedLessons->push($lesson);
            }
        }

        if ($orderedLessons->isEmpty()) {
            return null;
        }

        foreach ($orderedLessons as $lesson) {
            $progress = $lessonProgressByLesson->get($lesson->maBH);
            if (!$progress || $progress->trangThai !== 'COMPLETED') {
                return [
                    'lesson' => $lesson,
                    'status' => $progress->trangThai ?? 'NOT_STARTED',
                    'progress' => $progress,
                ];
            }
        }

        $lastLesson = $orderedLessons->last();
        $progress = $lessonProgressByLesson->get($lastLesson->maBH);

        return [
            'lesson' => $lastLesson,
            'status' => $progress->trangThai ?? 'COMPLETED',
            'progress' => $progress,
        ];
    }

    private function buildChapterSummaries($course, Collection $lessonProgressByLesson, Collection $miniTestResultsByMiniTest): array
    {
        $chapters = [];

        foreach ($course->chapters as $chapter) {
            $lessonItems = collect($chapter->lessons)->map(function ($lesson) use ($lessonProgressByLesson) {
                $progress = $lessonProgressByLesson->get($lesson->maBH);

                $percent = null;
                if ($progress && $progress->video_duration_seconds > 0) {
                    $percent = (int) round(($progress->video_progress_seconds / $progress->video_duration_seconds) * 100);
                    $percent = min(100, max(0, $percent));
                }

                return [
                    'id' => $lesson->maBH,
                    'title' => $lesson->tieuDe,
                    'type' => $lesson->loai,
                    'status' => $progress->trangThai ?? 'NOT_STARTED',
                    'percent' => $percent,
                    'is_video_completed' => (bool) ($progress->is_video_completed ?? false),
                    'updated_at' => $progress->updated_at ?? null,
                ];
            });

            $miniTestItems = collect($chapter->miniTests)->map(function ($miniTest) use ($miniTestResultsByMiniTest) {
                $result = $miniTestResultsByMiniTest->get($miniTest->maMT);

                return [
                    'id' => $miniTest->maMT,
                    'title' => $miniTest->title,
                    'max_score' => $miniTest->max_score,
                    'best_score' => $result->best_score ?? null,
                    'last_attempt_at' => $result->last_attempt_at ?? null,
                ];
            });

            $lessonTotal = $lessonItems->count();
            $lessonCompleted = $lessonItems->where('status', 'COMPLETED')->count();

            $chapters[] = [
                'id' => $chapter->maChuong,
                'title' => $chapter->tenChuong,
                'description' => $chapter->moTa,
                'lessons' => $lessonItems,
                'miniTests' => $miniTestItems,
                'lesson_completion_percent' => $lessonTotal > 0
                    ? (int) round(($lessonCompleted / $lessonTotal) * 100)
                    : 0,
                'total_minitests' => $miniTestItems->count(),
                'completed_minitests' => $miniTestItems->filter(fn($item) => $item['best_score'] !== null)->count(),
            ];
        }

        return $chapters;
    }

    private function formatSeconds(int $seconds): string
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

    private function toCarbon($value): ?Carbon
    {
        if (empty($value)) {
            return null;
        }

        return $value instanceof Carbon ? $value : Carbon::parse($value);
    }
}
