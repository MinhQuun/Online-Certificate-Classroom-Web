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
            ];
        }

        $assignments = (int) DB::table('BAIHOC as lessons')
            ->join('CHUONG as chapters', 'lessons.maChuong', '=', 'chapters.maChuong')
            ->whereIn('chapters.maKH', $courseIds)
            ->where('lessons.loai', 'assignment')
            ->count();

        $examsTotal = (int) DB::table('TEST')
            ->whereIn('maKH', $courseIds)
            ->count();

        $examsWithMaterials = (int) DB::table('TEST')
            ->join('TEST_TAILIEU', 'TEST.maTest', '=', 'TEST_TAILIEU.maTest')
            ->whereIn('TEST.maKH', $courseIds)
            ->distinct('TEST.maTest')
            ->count('TEST.maTest');

        $lowProgress = (int) DB::table('HOCVIEN_KHOAHOC')
            ->whereIn('maKH', $courseIds)
            ->where('progress_percent', '<', 40)
            ->count();

        return [
            'assignments_pending' => $assignments,
            'exams_pending'       => max(0, $examsTotal - $examsWithMaterials),
            'low_progress'        => $lowProgress,
        ];
    }
}

