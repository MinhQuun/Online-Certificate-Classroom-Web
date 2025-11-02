<?php

namespace App\Http\Controllers\Teacher;

use App\Models\MiniTest;
use App\Models\MiniTestResult;
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
            'low_progress' => 0,
            'minitests_active' => 0,
            'writing_pending' => 0,
            'speaking_pending' => 0,
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

        $writingPending = MiniTestResult::query()
            ->where('is_fully_graded', false)
            ->whereIn('status', [MiniTestResult::STATUS_SUBMITTED, MiniTestResult::STATUS_EXPIRED])
            ->whereHas('miniTest', function ($query) use ($courseIds) {
                $query->where('skill_type', MiniTest::SKILL_WRITING)
                    ->whereIn('maKH', $courseIds);
            })
            ->count();

        $speakingPending = MiniTestResult::query()
            ->where('is_fully_graded', false)
            ->whereIn('status', [MiniTestResult::STATUS_SUBMITTED, MiniTestResult::STATUS_EXPIRED])
            ->whereHas('miniTest', function ($query) use ($courseIds) {
                $query->where('skill_type', MiniTest::SKILL_SPEAKING)
                    ->whereIn('maKH', $courseIds);
            })
            ->whereHas('studentAnswers', function ($query) {
                $query->whereNotNull('answer_audio_url')
                    ->whereNull('graded_at');
            })
            ->count();

        return [
            'assignments_pending' => $assignments,
            'low_progress' => $lowProgress,
            'minitests_active' => $minitestsActive,
            'writing_pending' => $writingPending,
            'speaking_pending' => $speakingPending,
        ];
    }
}
