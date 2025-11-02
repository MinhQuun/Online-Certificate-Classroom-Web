<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\MiniTest;
use App\Models\MiniTestQuestion;
use App\Models\MiniTestResult;
use App\Models\MiniTestStudentAnswer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class GradingController extends Controller
{
    use LoadsTeacherContext;

    public function writingIndex(Request $request): View
    {
        return $this->buildIndex($request, MiniTest::SKILL_WRITING, 'Teacher.grading-writing', 'writing');
    }

    public function speakingIndex(Request $request): View
    {
        return $this->buildIndex($request, MiniTest::SKILL_SPEAKING, 'Teacher.grading-speaking', 'speaking');
    }

    public function writingShow(MiniTestResult $result): View
    {
        return $this->buildShow($result, MiniTest::SKILL_WRITING, 'Teacher.grading-writing', 'writing');
    }

    public function speakingShow(MiniTestResult $result): View
    {
        return $this->buildShow($result, MiniTest::SKILL_SPEAKING, 'Teacher.grading-speaking', 'speaking');
    }

    public function writingGrade(Request $request, MiniTestResult $result): RedirectResponse
    {
        return $this->gradeResult($request, $result, MiniTest::SKILL_WRITING, 'Graded writing submission successfully.');
    }

    public function speakingGrade(Request $request, MiniTestResult $result): RedirectResponse
    {
        return $this->gradeResult($request, $result, MiniTest::SKILL_SPEAKING, 'Graded speaking submission successfully.');
    }

    public function writingBulkGrade(Request $request): RedirectResponse
    {
        return $this->bulkGrade($request, MiniTest::SKILL_WRITING, 'Graded {count} writing submissions.');
    }

    public function speakingBulkGrade(Request $request): RedirectResponse
    {
        return $this->bulkGrade($request, MiniTest::SKILL_SPEAKING, 'Graded {count} speaking submissions.');
    }

    protected function buildIndex(Request $request, string $skillType, string $view, string $mode): View
    {
        $teacher = Auth::user();
        $teacherId = $teacher?->getKey() ?? 0;

        $courses = Course::where('maND', $teacherId)
            ->orderBy('tenKH')
            ->get();

        $selectedCourseId = (int) $request->query('course');
        if ($selectedCourseId && !$courses->contains('maKH', $selectedCourseId)) {
            $selectedCourseId = 0;
        }

        $answersRelation = function ($query) use ($mode) {
            $query->whereNull('graded_at')
                ->whereHas('question', fn ($q) => $q->where('loai', MiniTestQuestion::TYPE_ESSAY))
                ->with('question');

            if ($mode === 'speaking') {
                $query->whereNotNull('answer_audio_url');
            }
        };

        $pendingResults = MiniTestResult::query()
            ->where('is_fully_graded', false)
            ->whereIn('status', [MiniTestResult::STATUS_SUBMITTED, MiniTestResult::STATUS_EXPIRED])
            ->whereHas('miniTest', function ($query) use ($teacherId, $skillType) {
                $query->where('skill_type', $skillType)
                    ->whereHas('course', fn ($courseQuery) => $courseQuery->where('maND', $teacherId));
            })
            ->when($selectedCourseId, fn ($query) => $query->where('maKH', $selectedCourseId))
            ->with([
                'miniTest.chapter',
                'miniTest.course',
                'student.user',
                'studentAnswers' => $answersRelation,
            ])
            ->orderByDesc('nop_luc')
            ->paginate(20)
            ->withQueryString();

        return view($view, [
            'type' => 'index',
            'mode' => $mode,
            'teacher' => $teacher,
            'courses' => $courses,
            'selectedCourseId' => $selectedCourseId,
            'results' => $pendingResults,
            'routePrefix' => $this->routePrefix($skillType),
            'badges' => $this->teacherSidebarBadges($teacherId),
        ]);
    }

    protected function buildShow(MiniTestResult $result, string $skillType, string $view, string $mode): View
    {
        $teacherId = Auth::id() ?? 0;

        $this->guardResultOwnership($result, $teacherId, $skillType);

        $result->load([
            'miniTest.chapter',
            'miniTest.course',
            'student.user',
            'studentAnswers' => function ($query) use ($mode) {
                $query->whereHas('question', fn ($q) => $q->where('loai', MiniTestQuestion::TYPE_ESSAY))
                    ->with('question')
                    ->orderBy('maCauHoi');

                if ($mode === 'speaking') {
                    $query->whereNotNull('answer_audio_url');
                }
            },
        ]);

        return view($view, [
            'type' => 'show',
            'mode' => $mode,
            'teacher' => Auth::user(),
            'result' => $result,
            'routePrefix' => $this->routePrefix($skillType),
            'badges' => $this->teacherSidebarBadges($teacherId),
        ]);
    }

    protected function gradeResult(Request $request, MiniTestResult $result, string $skillType, string $message): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;

        $this->guardResultOwnership($result, $teacherId, $skillType);

        $validated = $this->validateGrades($request);

        try {
            DB::beginTransaction();

            $manualScore = $this->gradeAnswers($validated['grades'], $result, $teacherId);

            $result->refresh();
            $autoScore = $result->auto_graded_score ?? 0;
            $totalScore = $autoScore + $manualScore;

            $result->update([
                'essay_score' => $manualScore,
                'diem' => $totalScore,
                'is_fully_graded' => true,
                'graded_at' => now(),
            ]);

            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            report($throwable);

            return back()->with('error', 'Unable to grade submission. Please try again.');
        }

        return redirect()
            ->route($this->routePrefix($skillType) . '.index')
            ->with('success', $message);
    }

    protected function bulkGrade(Request $request, string $skillType, string $messageTemplate): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;

        $validated = $this->validateBulkGrades($request);
        $gradedCount = 0;

        try {
            DB::beginTransaction();

            foreach ($validated['results'] as $payload) {
                $result = MiniTestResult::find($payload['result_id']);

                if (!$result) {
                    continue;
                }

                if (!$this->guardResultOwnership($result, $teacherId, $skillType, true)) {
                    continue;
                }

                $manualScore = $this->gradeAnswers($payload['answers'], $result, $teacherId);
                $autoScore = $result->auto_graded_score ?? 0;
                $totalScore = $autoScore + $manualScore;

                $result->update([
                    'essay_score' => $manualScore,
                    'diem' => $totalScore,
                    'is_fully_graded' => true,
                    'graded_at' => now(),
                ]);

                $gradedCount++;
            }

            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            report($throwable);

            return back()->with('error', 'Unable to grade submissions in bulk. Please try again.');
        }

        $message = str_replace('{count}', (string) $gradedCount, $messageTemplate);

        return redirect()
            ->route($this->routePrefix($skillType) . '.index')
            ->with('success', $message);
    }

    protected function guardResultOwnership(MiniTestResult $result, int $teacherId, string $skillType, bool $suppress = false): bool
    {
        $result->loadMissing('miniTest.course');

        $unauthorised = $result->miniTest->course->maND !== $teacherId
            || $result->miniTest->skill_type !== $skillType
            || $result->status === MiniTestResult::STATUS_IN_PROGRESS;

        if ($unauthorised) {
            if ($suppress) {
                return false;
            }

            abort(403, 'You are not authorised to grade this submission.');
        }

        return true;
    }

    protected function routePrefix(string $skillType): string
    {
        return $skillType === MiniTest::SKILL_SPEAKING
            ? 'teacher.grading.speaking'
            : 'teacher.grading.writing';
    }

    protected function validateGrades(Request $request): array
    {
        return $request->validate([
            'grades' => ['required', 'array'],
            'grades.*.answer_id' => ['required', 'integer'],
            'grades.*.score' => ['required', 'numeric', 'min:0'],
            'grades.*.feedback' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    protected function validateBulkGrades(Request $request): array
    {
        return $request->validate([
            'results' => ['required', 'array'],
            'results.*.result_id' => ['required', 'integer'],
            'results.*.answers' => ['required', 'array'],
            'results.*.answers.*.answer_id' => ['required', 'integer'],
            'results.*.answers.*.score' => ['required', 'numeric', 'min:0'],
            'results.*.answers.*.feedback' => ['nullable', 'string', 'max:500'],
        ]);
    }

    protected function gradeAnswers(array $items, MiniTestResult $result, int $teacherId): float
    {
        $manualScore = 0;
        $now = now();

        foreach ($items as $gradeData) {
            $answer = MiniTestStudentAnswer::with('question')
                ->where('maKQDG', $result->maKQDG)
                ->find($gradeData['answer_id']);

            if (!$answer) {
                continue;
            }

            $question = $answer->question;
            if (!$question || $question->loai !== MiniTestQuestion::TYPE_ESSAY) {
                continue;
            }

            if ($answer->isGraded()) {
                continue;
            }

            $maxScore = (float) $question->diem;
            $score = min((float) $gradeData['score'], $maxScore);

            $answer->update([
                'diem' => $score,
                'teacher_feedback' => $gradeData['feedback'] ?? null,
                'graded_at' => $now,
                'graded_by' => $teacherId,
                'is_correct' => $score > 0,
            ]);

            $manualScore += $score;
        }

        return $manualScore;
    }
}
