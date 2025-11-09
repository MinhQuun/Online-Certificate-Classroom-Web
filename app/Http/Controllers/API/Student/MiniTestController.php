<?php

namespace App\Http\Controllers\API\Student;

use App\Http\Controllers\Student\MiniTestController as WebMiniTestController;
use App\Models\Chapter;
use App\Models\MiniTest;
use App\Models\MiniTestQuestion;
use App\Models\MiniTestResult;
use App\Models\MiniTestStudentAnswer;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Throwable;

class MiniTestController extends WebMiniTestController
{
    public function listByChapter(Chapter $chapter): JsonResponse
    {
        $student = $this->currentStudent();
        $chapter->loadMissing('course');
        $this->ensureEnrolled($student, (int) $chapter->maKH);

        [$miniTests, $resultsByTest] = $this->loadMiniTestsForChapter($chapter, $student);

        $data = $miniTests->map(function (MiniTest $miniTest) use ($resultsByTest, $student) {
            $results = $resultsByTest->get($miniTest->maMT, collect());

            return $this->transformMiniTest($miniTest, $results, $student->maHV);
        })->values();

        return response()->json([
            'status'  => 'success',
            'message' => 'Lấy danh sách mini-test thành công.',
            'data'    => [
                'chapter' => [
                    'id'    => $chapter->maChuong,
                    'title' => $chapter->tenChuong,
                ],
                'mini_tests' => $data,
            ],
        ]);
    }

