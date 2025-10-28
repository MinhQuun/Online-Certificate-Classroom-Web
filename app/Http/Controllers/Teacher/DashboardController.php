<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Teacher\LoadsTeacherContext;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use LoadsTeacherContext;

    public function index()
    {
        $teacher   = Auth::user();
        $teacherId = $teacher?->getKey();

        $courseIds = $this->coursesOwnedBy($teacherId);

        $stats = [
            'courses'             => $courseIds->count(),
            'lectures'            => 0,
            'students'            => 0,
            'assignments_total'   => 0,
            'assignments_pending' => 0,
            'exams_total'         => 0,
            'exams_upcoming'      => 0,
            'minitests_total'     => 0,
            'minitests_active'    => 0,
        ];

        if ($courseIds->isNotEmpty()) {
            $stats['lectures'] = (int) DB::table('BAIHOC as lessons')
                ->join('CHUONG as chapters', 'lessons.maChuong', '=', 'chapters.maChuong')
                ->whereIn('chapters.maKH', $courseIds)
                ->count();

            $stats['students'] = (int) DB::table('HOCVIEN_KHOAHOC')
                ->whereIn('maKH', $courseIds)
                ->distinct('maHV')
                ->count('maHV');

            $assignmentQuery = DB::table('BAIHOC as lessons')
                ->join('CHUONG as chapters', 'lessons.maChuong', '=', 'chapters.maChuong')
                ->whereIn('chapters.maKH', $courseIds)
                ->where('lessons.loai', 'assignment');

            $stats['assignments_total'] = (int) $assignmentQuery->count();
            $stats['assignments_pending'] = $stats['assignments_total'];

            $examQuery = DB::table('CHUONG_MINITEST')
                ->whereIn('maKH', $courseIds);

            $stats['exams_total'] = (int) $examQuery->count();
            $stats['exams_upcoming'] = (int) $examQuery->where('is_active', 1)->count();
            $stats['exams_pending'] = max(0, $stats['exams_total'] - $stats['exams_upcoming']);

            $miniTestQuery = DB::table('CHUONG_MINITEST')
                ->whereIn('maKH', $courseIds);

            $stats['minitests_total'] = (int) $miniTestQuery->count();
            $stats['minitests_active'] = (int) $miniTestQuery->where('is_active', 1)->count();

            $stats['low_progress_students'] = (int) DB::table('HOCVIEN_KHOAHOC')
                ->whereIn('maKH', $courseIds)
                ->where('progress_percent', '<', 40)
                ->count();
        }

        return view('Teacher.dashboard', [
            'teacher' => $teacher,
            'stats'   => $stats,
            'badges'  => $this->teacherSidebarBadges($teacherId ?? 0),
        ]);
    }

    public function placeholder(string $section)
    {
        $messages = [
            'lectures'         => 'Quản lý bài giảng đang được phát triển.',
            'videos'           => 'Quản lý video đang được phát triển.',
            'documents'        => 'Quản lý tài liệu đang được phát triển.',
            'assignments'      => 'Theo dõi bài tập đang được phát triển.',
            'students'         => 'Danh sách học viên đang được phát triển.',
            'progress'         => 'Thống kê tiến độ đang được hoàn thiện.',
            'exams'            => 'Quản lý kỳ thi đang được triển khai.',
            'reports-progress' => 'Báo cáo tiến độ đang được hoàn thiện.',
            'reports-exams'    => 'Báo cáo kỳ thi đang được bổ sung.',
        ];

        $message = $messages[$section] ?? 'Tính năng này đang được phát triển.';

        return redirect()
            ->route('teacher.dashboard')
            ->with('info', $message);
    }

    protected function coursesOwnedBy(?int $teacherId): Collection
    {
        if (!$teacherId) {
            return collect();
        }

        return DB::table('KHOAHOC')
            ->where('maND', $teacherId)
            ->pluck('maKH');
    }
}
