<?php

namespace App\Http\Controllers\API\Student;

use App\Http\Controllers\API\Student\Concerns\HasLessonProgressFormatting;
use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Services\CertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LessonProgressController extends Controller
{
    use HasLessonProgressFormatting;

    public function __construct(
        private readonly CertificateService $certificateService
    ) {
    }

    /**
     * Cập nhật tiến độ học tập của một bài học.
     */
    public function update(Request $request, Lesson $lesson): JsonResponse
    {
        $user = $request->user()->loadMissing('student');

        if (! $user->student) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tài khoản không có thông tin học viên.',
            ], 403);
        }

        $lesson->loadMissing('chapter.course');

        $course = $lesson->chapter->course;

        $enrollment = Enrollment::query()
            ->where('maHV', $user->student->maHV)
            ->where('maKH', $course->maKH)
            ->where('trangThai', 'ACTIVE')
            ->first();

        if (! $enrollment) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Bạn chưa kích hoạt khóa học này.',
            ], 403);
        }

        $validated = $request->validate([
            'status'                 => ['required', Rule::in(['NOT_STARTED', 'IN_PROGRESS', 'COMPLETED'])],
            'total_view_seconds'     => ['nullable', 'integer', 'min:0'],
            'video_progress_seconds' => ['nullable', 'integer', 'min:0'],
            'video_duration_seconds' => ['nullable', 'integer', 'min:0'],
            'watch_count'            => ['nullable', 'integer', 'min:0'],
            'last_viewed_at'         => ['nullable', 'date'],
            'completed_at'           => ['nullable', 'date'],
            'note'                   => ['nullable', 'string', 'max:500'],
            'course_progress.percent_overall' => ['nullable', 'integer', 'between:0,100'],
            'course_progress.percent_video'   => ['nullable', 'integer', 'between:0,100'],
            'mark_last_lesson'       => ['nullable', 'boolean'],
        ]);

        $progress = null;

        // Gồm thao tác cập nhật vào transaction để tránh dữ liệu lệch.
        DB::transaction(function () use (&$progress, $validated, $lesson, $enrollment, $user) {
            $progressData = [
                'trangThai' => $validated['status'],
            ];

            if (array_key_exists('total_view_seconds', $validated)) {
                $progressData['thoiGianHoc'] = $validated['total_view_seconds'];
            }

            if (array_key_exists('video_progress_seconds', $validated)) {
                $progressData['video_progress_seconds'] = $validated['video_progress_seconds'];
            }

            if (array_key_exists('video_duration_seconds', $validated)) {
                $progressData['video_duration_seconds'] = $validated['video_duration_seconds'];
            }

            if (array_key_exists('watch_count', $validated)) {
                $progressData['soLanXem'] = $validated['watch_count'];
            }

            if (array_key_exists('last_viewed_at', $validated)) {
                $progressData['lanXemCuoi'] = $validated['last_viewed_at']
                    ? Carbon::parse($validated['last_viewed_at'])
                    : null;
            }

            if (array_key_exists('note', $validated)) {
                $progressData['ghiChu'] = $validated['note'];
            }

            if (array_key_exists('completed_at', $validated)) {
                $progressData['completed_at'] = $validated['completed_at']
                    ? Carbon::parse($validated['completed_at'])
                    : null;
            } elseif ($validated['status'] === 'COMPLETED') {
                $progressData['completed_at'] = now();
            }

            $progress = LessonProgress::query()->updateOrCreate(
                [
                    'maHV' => $user->student->maHV,
                    'maKH' => $enrollment->maKH,
                    'maBH' => $lesson->maBH,
                ],
                $progressData
            );

            $courseProgress = $validated['course_progress'] ?? [];
            $enrollmentData = [];

            if (array_key_exists('percent_overall', $courseProgress)) {
                $enrollmentData['progress_percent'] = $courseProgress['percent_overall'];
            }

            if (array_key_exists('percent_video', $courseProgress)) {
                $enrollmentData['video_progress_percent'] = $courseProgress['percent_video'];
            }

            $markLastLesson = (bool) ($validated['mark_last_lesson'] ?? false);

            if ($markLastLesson || $validated['status'] === 'COMPLETED') {
                // Lưu bài học cuối cùng đã học để mobile tiếp tục từ đây.
                $enrollmentData['last_lesson_id'] = $lesson->maBH;
            }

            if (! empty($enrollmentData)) {
                $enrollment->forceFill($enrollmentData)->save();
            }
        });

        $progress->refresh();
        $enrollment->refresh();
        $this->certificateService->issueCourseCertificateIfEligible($enrollment);

        return response()->json([
            'status'  => 'success',
            'message' => 'Cập nhật tiến độ bài học thành công.',
            'data'    => [
                'lesson_id' => $lesson->maBH,
                'course_id' => $lesson->chapter->course->maKH,
                'progress'  => $this->formatProgress($progress),
                'course_progress' => [
                    'percent_overall' => (int) ($enrollment->progress_percent ?? 0),
                    'percent_video'   => (int) ($enrollment->video_progress_percent ?? 0),
                    'last_lesson_id'  => $enrollment->last_lesson_id,
                ],
            ],
        ]);
    }
}
