<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\MiniTestResult;
use App\Models\MiniTestStudentAnswer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GradingController extends Controller
{
    use LoadsTeacherContext;

    public function index(Request $request): View
    {
        $teacher = Auth::user();
        $teacherId = $teacher?->getKey() ?? 0;

        $courses = Course::where('maND', $teacherId)->get();

        $selectedCourseId = (int) $request->query('course');
        if ($selectedCourseId && !$courses->contains('maKH', $selectedCourseId)) {
            $selectedCourseId = 0;
        }

        $pendingResults = MiniTestResult::query()
            ->where('is_fully_graded', false)
            ->whereIn('status', [MiniTestResult::STATUS_SUBMITTED, MiniTestResult::STATUS_EXPIRED])
            ->whereHas('miniTest.course', fn ($query) => $query->where('maND', $teacherId))
            ->when($selectedCourseId, fn ($query) => $query->where('maKH', $selectedCourseId))
            ->with([
                'miniTest.chapter',
                'miniTest.course',
                'student.user',
                'studentAnswers' => fn ($query) => $query
                    ->whereNull('graded_at')
                    ->whereHas('question', fn ($q) => $q->where('loai', 'essay'))
                    ->with('question'),
            ])
            ->orderByDesc('nop_luc')
            ->paginate(20);

        return view('Teacher.grading', [
            'type' => 'index',
            'teacher' => $teacher,
            'courses' => $courses,
            'selectedCourseId' => $selectedCourseId,
            'results' => $pendingResults,
            'badges' => $this->teacherSidebarBadges($teacherId),
        ]);
    }

    public function show(MiniTestResult $result): View
    {
        $teacherId = Auth::id() ?? 0;

        abort_if($result->miniTest->course->maND !== $teacherId, 403, 'Bạn không có quyền chấm bài này.');
        abort_if($result->status === MiniTestResult::STATUS_IN_PROGRESS, 403, 'Bài làm vẫn đang trong quá trình thực hiện.');

        $result->load([
            'miniTest.chapter',
            'miniTest.course',
            'student.user',
            'studentAnswers.question',
        ]);

        return view('Teacher.grading', [
            'type' => 'show',
            'teacher' => Auth::user(),
            'result' => $result,
            'badges' => $this->teacherSidebarBadges($teacherId),
        ]);
    }

    public function grade(Request $request, MiniTestResult $result): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;

        abort_if($result->miniTest->course->maND !== $teacherId, 403);
        abort_if($result->status === MiniTestResult::STATUS_IN_PROGRESS, 403);

        $validated = $request->validate([
            'grades' => ['required', 'array'],
            'grades.*.answer_id' => ['required', 'integer'],
            'grades.*.score' => ['required', 'numeric', 'min:0'],
            'grades.*.feedback' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            DB::beginTransaction();

            $essayScore = $this->gradeAnswers($validated['grades'], $result, $teacherId);

            $result->refresh();
            $autoScore = $result->auto_graded_score ?? 0;
            $totalScore = $autoScore + $essayScore;

            $result->update([
                'essay_score' => $essayScore,
                'diem' => $totalScore,
                'is_fully_graded' => true,
                'graded_at' => now(),
            ]);

            DB::commit();
        } catch (\Throwable $throwable) {
            DB::rollBack();
            report($throwable);

            return back()->with('error', 'Có lỗi xảy ra khi chấm bài. Vui lòng thử lại.');
        }

        return redirect()
            ->route('teacher.grading.index')
            ->with('success', 'Đã chấm bài thành công. Tổng điểm: ' . number_format($totalScore, 2));
    }

    public function bulkGrade(Request $request): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;

        $validated = $request->validate([
            'results' => ['required', 'array'],
            'results.*.result_id' => ['required', 'integer'],
            'results.*.answers' => ['required', 'array'],
            'results.*.answers.*.answer_id' => ['required', 'integer'],
            'results.*.answers.*.score' => ['required', 'numeric', 'min:0'],
            'results.*.answers.*.feedback' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            DB::beginTransaction();

            foreach ($validated['results'] as $payload) {
                $result = MiniTestResult::findOrFail($payload['result_id']);

                if ($result->miniTest->course->maND !== $teacherId || $result->status === MiniTestResult::STATUS_IN_PROGRESS) {
                    continue;
                }

                $essayScore = $this->gradeAnswers($payload['answers'], $result, $teacherId);

                $autoScore = $result->auto_graded_score ?? 0;
                $totalScore = $autoScore + $essayScore;

                $result->update([
                    'essay_score' => $essayScore,
                    'diem' => $totalScore,
                    'is_fully_graded' => true,
                    'graded_at' => now(),
                ]);
            }

            DB::commit();
        } catch (\Throwable $throwable) {
            DB::rollBack();
            report($throwable);

            return back()->with('error', 'Không thể chấm hàng loạt. Vui lòng thử lại.');
        }

        return redirect()
            ->route('teacher.grading.index')
            ->with('success', 'Đã chấm ' . count($validated['results']) . ' bài.');
    }

    protected function gradeAnswers(array $items, MiniTestResult $result, int $teacherId): float
    {
        $essayScore = 0;
        $now = now();

        foreach ($items as $gradeData) {
            $answer = MiniTestStudentAnswer::with('question')
                ->where('maKQDG', $result->maKQDG)
                ->find($gradeData['answer_id']);

            if (!$answer) {
                continue;
            }

            $question = $answer->question;
            if (!$question || $question->loai !== 'essay') {
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

            $essayScore += $score;
        }

        return $essayScore;
    }
}