    public function showMiniTest(MiniTest $miniTest): JsonResponse
    {
        $student = $this->currentStudent();
        $miniTest->loadMissing(['chapter.course', 'questions' => fn ($query) => $query->orderBy('thuTu')]);
        $this->ensureMiniTestAvailable($student, $miniTest);

        $results = MiniTestResult::where('maMT', $miniTest->maMT)
            ->where('maHV', $student->maHV)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Lấy thông tin mini-test thành công.',
            'data'    => $this->transformMiniTest($miniTest, $results, $student->maHV),
        ]);
    }

    public function startAttempt(Request $request, MiniTest $miniTest): JsonResponse
    {
        $student = $this->currentStudent();
        $miniTest->loadMissing(['chapter.course', 'questions' => fn ($query) => $query->orderBy('thuTu')]);

        $this->ensureMiniTestAvailable($student, $miniTest);

        if ($miniTest->questions->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Mini-test chưa có câu hỏi. Vui lòng liên hệ giáo viên.',
            ], 422);
        }

        $result = $this->beginAttempt($student, $miniTest, function () {
            throw new HttpResponseException(response()->json([
                'status'  => 'error',
                'message' => 'Bạn đã hết lượt làm bài. Vui lòng liên hệ giáo viên để được hỗ trợ.',
            ], 429));
        });

        $remainingSeconds = $result->expires_at
            ? max(0, now()->diffInSeconds($result->expires_at, false))
            : null;

        return response()->json([
            'status'  => 'success',
            'message' => 'Bắt đầu làm bài thành công.',
            'data'    => [
                'result_id'         => $result->maKQDG,
                'mini_test_id'      => $miniTest->maMT,
                'attempt_no'        => $result->attempt_no,
                'expires_at'        => optional($result->expires_at)->toIso8601String(),
                'remaining_seconds' => $remainingSeconds,
            ],
        ], 201);
    }

    public function showAttempt(MiniTestResult $result): JsonResponse
    {
        $student = $this->currentStudent();
        $this->ensureResultOwnership($result, $student);

        $result->loadMissing([
            'miniTest.chapter',
            'miniTest.course',
            'miniTest.questions' => fn ($query) => $query->orderBy('thuTu'),
            'studentAnswers',
        ]);

        if ($result->status !== MiniTestResult::STATUS_IN_PROGRESS) {
            return response()->json([
                'status'  => 'info',
                'message' => 'Bài làm đã được nộp. Bạn có thể xem kết quả.',
                'data'    => [
                    'result_id' => $result->maKQDG,
                    'status'    => $result->status,
                ],
            ], 409);
        }

        if ($this->hasExpired($result)) {
            try {
                $this->completeAttempt($result, $student, true);
            } catch (Throwable $throwable) {
                report($throwable);
            }

            return response()->json([
                'status'  => 'info',
                'message' => 'Bài làm đã hết thời gian và được hệ thống tự động nộp.',
                'data'    => [
                    'result_id' => $result->maKQDG,
                    'status'    => MiniTestResult::STATUS_EXPIRED,
                ],
            ], 409);
        }

        $answers = $result->studentAnswers->keyBy('maCauHoi');
        $remainingSeconds = $result->expires_at
            ? max(0, now()->diffInSeconds($result->expires_at, false))
            : null;

        $questions = $result->miniTest->questions->map(function (MiniTestQuestion $question) use ($answers) {
            /** @var MiniTestStudentAnswer|null $answer */
            $answer = $answers->get($question->maCauHoi);

            return [
                'id'        => $question->maCauHoi,
                'order'     => $question->thuTu,
                'type'      => $question->loai,
                'content'   => $question->noiDungCauHoi,
                'score'     => (float) $question->diem,
                'choices'   => [
                    'A' => $question->phuongAnA,
                    'B' => $question->phuongAnB,
                    'C' => $question->phuongAnC,
                    'D' => $question->phuongAnD,
                ],
                'attachments' => [
                    'audio' => $question->audio_url,
                    'image' => $question->image_url,
                    'pdf'   => $question->pdf_url,
                ],
                'answer' => [
                    'choice'    => $answer?->answer_choice,
                    'text'      => $answer?->answer_text,
                    'audio_url' => $answer?->answer_audio_url,
                ],
            ];
        })->values();

        return response()->json([
            'status'  => 'success',
            'message' => 'Lấy dữ liệu bài làm thành công.',
            'data'    => [
                'result_id'         => $result->maKQDG,
                'mini_test_id'      => $result->miniTest->maMT,
                'remaining_seconds' => $remainingSeconds,
                'questions'         => $questions,
            ],
        ]);
    }

    public function submitAttempt(Request $request, MiniTestResult $result): JsonResponse
    {
        $student = $this->currentStudent();
        $this->ensureResultOwnership($result, $student);
        $this->ensureAttemptEditable($result, true);

        $expired = $this->hasExpired($result);

        try {
            $outcome = $this->completeAttempt($result, $student, $expired);
        } catch (Throwable $throwable) {
            report($throwable);

            return response()->json([
                'status'  => 'error',
                'message' => 'Không thể nộp bài vào lúc này. Vui lòng thử lại.',
            ], 500);
        }

        $message = $outcome['hasEssay']
            ? 'Đã nộp bài thành công. Giáo viên sẽ chấm điểm sớm nhất.'
            : 'Đã nộp bài thành công.';

        if ($outcome['expired']) {
            $message = 'Bài làm đã quá thời gian, hệ thống đã lưu lại các câu trả lời trước khi hết giờ.';
        }

        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => [
                'result_id'    => $result->maKQDG,
                'auto_score'   => $outcome['autoScore'] ?? null,
                'has_essay'    => $outcome['hasEssay'],
                'expired'      => $outcome['expired'],
                'max_score'    => $result->miniTest->max_score,
            ],
        ]);
    }

    public function showResult(MiniTestResult $result): JsonResponse
    {
        $student = $this->currentStudent();
        $this->ensureResultOwnership($result, $student);

        $result->loadMissing([
            'miniTest.chapter',
            'miniTest.course',
            'miniTest.questions' => fn ($query) => $query->orderBy('thuTu'),
            'studentAnswers.question',
        ]);

        $correctCount = $result->studentAnswers->where('is_correct', true)->count();
        $incorrectCount = $result->studentAnswers->where('is_correct', false)->count();
        $essayCount = $result->studentAnswers->whereNull('is_correct')->count();

        $attemptsUsed = MiniTestResult::where('maMT', $result->maMT)
            ->where('maHV', $student->maHV)
            ->count();
        $attemptsAllowed = (int) ($result->miniTest->attempts_allowed ?? 0);
        $attemptsLeft = max(0, $attemptsAllowed - $attemptsUsed);

        return response()->json([
            'status'  => 'success',
            'message' => 'Lấy kết quả mini-test thành công.',
            'data'    => [
                'result_id'       => $result->maKQDG,
                'mini_test_id'    => $result->maMT,
                'status'          => $result->status,
                'score'           => $result->diem,
                'auto_score'      => $result->auto_graded_score,
                'essay_score'     => $result->essay_score,
                'is_fully_graded' => (bool) $result->is_fully_graded,
                'attempts_used'   => $attemptsUsed,
                'attempts_left'   => $attemptsLeft,
                'question_summary'=> [
                    'correct'   => $correctCount,
                    'incorrect' => $incorrectCount,
                    'essay'     => $essayCount,
                ],
            ],
        ]);
    }

    protected function transformMiniTest(MiniTest $miniTest, Collection $results, int $studentId): array
    {
        $latest = $results->sortByDesc('created_at')->first();
        $bestScore = $results->max('diem');

        $attemptsAllowed = (int) ($miniTest->attempts_allowed ?? 0);
        $attemptsUsed = $results->count();

        $inProgress = $results->first(fn (MiniTestResult $result) => $result->status === MiniTestResult::STATUS_IN_PROGRESS);

        return [
            'id'               => $miniTest->maMT,
            'title'            => $miniTest->tenMiniTest,
            'course_id'        => $miniTest->maKH,
            'chapter_id'       => $miniTest->maChuong,
            'max_score'        => $miniTest->max_score,
            'duration_minutes' => $miniTest->duration_minutes,
            'attempts_allowed' => $attemptsAllowed,
            'attempts_used'    => $attemptsUsed,
            'attempts_left'    => max(0, $attemptsAllowed - $attemptsUsed),
            'best_score'       => $bestScore,
            'latest_result_id' => $latest?->maKQDG,
            'in_progress_result_id' => $inProgress?->maKQDG,
            'is_active'        => (bool) $miniTest->is_active,
            'is_published'     => (bool) $miniTest->is_published,
            'status'           => $this->determineMiniTestStatus($miniTest, $attemptsAllowed, $attemptsUsed, $inProgress),
        ];
    }

    protected function determineMiniTestStatus(MiniTest $miniTest, int $attemptsAllowed, int $attemptsUsed, ?MiniTestResult $inProgress): string
    {
        if (!$miniTest->is_active || !$miniTest->is_published) {
            return 'upcoming';
        }

        if ($inProgress) {
            return 'in_progress';
        }

        if ($attemptsAllowed > 0 && $attemptsUsed >= $attemptsAllowed) {
            return 'exhausted';
        }

        return 'available';
    }
}
