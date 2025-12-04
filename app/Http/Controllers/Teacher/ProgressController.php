<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Teacher\LoadsTeacherContext;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Teacher-facing progress dashboard is monitor-only (read-only).
 */
class ProgressController extends Controller
{
    use LoadsTeacherContext;

    public function index(Request $request): View
    {
        $teacherId = Auth::id() ?? 0;
        $courses = $this->loadTeacherCourses($teacherId);
        $courseSummaries = $this->buildCourseSummaries($courses);

        return view('Teacher.progress', [
            'courses'         => $courses,
            'courseSummaries' => $courseSummaries,
            'activeCourse'    => null,
            'enrollments'     => collect(),
            'chapterProgress' => [],
            'filters'         => [
                'status' => strtoupper($request->query('status', '')),
                'search' => trim($request->query('search', '')),
            ],
            'metrics'      => $this->emptyMetrics(),
            'statusLabels' => $this->statusLabels(),
            'badges'       => $this->teacherSidebarBadges($teacherId),
        ]);
    }

    public function show(Request $request, Course $course): View
    {
        $teacherId = Auth::id() ?? 0;

        abort_unless($course->maND === $teacherId, 404);

        $course->loadMissing(['chapters.lessons' => fn ($query) => $query->orderBy('thuTu')]);

        $courses = $this->loadTeacherCourses($teacherId);
        $courseSummaries = $this->buildCourseSummaries($courses);

        $filters = [
            'status' => strtoupper($request->query('status', '')),
            'search' => trim($request->query('search', '')),
        ];

        [$enrollments, $metrics, $chapterProgress] = $this->buildCourseDetails($course, $filters);

        return view('Teacher.progress', [
            'courses'         => $courses,
            'courseSummaries' => $courseSummaries,
            'activeCourse'    => $course,
            'enrollments'     => $enrollments,
            'chapterProgress' => $chapterProgress,
            'filters'         => $filters,
            'metrics'         => $metrics,
            'statusLabels'    => $this->statusLabels(),
            'badges'          => $this->teacherSidebarBadges($teacherId),
        ]);
    }

    private function loadTeacherCourses(int $teacherId): Collection
    {
        return Course::with(['chapters.lessons' => fn ($query) => $query->orderBy('thuTu')])
            ->where('maND', $teacherId)
            ->orderBy('tenKH')
            ->get();
    }

    private function buildCourseSummaries(Collection $courses): array
    {
        $courseIds = $courses->pluck('maKH');

        if ($courseIds->isEmpty()) {
            return [];
        }

        $enrollments = DB::table('hocvien_khoahoc')
            ->whereIn('maKH', $courseIds)
            ->select([
                'maKH',
                'progress_percent',
            ])
            ->get()
            ->groupBy('maKH');

        return $courses->map(function (Course $course) use ($enrollments) {
            $courseEnrollments = $enrollments->get($course->maKH, collect());
            $total = $courseEnrollments->count();

            $completed = $courseEnrollments->filter(fn ($row) => (int) ($row->progress_percent ?? 0) >= 100)->count();
            $inProgress = $courseEnrollments->filter(function ($row) {
                $value = (int) ($row->progress_percent ?? 0);
                return $value > 0 && $value < 100;
            })->count();
            $notStarted = $courseEnrollments->filter(fn ($row) => (int) ($row->progress_percent ?? 0) <= 0)->count();

            return [
                'id'           => $course->maKH,
                'name'         => $course->tenKH,
                'total'        => $total,
                'average'      => $total > 0 ? round($courseEnrollments->avg('progress_percent'), 1) : 0,
                'completed'    => $completed,
                'in_progress'  => $inProgress,
                'not_started'  => $notStarted,
            ];
        })->values()->all();
    }

    private function buildCourseDetails(Course $course, array $filters): array
    {
        $query = DB::table('HOCVIEN_KHOAHOC as hk')
            ->join('hocvien as hv', 'hk.maHV', '=', 'hv.maHV')
            ->leftJoin('nguoidung as nd', 'hv.maND', '=', 'nd.maND')
            ->leftJoin('baihoc as lessons', 'hk.last_lesson_id', '=', 'lessons.maBH')
            ->where('hk.maKH', $course->maKH);

        $statusFilter = $filters['status'] ?? '';
        if ($statusFilter && in_array($statusFilter, ['PENDING', 'ACTIVE', 'EXPIRED'], true)) {
            $query->where('hk.trangThai', $statusFilter);
        }

        $searchKeyword = $filters['search'] ?? '';
        if ($searchKeyword !== '') {
            $keyword = '%' . $searchKeyword . '%';
            $query->where(function ($builder) use ($keyword) {
                $builder->where('hv.hoTen', 'like', $keyword)
                    ->orWhere('nd.email', 'like', $keyword);
            });
        }

        $enrollments = $query
            ->select([
                'hk.maHV',
                'hk.maKH',
                'hk.progress_percent',
                'hk.trangThai',
                'hk.ngayNhapHoc',
                'hk.updated_at',
                'hv.hoTen as student_name',
                'nd.email',
                'lessons.tieuDe as last_lesson_title',
                'lessons.thuTu as last_lesson_order',
            ])
            ->orderBy('hv.hoTen')
            ->get()
            ->map(function ($item) {
                $item->joined_at = $item->ngayNhapHoc
                    ? Carbon::parse($item->ngayNhapHoc)->format('d/m/Y')
                    : null;
                $item->updated_for_humans = $item->updated_at
                    ? Carbon::parse($item->updated_at)->diffForHumans()
                    : null;

                return $item;
            });

        $metrics = $this->summarizeMetrics($enrollments);

        $chapterProgress = $this->buildChapterProgress($course, $enrollments);

        return [$enrollments, $metrics, $chapterProgress];
    }

