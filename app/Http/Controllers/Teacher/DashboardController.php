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
            $stats['lectures'] = (int) DB::table('baihoc as lessons')
                ->join('chuong as chapters', 'lessons.maChuong', '=', 'chapters.maChuong')
                ->whereIn('chapters.maKH', $courseIds)
                ->count();

            $stats['students'] = (int) DB::table('hocvien_khoahoc')
                ->whereIn('maKH', $courseIds)
                ->distinct('maHV')
                ->count('maHV');

            $assignmentQuery = DB::table('baihoc as lessons')
                ->join('chuong as chapters', 'lessons.maChuong', '=', 'chapters.maChuong')
                ->whereIn('chapters.maKH', $courseIds)
                ->where('lessons.loai', 'assignment');

            $stats['assignments_total'] = (int) $assignmentQuery->count();
            $stats['assignments_pending'] = $stats['assignments_total'];

            $examQuery = DB::table('chuong_minitest')
                ->whereIn('maKH', $courseIds);

            $stats['exams_total'] = (int) $examQuery->count();
            $stats['exams_upcoming'] = (int) $examQuery->where('is_active', 1)->count();
            $stats['exams_pending'] = max(0, $stats['exams_total'] - $stats['exams_upcoming']);

            $miniTestQuery = DB::table('chuong_minitest')
                ->whereIn('maKH', $courseIds);

            $stats['minitests_total'] = (int) $miniTestQuery->count();
            $stats['minitests_active'] = (int) $miniTestQuery->where('is_active', 1)->count();

            $stats['low_progress_students'] = (int) DB::table('hocvien_khoahoc')
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
            'lectures'         => 'Quáº£n lÃ½ bÃ i giáº£ng Ä‘ang Ä‘Æ°á»£c phÃ¡t triá»ƒn.',
            'videos'           => 'Quáº£n lÃ½ video Ä‘ang Ä‘Æ°á»£c phÃ¡t triá»ƒn.',
            'documents'        => 'Quáº£n lÃ½ tÃ i liá»‡u Ä‘ang Ä‘Æ°á»£c phÃ¡t triá»ƒn.',
            'assignments'      => 'Theo dÃµi bÃ i táº­p Ä‘ang Ä‘Æ°á»£c phÃ¡t triá»ƒn.',
            'students'         => 'Danh sÃ¡ch há»c viÃªn Ä‘ang Ä‘Æ°á»£c phÃ¡t triá»ƒn.',
            'progress'         => 'Thá»‘ng kÃª tiáº¿n Ä‘á»™ Ä‘ang Ä‘Æ°á»£c hoÃ n thiá»‡n.',
            'exams'            => 'Quáº£n lÃ½ ká»³ thi Ä‘ang Ä‘Æ°á»£c triá»ƒn khai.',
            'reports-progress' => 'BÃ¡o cÃ¡o tiáº¿n Ä‘á»™ Ä‘ang Ä‘Æ°á»£c hoÃ n thiá»‡n.',
            'reports-exams'    => 'BÃ¡o cÃ¡o ká»³ thi Ä‘ang Ä‘Æ°á»£c bá»• sung.',
        ];

        $message = $messages[$section] ?? 'TÃ­nh nÄƒng nÃ y Ä‘ang Ä‘Æ°á»£c phÃ¡t triá»ƒn.';

        return redirect()
            ->route('teacher.dashboard')
            ->with('info', $message);
    }

    protected function coursesOwnedBy(?int $teacherId): Collection
    {
        if (!$teacherId) {
            return collect();
        }

        return DB::table('khoahoc')
            ->where('maND', $teacherId)
            ->pluck('maKH');
    }
}
