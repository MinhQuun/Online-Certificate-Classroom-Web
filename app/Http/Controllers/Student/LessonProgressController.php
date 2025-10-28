<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LessonProgressController extends Controller
{
    public function store(Request $request, Lesson $lesson): JsonResponse
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $student = DB::table('HOCVIEN')->where('maND', $userId)->first();
        if (!$student) {
            return response()->json(['message' => 'Khong tim thay ho so hoc vien.'], 403);
        }

        $lesson->loadMissing('chapter');
        $courseId = optional($lesson->chapter)->maKH;
        if (!$courseId) {
            return response()->json(['message' => 'Khong xac dinh duoc khoa hoc.'], 422);
        }

        $enrollment = Enrollment::where('maHV', $student->maHV)
            ->where('maKH', $courseId)
            ->where('trangThai', 'ACTIVE')
            ->first();

        if (!$enrollment) {
            return response()->json(['message' => 'Khoa hoc chua duoc kich hoat.'], 403);
        }

        $validated = $this->validatePayload($request);

        $progressData = DB::transaction(function () use ($validated, $lesson, $student, $courseId, $enrollment) {
            $now = Carbon::now('Asia/Ho_Chi_Minh');
            $progress = LessonProgress::firstOrNew([
                'maHV' => $student->maHV,
                'maKH' => $courseId,
                'maBH' => $lesson->maBH,
            ]);

            if (!$progress->exists) {
                $progress->trangThai = 'NOT_STARTED';
                $progress->thoiGianHoc = 0;
                $progress->soLanXem = 0;
                $progress->video_progress_seconds = 0;
                $progress->video_duration_seconds = 0;
            }

            $event = $validated['event'];
            $currentTime = (int) floor($validated['current_time'] ?? 0);
            $duration = (int) floor($validated['duration'] ?? 0);
            $watchedDelta = (int) floor($validated['watched_delta'] ?? 0);
            $watchedDelta = max(0, min($watchedDelta, 900)); // cap to avoid unrealistic jumps
            $completedFlag = (bool) ($validated['completed'] ?? false);

            if ($event === 'start') {
                $progress->soLanXem = max(0, (int) $progress->soLanXem) + 1;
                $progress->lanXemCuoi = $now;
                if ($progress->trangThai === 'NOT_STARTED') {
                    $progress->trangThai = 'IN_PROGRESS';
                }
            }

            if ($event === 'progress') {
                $progress->lanXemCuoi = $now;
                if ($watchedDelta > 0) {
                    $progress->thoiGianHoc = max(0, (int) $progress->thoiGianHoc) + $watchedDelta;
                }
            }

            if ($duration > 0) {
                $progress->video_duration_seconds = max(
                    (int) $progress->video_duration_seconds,
                    $duration
                );
            }

            if ($currentTime > 0) {
                $progress->video_progress_seconds = max(
                    (int) $progress->video_progress_seconds,
                    min($currentTime, max($duration, (int) $progress->video_duration_seconds))
                );
            }

            $isCompletedByDuration = false;
            if ($progress->video_duration_seconds > 0) {
                $required = (int) floor($progress->video_duration_seconds * 0.9);
                if ($progress->video_progress_seconds >= $required) {
                    $isCompletedByDuration = true;
                }
            }

            if ($completedFlag || $isCompletedByDuration) {
                $progress->trangThai = 'COMPLETED';
                if (!$progress->completed_at) {
                    $progress->completed_at = $now;
                }
            } elseif ($progress->trangThai !== 'COMPLETED' && $event !== 'start') {
                $progress->trangThai = 'IN_PROGRESS';
            }

            $progress->save();

            $metrics = $this->recalculateEnrollmentProgress($enrollment, $lesson);

            return [
                'lesson_progress' => [
                    'status' => $progress->trangThai,
                    'video_progress_seconds' => (int) $progress->video_progress_seconds,
                    'video_duration_seconds' => (int) $progress->video_duration_seconds,
                    'watched_seconds' => (int) $progress->thoiGianHoc,
                    'completed_at' => $progress->completed_at?->toIso8601String(),
                ],
                'enrollment' => $metrics,
            ];
        });

        return response()->json([
            'status' => 'ok',
            'data' => $progressData,
        ]);
    }

    /**
     * @throws ValidationException
     */
    protected function validatePayload(Request $request): array
    {
        return $request->validate([
            'event' => ['required', 'string', 'in:start,progress'],
            'current_time' => ['nullable', 'numeric', 'min:0'],
            'duration' => ['nullable', 'numeric', 'min:0'],
            'watched_delta' => ['nullable', 'numeric', 'min:0'],
            'completed' => ['nullable', 'boolean'],
        ]);
    }

    protected function recalculateEnrollmentProgress(Enrollment $enrollment, Lesson $lesson): array
    {
        $courseId = $enrollment->maKH;

        $lessonTypes = DB::table('BAIHOC as bh')
            ->join('CHUONG as ch', 'bh.maChuong', '=', 'ch.maChuong')
            ->where('ch.maKH', $courseId)
            ->pluck('loai', 'maBH')
            ->map(fn($type) => strtolower((string) $type));

        $totalLessons = $lessonTypes->count();
        $totalVideoLessons = $lessonTypes->filter(fn($type) => $type === 'video')->count();

        $progressEntries = LessonProgress::where('maHV', $enrollment->maHV)
            ->where('maKH', $courseId)
            ->get();

        $completedLessons = 0;
        $completedVideos = 0;
        $totalVideoDuration = 0;
        $totalVideoProgress = 0;

        foreach ($progressEntries as $entry) {
            $lessonType = $lessonTypes->get($entry->maBH);
            $isCompleted = $entry->trangThai === 'COMPLETED';
            if ($isCompleted) {
                $completedLessons++;
            }

            if ($lessonType === 'video') {
                $duration = max(0, (int) $entry->video_duration_seconds);
                $progress = max(0, (int) $entry->video_progress_seconds);
                $totalVideoDuration += $duration;
                $totalVideoProgress += min($progress, $duration > 0 ? $duration : $progress);

                if ($isCompleted || ($duration > 0 && $progress >= (int) floor($duration * 0.9))) {
                    $completedVideos++;
                }
            }
        }

        $lessonPercent = $totalLessons > 0
            ? (int) round(($completedLessons / $totalLessons) * 100)
            : 0;

        $videoPercent = null;
        if ($totalVideoLessons > 0) {
            $videoPercent = (int) round(($completedVideos / $totalVideoLessons) * 100);
        } elseif ($totalVideoDuration > 0) {
            $videoPercent = (int) round(($totalVideoProgress / $totalVideoDuration) * 100);
        }
        if ($videoPercent !== null) {
            $videoPercent = min(100, max(0, $videoPercent));
        }

        $updatePayload = [
            'progress_percent' => $lessonPercent,
            'last_lesson_id' => $lesson->maBH,
            'updated_at' => Carbon::now('Asia/Ho_Chi_Minh'),
        ];
        if ($videoPercent !== null) {
            $updatePayload['video_progress_percent'] = $videoPercent;
        }

        DB::table('HOCVIEN_KHOAHOC')
            ->where('maHV', $enrollment->maHV)
            ->where('maKH', $courseId)
            ->update($updatePayload);

        $enrollment->progress_percent = $lessonPercent;
        if ($videoPercent !== null) {
            $enrollment->video_progress_percent = $videoPercent;
        }
        $enrollment->last_lesson_id = $lesson->maBH;

        return [
            'progress_percent' => $enrollment->progress_percent,
            'video_progress_percent' => $enrollment->video_progress_percent,
        ];
    }
}