    private function buildChapterProgress(Course $course, Collection $enrollments): array
    {
        if ($enrollments->isEmpty() || !$course->chapters) {
            return [];
        }

        $chapterIds = $course->chapters->pluck('maChuong');
        $chapterLessons = $course->chapters
            ->mapWithKeys(fn ($chapter) => [$chapter->maChuong => max(0, $chapter->lessons->count())]);

        $progressRows = DB::table('tiendo_hoctap as tp')
            ->join('baihoc as bh', 'tp.maBH', '=', 'bh.maBH')
            ->where('tp.maKH', $course->maKH)
            ->whereIn('tp.maHV', $enrollments->pluck('maHV'))
            ->whereIn('bh.maChuong', $chapterIds)
            ->select('tp.maHV', 'bh.maChuong')
            ->selectRaw("SUM(CASE WHEN tp.trangThai = 'COMPLETED' THEN 1 ELSE 0 END) as completed_lessons")
            ->selectRaw('COUNT(tp.maBH) as tracked_lessons')
            ->groupBy('tp.maHV', 'bh.maChuong')
            ->get()
            ->groupBy('maHV');

        $chapterProgress = [];

        foreach ($enrollments as $enrollment) {
            $perChapter = [];

            foreach ($course->chapters as $chapter) {
                $chapterRow = $progressRows->get($enrollment->maHV, collect())
                    ->firstWhere('maChuong', $chapter->maChuong);

                $totalLessons = (int) ($chapterLessons[$chapter->maChuong] ?? 0);
                $trackedLessons = (int) ($chapterRow->tracked_lessons ?? 0);
                $completedLessons = (int) ($chapterRow->completed_lessons ?? 0);
                $percent = $totalLessons > 0
                    ? (int) round(($completedLessons / $totalLessons) * 100)
                    : null;

                $status = 'no-lessons';
                if ($totalLessons > 0) {
                    $status = match (true) {
                        $completedLessons >= $totalLessons => 'completed',
                        $trackedLessons > 0 || $completedLessons > 0 => 'in-progress',
                        default => 'not-started',
                    };
                }

                $perChapter[] = [
                    'id'                 => $chapter->maChuong,
                    'title'              => $chapter->tenChuong,
                    'order'              => $chapter->thuTu,
                    'status'             => $status,
                    'percent'            => $percent !== null ? min(100, max(0, $percent)) : null,
                    'total_lessons'      => $totalLessons,
                    'completed_lessons'  => $completedLessons,
                ];
            }

            $chapterProgress[$enrollment->maHV] = $perChapter;
        }

        return $chapterProgress;
    }

    private function summarizeMetrics(Collection $enrollments): array
    {
        if ($enrollments->isEmpty()) {
            return $this->emptyMetrics();
        }

        $total = $enrollments->count();

        $completed = $enrollments->filter(fn ($row) => (int) ($row->progress_percent ?? 0) >= 100)->count();
        $inProgress = $enrollments->filter(function ($row) {
            $value = (int) ($row->progress_percent ?? 0);
            return $value > 0 && $value < 100;
        })->count();
        $notStarted = $enrollments->filter(fn ($row) => (int) ($row->progress_percent ?? 0) <= 0)->count();
        $atRisk = $enrollments->filter(fn ($row) => (int) ($row->progress_percent ?? 0) < 40)->count();

        return [
            'total'        => $total,
            'average'      => $total > 0 ? round($enrollments->avg('progress_percent'), 1) : 0,
            'completed'    => $completed,
            'in_progress'  => $inProgress,
            'not_started'  => $notStarted,
            'at_risk'      => $atRisk,
        ];
    }

    private function emptyMetrics(): array
    {
        return [
            'total'        => 0,
            'average'      => 0,
            'completed'    => 0,
            'in_progress'  => 0,
            'not_started'  => 0,
            'at_risk'      => 0,
        ];
    }

    private function statusLabels(): array
    {
        return [
            'PENDING' => 'Chờ kích hoạt',
            'ACTIVE'  => 'Đang học',
            'EXPIRED' => 'Hết hạn',
        ];
    }
}
