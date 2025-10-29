<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\MiniTest;
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

    /**
     * Hiển thị danh sách bài cần chấm điểm.
     */
    public function index(Request $request): View
    {
        $teacher = Auth::user();
        $teacherId = $teacher?->getKey() ?? 0;

        // Lấy các khóa học của giảng viên
        $courses = Course::where('maND', $teacherId)->get();

        $selectedCourseId = (int) $request->query('course');
        if ($selectedCourseId && !$courses->contains('maKH', $selectedCourseId)) {
            $selectedCourseId = 0;
        }

        // Lấy các bài cần chấm (có câu essay chưa chấm)
        $query = MiniTestResult::query()
            ->where('is_fully_graded', false)
            ->whereHas('miniTest', function ($q) use ($teacherId, $selectedCourseId) {
                $q->whereHas('course', fn($query) => $query->where('maND', $teacherId));
                if ($selectedCourseId) {
                    $q->where('maKH', $selectedCourseId);
                }
            })
            ->with([
                'miniTest.chapter',
                'miniTest.course',
                'student.user',
                'studentAnswers' => fn($q) => $q->whereNull('graded_at')
                    ->whereHas('question', fn($query) => $query->where('loai', 'essay'))
                    ->with('question')
            ])
            ->orderBy('nop_luc', 'desc')
            ->paginate(20);

        return view('Teacher.grading.index', [
            'teacher' => $teacher,
            'courses' => $courses,
            'selectedCourseId' => $selectedCourseId,
            'results' => $query,
            'badges' => $this->teacherSidebarBadges($teacherId),
        ]);
    }

    /**
     * Hiển thị chi tiết bài làm để chấm điểm.
     */
    public function show(MiniTestResult $result): View
    {
        $teacherId = Auth::id() ?? 0;

        // Kiểm tra quyền
        $courseTeacher = $result->miniTest->course->maND;
        if ($courseTeacher !== $teacherId) {
            abort(403, 'Bạn không có quyền chấm bài này.');
        }

        $result->load([
            'miniTest.chapter',
            'miniTest.course',
            'student.user',
            'studentAnswers.question',
        ]);

        return view('Teacher.grading.show', [
            'teacher' => Auth::user(),
            'result' => $result,
            'badges' => $this->teacherSidebarBadges($teacherId),
        ]);
    }

    /**
     * Lưu điểm chấm cho các câu essay.
     */
    public function grade(Request $request, MiniTestResult $result): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;

        // Kiểm tra quyền
        $courseTeacher = $result->miniTest->course->maND;
        if ($courseTeacher !== $teacherId) {
            abort(403);
        }

        $validated = $request->validate([
            'grades' => ['required', 'array'],
            'grades.*.answer_id' => ['required', 'integer'],
            'grades.*.score' => ['required', 'numeric', 'min:0'],
            'grades.*.feedback' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            DB::beginTransaction();

            $essayScore = 0;

            foreach ($validated['grades'] as $gradeData) {
                $answer = MiniTestStudentAnswer::findOrFail($gradeData['answer_id']);

                // Kiểm tra câu trả lời thuộc về result này
                if ($answer->maKQDG !== $result->maKQDG) {
                    continue;
                }

                // Kiểm tra điểm không vượt quá điểm tối đa của câu hỏi
                $maxScore = $answer->question->diem;
                $score = min($gradeData['score'], $maxScore);

                $answer->update([
                    'diem' => $score,
                    'teacher_feedback' => $gradeData['feedback'] ?? null,
                    'graded_at' => now(),
                    'graded_by' => $teacherId,
                    'is_correct' => $score > 0,
                ]);

                $essayScore += $score;
            }

            // Cập nhật điểm tổng
            $autoScore = $result->auto_graded_score ?? 0;
            $totalScore = $autoScore + $essayScore;

            $result->update([
                'essay_score' => $essayScore,
                'diem' => $totalScore,
                'is_fully_graded' => true,
                'graded_at' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('teacher.grading.index')
                ->with('success', 'Đã chấm điểm thành công. Tổng điểm: ' . number_format($totalScore, 2));
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi chấm điểm: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi chấm điểm. Vui lòng thử lại.');
        }
    }

    /**
     * Chấm nhanh nhiều bài cùng lúc.
     */
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

            foreach ($validated['results'] as $resultData) {
                $result = MiniTestResult::findOrFail($resultData['result_id']);

                // Kiểm tra quyền
                if ($result->miniTest->course->maND !== $teacherId) {
                    continue;
                }

                $essayScore = 0;

                foreach ($resultData['answers'] as $gradeData) {
                    $answer = MiniTestStudentAnswer::findOrFail($gradeData['answer_id']);

                    if ($answer->maKQDG !== $result->maKQDG) {
                        continue;
                    }

                    $maxScore = $answer->question->diem;
                    $score = min($gradeData['score'], $maxScore);

                    $answer->update([
                        'diem' => $score,
                        'teacher_feedback' => $gradeData['feedback'] ?? null,
                        'graded_at' => now(),
                        'graded_by' => $teacherId,
                        'is_correct' => $score > 0,
                    ]);

                    $essayScore += $score;
                }

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

            return redirect()
                ->route('teacher.grading.index')
                ->with('success', 'Đã chấm ' . count($validated['results']) . ' bài thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi chấm điểm hàng loạt: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra. Vui lòng thử lại.');
        }
    }
}
