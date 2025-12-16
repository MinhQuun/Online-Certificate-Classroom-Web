<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Services\CertificateService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LessonProgressController extends Controller
{
    public function __construct(
        private readonly CertificateService $certificateService
    ) {
    }

    public function store(Request $request, Lesson $lesson): JsonResponse
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $student = DB::table('hocvien')->where('maND', $userId)->first();
        if (!$student) {
            return response()->json(['message' => 'Không tim thấy hồ sơ học viên.'], 403);
        }

        $lesson->loadMissing('chapter');
        $courseId = optional($lesson->chapter)->maKH;
        if (!$courseId) {
            return response()->json(['message' => 'Không xác định được khóa học.'], 422);
        }

        $enrollment = Enrollment::where('maHV', $student->maHV)
            ->where('maKH', $courseId)
            ->where('trangThai', 'ACTIVE')
            ->first();

        if (!$enrollment) {
            return response()->json(['message' => 'Khóa học chưa được kích hoạt.'], 403);
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

        $enrollment->refresh();
        $this->certificateService->issueCourseCertificateIfEligible($enrollment);

        return response()->json([
            'status' => 'ok',
            'data' => $progressData,
        ]);
    }

    public function pass(Request $request, Lesson $lesson): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $isPrivilegedRole = in_array($user->vaiTro, ['ADMIN', 'GIANG_VIEN'], true);
        $isDebug = (bool) config('app.debug');
        if (!$isPrivilegedRole && !$isDebug) {
            return response()->json(['message' => 'Bạn không có quyền pass nhanh video.'], 403);
        }

        $student = DB::table('hocvien')->where('maND', $user->getAuthIdentifier())->first();
        if (!$student) {
            return response()->json(['message' => 'Chức năng pass nhanh chỉ áp dụng cho tài khoản học viên.'], 403);
        }

        $lesson->loadMissing('chapter');
        $courseId = optional($lesson->chapter)->maKH;
        if (!$courseId) {
            return response()->json(['message' => 'Không xác định được khóa học.'], 422);
        }

        $enrollment = Enrollment::where('maHV', $student->maHV)
            ->where('maKH', $courseId)
            ->where('trangThai', 'ACTIVE')
            ->first();

        if (!$enrollment) {
            return response()->json(['message' => 'Khóa học chưa được kích hoạt.'], 403);
        }

        $validated = $request->validate([
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'pass_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $progressData = DB::transaction(function () use ($validated, $lesson, $student, $courseId, $enrollment, $user) {
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

            $duration = max(0, (int) ($validated['duration_seconds'] ?? 0));
            if ($duration > 0) {
                $progress->video_duration_seconds = max(
                    (int) $progress->video_duration_seconds,
                    $duration
                );
            }

            $targetProgress = max(
                (int) $progress->video_progress_seconds,
                (int) $progress->video_duration_seconds,
                $duration
            );

            if ($targetProgress > 0) {
                $progress->video_progress_seconds = $targetProgress;
            }

            $progress->soLanXem = max(1, (int) $progress->soLanXem);
            $progress->thoiGianHoc = max((int) $progress->thoiGianHoc, $targetProgress);
            $progress->lanXemCuoi = $now;
            $progress->trangThai = 'COMPLETED';
            $progress->completed_at = $now;
            $progress->demo_passed_at = $now;
            $progress->demo_passed_by = $user->maND;
            if (!empty($validated['pass_reason'])) {
                $progress->demo_pass_reason = $validated['pass_reason'];
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
                    'demo_passed_at' => $progress->demo_passed_at?->toIso8601String(),
                    'demo_passed_by' => $progress->demo_passed_by,
                ],
                'enrollment' => $metrics,
            ];
        });

        $enrollment->refresh();
        $this->certificateService->issueCourseCertificateIfEligible($enrollment);

        return response()->json([
            'status' => 'ok',
            'message' => 'Đã pass nhanh video cho mục đích demo tiến độ.',
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

        $lessonTypes = DB::table('baihoc as bh')
            ->join('chuong as ch', 'bh.maChuong', '=', 'ch.maChuong')
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

        DB::table('hocvien_khoahoc')
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
