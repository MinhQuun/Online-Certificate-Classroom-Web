<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait LoadsTeacherContext
{
    protected function teacherCourseIds(int $teacherId): Collection
    {
        return DB::table('KHOAHOC')
            ->where('maND', $teacherId)
            ->orderBy('tenKH')
            ->pluck('maKH');
    }

    protected function teacherSidebarBadges(int $teacherId): array
    {
        $courseIds = $this->teacherCourseIds($teacherId);

        if ($courseIds->isEmpty()) {
            return [
                'assignments_pending' => 0,
                'exams_pending'       => 0,
                'low_progress'        => 0,
                'minitests_active'    => 0,
            ];
        }

        $assignments = (int) DB::table('BAIHOC as lessons')
            ->join('CHUONG as chapters', 'lessons.maChuong', '=', 'chapters.maChuong')
            ->whereIn('chapters.maKH', $courseIds)
            ->where('lessons.loai', 'assignment')
            ->count();

        $lowProgress = (int) DB::table('HOCVIEN_KHOAHOC')
            ->whereIn('maKH', $courseIds)
            ->where('progress_percent', '<', 40)
            ->count();

        $minitestsActive = (int) DB::table('CHUONG_MINITEST')
            ->whereIn('maKH', $courseIds)
            ->where('is_active', 1)
            ->count();

        return [
            'assignments_pending' => $assignments,
            'low_progress'        => $lowProgress,
            'minitests_active'    => $minitestsActive,
        ];
    }
}

