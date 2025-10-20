<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
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
        ];

        $badges = [
            'assignments_pending' => 0,
            'exams_pending'       => 0,
            'low_progress'        => 0,
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
            $badges['assignments_pending'] = $stats['assignments_pending'];

            $examQuery = DB::table('CHUONG_MINITEST')
                ->whereIn('maKH', $courseIds);

            $stats['exams_total'] = (int) $examQuery->count();
            $stats['exams_upcoming'] = (int) $examQuery->where('is_active', 1)->count();
            $badges['exams_pending'] = max(0, $stats['exams_total'] - $stats['exams_upcoming']);

            $badges['low_progress'] = (int) DB::table('HOCVIEN_KHOAHOC')
                ->whereIn('maKH', $courseIds)
                ->where('progress_percent', '<', 40)
                ->count();
        }

        return view('Teacher.dashboard', [
            'teacher' => $teacher,
            'stats'   => $stats,
            'badges'  => $badges,
        ]);
    }

    public function placeholder(string $section)
    {
        $messages = [
            'lectures'         => 'Quan ly bai giang dang duoc phat trien.',
            'videos'           => 'Quan ly video dang duoc phat trien.',
            'documents'        => 'Quan ly tai lieu dang duoc phat trien.',
            'assignments'      => 'Theo doi bai tap dang duoc phat trien.',
            'students'         => 'Danh sach hoc vien dang duoc phat trien.',
            'progress'         => 'Thong ke tien do dang duoc hoan thien.',
            'exams'            => 'Quan ly ky thi dang duoc trien khai.',
            'reports-progress' => 'Bao cao tien do dang duoc hoan thien.',
            'reports-exams'    => 'Bao cao ky thi dang duoc bo sung.',
        ];

        $message = $messages[$section] ?? 'Tinh nang nay dang duoc phat trien.';

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

