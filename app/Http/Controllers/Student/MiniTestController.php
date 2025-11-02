<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Enrollment;
use App\Models\MiniTest;
use App\Models\MiniTestQuestion;
use App\Models\MiniTestResult;
use App\Models\MiniTestStudentAnswer;
use App\Models\Student;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class MiniTestController extends Controller
{
    public function index(Chapter $chapter): View
    {
        $student = $this->currentStudent();
        $chapter->loadMissing('course');
        $this->ensureEnrolled($student, (int) $chapter->maKH);

        [$miniTests, $resultsByTest] = $this->loadMiniTestsForChapter($chapter, $student);

        return view('Student.minitests', [
            'type' => 'index',
            'chapter' => $chapter,
            'miniTests' => $miniTests,
            'resultsByTest' => $resultsByTest,
            'student' => $student,
        ]);
    }

    public function show(MiniTest $miniTest): View
    {
        $student = $this->currentStudent();
        $miniTest->loadMissing(['chapter.course']);

        $this->ensureMiniTestAvailable($student, $miniTest);

        $chapter = $miniTest->chapter;
        [$miniTests, $resultsByTest] = $this->loadMiniTestsForChapter($chapter, $student);

        return view('Student.minitests', [
            'type' => 'index',
            'chapter' => $chapter,
            'miniTests' => $miniTests,
            'resultsByTest' => $resultsByTest,
            'student' => $student,
            'activeMiniTestId' => $miniTest->maMT,
        ]);
    }

    public function start(Request $request, MiniTest $miniTest): RedirectResponse
    {
        $student = $this->currentStudent();
        $miniTest->loadMissing(['chapter.course', 'questions' => fn ($query) => $query->orderBy('thuTu')]);

        $this->ensureMiniTestAvailable($student, $miniTest);

        if ($miniTest->questions->isEmpty()) {
            return back()->with('error', 'Mini-test chưa được cấu hình câu hỏi. Vui lòng thông báo cho giáo viên.');
        }

        $result = $this->beginAttempt(
            $student,
            $miniTest,
            fn () => back()->with('error', 'Bạn đã hết lượt làm bài. Vui lòng liên hệ giáo viên để được hỗ trợ thêm thời gian.')
        );

        if ($result instanceof RedirectResponse) {
            return $result;
        }

        return redirect()->route('student.minitests.attempt', $result->maKQDG);
    }

    public function attempt(MiniTestResult $result): View|RedirectResponse
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
            return redirect()->route('student.minitests.result', $result->maKQDG);
        }

        if ($this->hasExpired($result)) {
            try {
                $this->completeAttempt($result, $student, true);
            } catch (Throwable $throwable) {
                report($throwable);
            }

            return redirect()
                ->route('student.minitests.result', $result->maKQDG)
                ->with('warning', 'Bài làm đã hết thời gian. Hệ thống đã tự động nộp bài để lưu kết quả.');
        }

        $answers = $result->studentAnswers->keyBy('maCauHoi');

        $remainingSeconds = $result->expires_at
            ? max(0, now()->diffInSeconds($result->expires_at, false))
            : null;

        return view('Student.minitests', [
            'type' => 'attempt',
            'result' => $result,
            'student' => $student,
            'answers' => $answers,
            'remainingSeconds' => $remainingSeconds,
        ]);
    }

    public function saveAnswer(Request $request, MiniTestResult $result, MiniTestQuestion $question): JsonResponse
    {
        $student = $this->currentStudent();
        $this->ensureResultOwnership($result, $student);
        $this->ensureQuestionBelongsToResult($question, $result);

        $this->ensureAttemptEditable($result);

        $payload = $request->validate([
            'answer' => ['nullable'],
        ]);

        $answerValue = $payload['answer'] ?? null;

        if ($question->isChoice()) {
            $answerValue = $this->normaliseChoiceAnswer($question, $answerValue);
        }

        $studentAnswer = MiniTestStudentAnswer::updateOrCreate(
            [
                'maKQDG' => $result->maKQDG,
                'maCauHoi' => $question->maCauHoi,
                'maHV' => $student->maHV,
            ],
            [
                'answer_choice' => $question->isChoice() ? $answerValue : null,
                'answer_text' => $question->isEssay()
                    ? ($answerValue !== null ? (string) $answerValue : null)
                    : null,
                'is_correct' => null,
                'diem' => null,
                'graded_at' => null,
                'graded_by' => null,
            ]
        );

        return response()->json([
            'success' => true,
            'answer_id' => $studentAnswer->id,
            'saved_at' => now()->toISOString(),
        ]);
    }

    public function uploadSpeakingAnswer(Request $request, MiniTestResult $result, MiniTestQuestion $question): JsonResponse
    {
        $student = $this->currentStudent();
        $this->ensureResultOwnership($result, $student);
        $this->ensureQuestionBelongsToResult($question, $result);
        $this->ensureAttemptEditable($result);

        if (!$question->isEssay()) {
            return response()->json([
                'success' => false,
                'message' => 'Câu hỏi này không yêu cầu ghi âm.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $request->validate([
            'audio' => ['required', 'file', 'mimetypes:audio/mpeg,audio/mp3', 'max:10240'],
        ]);

        $file = $data['audio'];
        $studentAnswer = MiniTestStudentAnswer::firstOrCreate(
            [
                'maKQDG' => $result->maKQDG,
                'maCauHoi' => $question->maCauHoi,
                'maHV' => $student->maHV,
            ]
        );

        if ($studentAnswer->answer_audio_url) {
            $this->deleteStoredFile($studentAnswer->answer_audio_url);
        }

        $path = $file->storeAs(
            "mini-test-results/{$result->maKQDG}",
            "question-{$question->maCauHoi}.mp3",
            's3'
        );

        $url = Storage::disk('s3')->url($path);

        $studentAnswer->update([
            'answer_audio_url' => $url,
            'audio_mime' => $file->getMimeType(),
            'audio_size_kb' => (int) ceil($file->getSize() / 1024),
        ]);

        return response()->json([
            'success' => true,
            'audio_url' => $url,
        ]);
    }

    public function submit(Request $request, MiniTestResult $result): RedirectResponse
    {
        $student = $this->currentStudent();
        $this->ensureResultOwnership($result, $student);
        $this->ensureAttemptEditable($result, true);

        $expired = $this->hasExpired($result);

        try {
            $outcome = $this->completeAttempt($result, $student, $expired);
        } catch (Throwable $throwable) {
            report($throwable);

            return back()->with('error', 'Không thể nộp bài lúc này. Vui lòng thử lại.');
        }

        $route = route('student.minitests.result', $result->maKQDG);

        if ($outcome['expired']) {
            return redirect($route)->with('warning', 'Bài làm đã quá thời gian, hệ thống đã lưu lại các câu trả lời trước khi hết giờ.');
        }

        $successMessage = $outcome['hasEssay']
            ? 'Đã nộp bài thành công. Giáo viên sẽ chấm điểm và phản hồi sớm nhất.'
            : 'Đã nộp bài thành công. Điểm của bạn: ' . number_format($outcome['autoScore'], 2) . '/' . number_format($result->miniTest->max_score, 2);

        return redirect($route)->with('success', $successMessage);
    }

    public function result(MiniTestResult $result): View
    {
        $student = $this->currentStudent();
        $this->ensureResultOwnership($result, $student);

        $result->load([
            'miniTest.chapter',
            'miniTest.course',
            'miniTest.questions' => fn ($q) => $q->orderBy('thuTu'),
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

        return view('Student.minitests', [
            'type' => 'result',
            'result' => $result,
            'student' => $student,
            'correctCount' => $correctCount,
            'incorrectCount' => $incorrectCount,
            'essayCount' => $essayCount,
            'attemptsLeft' => $attemptsLeft,
        ]);
    }

    protected function loadMiniTestsForChapter(Chapter $chapter, Student $student): array
    {
        $miniTests = $chapter->miniTests()
            ->visibleToStudents()
            ->with([
                'questions' => fn ($query) => $query->orderBy('thuTu'),
                'results' => fn ($query) => $query->where('maHV', $student->maHV),
            ])
            ->orderBy('thuTu')
            ->get();

        $resultsByTest = MiniTestResult::query()
            ->where('maHV', $student->maHV)
            ->whereIn('maMT', $miniTests->pluck('maMT'))
            ->get()
            ->groupBy('maMT');

        return [$miniTests, $resultsByTest];
    }

    protected function currentStudent(): Student
    {
        $user = Auth::user();

        return Student::where('maND', $user->maND)->firstOrFail();
    }

    protected function ensureEnrolled(Student $student, int $courseId): Enrollment
    {
        $enrollment = Enrollment::where('maHV', $student->maHV)
            ->where('maKH', $courseId)
            ->whereIn('trangThai', ['ACTIVE', 'PENDING'])
            ->first();

        if (!$enrollment) {
            abort(Response::HTTP_FORBIDDEN, 'Bạn chưa được ghi danh vào khóa học này.');
        }

        return $enrollment;
    }

    protected function ensureMiniTestAvailable(Student $student, MiniTest $miniTest): void
    {
        $this->ensureEnrolled($student, (int) $miniTest->maKH);

        if (!$miniTest->is_active || !$miniTest->is_published) {
            abort(Response::HTTP_FORBIDDEN, 'Mini-test hiện chưa mở cho học viên.');
        }
    }

    protected function ensureResultOwnership(MiniTestResult $result, Student $student): void
    {
        if ((int) $result->maHV !== (int) $student->maHV) {
            abort(Response::HTTP_FORBIDDEN, 'Bạn không có quyền truy cập bài làm này.');
        }
    }

    protected function ensureQuestionBelongsToResult(MiniTestQuestion $question, MiniTestResult $result): void
    {
        if ((int) $question->maMT !== (int) $result->maMT) {
            abort(Response::HTTP_FORBIDDEN, 'Câu hỏi không thuộc mini-test này.');
        }
    }

    protected function ensureAttemptEditable(MiniTestResult $result, bool $allowExpired = false): void
    {
        if ($result->status !== MiniTestResult::STATUS_IN_PROGRESS) {
            $this->throwAttemptLocked('Bài làm đã được nộp hoặc đã kết thúc.');
        }

        if (!$allowExpired && $this->hasExpired($result)) {
            $this->throwAttemptLocked('Bài làm đã hết thời gian.');
        }
    }

    protected function throwAttemptLocked(string $message): void
    {
        if (request()->expectsJson()) {
            throw new HttpResponseException(response()->json([
                'message' => $message,
            ], Response::HTTP_FORBIDDEN));
        }

        abort(Response::HTTP_FORBIDDEN, $message);
    }

    protected function normaliseChoiceAnswer(MiniTestQuestion $question, mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($question->allowsMultipleSelections()) {
            $values = is_array($value) ? $value : explode(';', (string) $value);
            $values = array_map(
                fn ($item) => strtoupper(trim((string) $item)),
                $values
            );
            $values = array_values(array_unique(array_filter($values, fn ($item) => $item !== '')));

            sort($values);

            return implode(';', $values) ?: null;
        }

        $selected = is_array($value) ? ($value[0] ?? null) : $value;
        if ($selected === null) {
            return null;
        }

        $selected = strtoupper(trim((string) $selected));

        if ($question->loai === MiniTestQuestion::TYPE_TRUE_FALSE) {
            if (!in_array($selected, ['TRUE', 'FALSE'], true)) {
                return null;
            }
        }

        return $selected !== '' ? $selected : null;
    }

    protected function expandChoiceAnswer(?string $stored): array|string|null
    {
        if ($stored === null || $stored === '') {
            return null;
        }

        if (str_contains($stored, ';')) {
            $parts = array_map('trim', explode(';', $stored));
            $parts = array_values(array_filter($parts, fn ($part) => $part !== ''));

            return $parts;
        }

        return trim($stored);
    }

    protected function beginAttempt(Student $student, MiniTest $miniTest, callable $onLimitReached): MiniTestResult|RedirectResponse
    {
        $inProgress = MiniTestResult::query()
            ->where('maHV', $student->maHV)
            ->where('maMT', $miniTest->maMT)
            ->where('status', MiniTestResult::STATUS_IN_PROGRESS)
            ->latest('created_at')
            ->first();

        if ($inProgress) {
            if ($this->hasExpired($inProgress)) {
                try {
                    $this->completeAttempt($inProgress, $student, true);
                } catch (Throwable $throwable) {
                    report($throwable);
                }
            } else {
                return $inProgress;
            }
        }

        $attemptCount = MiniTestResult::query()
            ->where('maHV', $student->maHV)
            ->where('maMT', $miniTest->maMT)
            ->count();

        $attemptsAllowed = (int) ($miniTest->attempts_allowed ?? 0);
        if ($attemptsAllowed > 0 && $attemptCount >= $attemptsAllowed) {
            $response = $onLimitReached();

            return $response instanceof RedirectResponse
                ? $response
                : back();
        }

        $now = now();
        $expiresAt = $miniTest->time_limit_min
            ? $now->copy()->addMinutes((int) $miniTest->time_limit_min)
            : null;

        return MiniTestResult::create([
            'maMT' => $miniTest->maMT,
            'maKH' => $miniTest->maKH,
            'maHV' => $student->maHV,
            'attempt_no' => $attemptCount + 1,
            'status' => MiniTestResult::STATUS_IN_PROGRESS,
            'started_at' => $now,
            'expires_at' => $expiresAt,
        ]);
    }

    protected function completeAttempt(MiniTestResult $result, Student $student, bool $expired): array
    {
        $result->loadMissing([
            'miniTest.questions' => fn ($query) => $query->orderBy('thuTu'),
        ]);

        $hasEssayQuestion = $result->miniTest->questions->contains(fn ($q) => $q->isEssay());

        if ($result->status !== MiniTestResult::STATUS_IN_PROGRESS) {
            return [
                'autoScore' => (float) ($result->auto_graded_score ?? 0),
                'hasEssay' => $hasEssayQuestion,
                'expired' => $result->status === MiniTestResult::STATUS_EXPIRED,
            ];
        }

        $result->loadMissing(['studentAnswers']);

        $answers = $result->studentAnswers->keyBy('maCauHoi');
        $autoScore = 0.0;
        $hasEssay = false;
        $now = now();

        DB::beginTransaction();

        try {
            foreach ($result->miniTest->questions as $question) {
                $answer = $answers->get($question->maCauHoi) ?? MiniTestStudentAnswer::create([
                    'maKQDG' => $result->maKQDG,
                    'maCauHoi' => $question->maCauHoi,
                    'maHV' => $student->maHV,
                ]);

                if ($question->isChoice()) {
                    $choiceValue = $answer->answer_choice ?? '';
                    $isCorrect = $question->checkAnswer($this->expandChoiceAnswer($choiceValue));
                    $score = $isCorrect ? (float) $question->diem : 0.0;

                    $answer->update([
                        'is_correct' => $isCorrect,
                        'diem' => $score,
                        'graded_at' => $now,
                        'teacher_feedback' => null,
                        'graded_by' => null,
                    ]);

                    $autoScore += $score;
                } else {
                    $hasEssay = true;

                    $answer->update([
                        'is_correct' => null,
                        'diem' => null,
                        'graded_at' => null,
                        'teacher_feedback' => null,
                        'graded_by' => null,
                    ]);
                }
            }

            $status = $expired ? MiniTestResult::STATUS_EXPIRED : MiniTestResult::STATUS_SUBMITTED;
            $startedAt = $result->started_at ?: $now;
            $timeSpent = $startedAt ? $startedAt->diffInSeconds($now) : null;

            $result->update([
                'status' => $status,
                'auto_graded_score' => $autoScore,
                'essay_score' => $hasEssay ? null : 0,
                'diem' => $hasEssay ? null : $autoScore,
                'is_fully_graded' => !$hasEssay,
                'submitted_late' => $expired,
                'nop_luc' => $now,
                'completed_at' => $now,
                'graded_at' => $hasEssay ? null : $now,
                'time_spent_sec' => $timeSpent,
            ]);

            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();

            throw $throwable;
        }

        return [
            'autoScore' => $autoScore,
            'hasEssay' => $hasEssay || $hasEssayQuestion,
            'expired' => $expired,
        ];
    }

    protected function hasExpired(MiniTestResult $result): bool
    {
        if ($result->status === MiniTestResult::STATUS_EXPIRED) {
            return true;
        }

        if (!$result->expires_at) {
            return false;
        }

        return now()->greaterThan($result->expires_at);
    }

    protected function deleteStoredFile(?string $url): void
    {
        if (!$url) {
            return;
        }

        $path = parse_url($url, PHP_URL_PATH);
        if (!$path) {
            return;
        }

        $path = ltrim($path, '/');

        try {
            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
            }
        } catch (Throwable $throwable) {
            Log::warning('Không thể xoá tệp mini-test trên S3', [
                'path' => $path,
                'error' => $throwable->getMessage(),
            ]);
        }
    }
}
