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
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class MiniTestController extends Controller
{
    /**
     * Hiển thị danh sách mini-test của chương (chỉ các mini-test đã công bố).
     */
    public function index(Chapter $chapter): View
    {
        $type = 'index';
        $user = Auth::user();
        $student = Student::where('maND', $user->maND)->first();

        // Kiểm tra xem học viên đã ghi danh khóa học chưa
        $enrollment = Enrollment::where('maHV', $student->maHV)
            ->where('maKH', $chapter->maKH)
            ->whereIn('trangThai', ['ACTIVE', 'PENDING'])
            ->first();

        if (!$enrollment) {
            abort(403, 'Bạn chưa ghi danh khóa học này.');
        }

        // Lấy các mini-test đã công bố
        $miniTests = $chapter->miniTests()
            ->where('is_published', true)
            ->where('is_active', true)
            ->with('questions')
            ->orderBy('thuTu')
            ->get();

        // Lấy kết quả các lần làm bài của học viên
        $results = MiniTestResult::where('maHV', $student->maHV)
            ->where('maKH', $chapter->maKH)
            ->whereIn('maMT', $miniTests->pluck('maMT'))
            ->get()
            ->groupBy('maMT');

        return view('Student.minitests', compact('type', 'chapter', 'miniTests', 'results', 'student'));
    }

    /**
     * Hiển thị chi tiết mini-test và form làm bài.
     */
    public function show(MiniTest $miniTest): View|RedirectResponse
    {
        $type = 'show';
        // Kiểm tra mini-test đã công bố chưa
        if (!$miniTest->is_published || !$miniTest->is_active) {
            abort(404, 'Mini-test không tồn tại hoặc chưa được công bố.');
        }

        $user = Auth::user();
        $student = Student::where('maND', $user->maND)->first();

        // Kiểm tra ghi danh
        $enrollment = Enrollment::where('maHV', $student->maHV)
            ->where('maKH', $miniTest->maKH)
            ->whereIn('trangThai', ['ACTIVE', 'PENDING'])
            ->first();

        if (!$enrollment) {
            abort(403, 'Bạn chưa ghi danh khóa học này.');
        }

        // Đếm số lần đã làm
        $attemptCount = MiniTestResult::where('maMT', $miniTest->maMT)
            ->where('maHV', $student->maHV)
            ->where('maKH', $miniTest->maKH)
            ->count();

        // Load câu hỏi
        $miniTest->load(['questions' => fn($q) => $q->orderBy('thuTu'), 'chapter', 'course']);

        // Kiểm tra nếu chưa có câu hỏi
        if ($miniTest->questions->isEmpty()) {
            return redirect()->back()->with('error', 'Bài kiểm tra này chưa có câu hỏi. Vui lòng liên hệ giảng viên.');
        }

        $attemptNo = $attemptCount + 1;

        return view('Student.minitests', compact('type', 'miniTest', 'student', 'attemptNo'));
    }

    /**
     * Học viên nộp bài.
     */
    public function submit(Request $request, MiniTest $miniTest): RedirectResponse
    {
        $user = Auth::user();
        $student = Student::where('maND', $user->maND)->first();

        // Kiểm tra ghi danh
        $enrollment = Enrollment::where('maHV', $student->maHV)
            ->where('maKH', $miniTest->maKH)
            ->whereIn('trangThai', ['ACTIVE', 'PENDING'])
            ->first();

        if (!$enrollment) {
            abort(403);
        }

        // Đếm số lần đã làm
        $attemptCount = MiniTestResult::where('maMT', $miniTest->maMT)
            ->where('maHV', $student->maHV)
            ->where('maKH', $miniTest->maKH)
            ->count();

        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['required'], // Có thể là string (choice) hoặc text (essay)
        ]);

        try {
            DB::beginTransaction();

            // Tạo kết quả mới
            $result = MiniTestResult::create([
                'maMT' => $miniTest->maMT,
                'maHV' => $student->maHV,
                'maKH' => $miniTest->maKH,
                'attempt_no' => $attemptCount + 1,
                'nop_luc' => now(),
                'completed_at' => now(),
            ]);

            $totalScore = 0;
            $autoGradedScore = 0;
            $hasEssay = false;

            // Xử lý từng câu trả lời
            foreach ($validated['answers'] as $questionId => $answer) {
                $question = MiniTestQuestion::findOrFail($questionId);

                $isCorrect = null;
                $score = null;

                if ($question->loai === 'essay') {
                    // Câu tự luận - chưa chấm
                    $hasEssay = true;
                    MiniTestStudentAnswer::create([
                        'maKQDG' => $result->maKQDG,
                        'maCauHoi' => $questionId,
                        'maHV' => $student->maHV,
                        'answer_text' => $answer,
                    ]);
                } else {
                    // Câu trắc nghiệm - tự động chấm
                    $isCorrect = $question->checkAnswer($answer);
                    $score = $isCorrect ? $question->diem : 0;
                    $autoGradedScore += $score;
                    $totalScore += $score;

                    MiniTestStudentAnswer::create([
                        'maKQDG' => $result->maKQDG,
                        'maCauHoi' => $questionId,
                        'maHV' => $student->maHV,
                        'answer_choice' => $answer,
                        'is_correct' => $isCorrect,
                        'diem' => $score,
                        'graded_at' => now(),
                    ]);
                }
            }

            // Cập nhật điểm
            $result->update([
                'auto_graded_score' => $autoGradedScore,
                'diem' => $hasEssay ? null : $totalScore, // Nếu có essay thì chưa có điểm cuối
                'is_fully_graded' => !$hasEssay, // Nếu không có essay thì đã chấm xong
                'graded_at' => $hasEssay ? null : now(),
            ]);

            DB::commit();

            if ($hasEssay) {
                return redirect()
                    ->route('student.minitests.result', $result->maKQDG)
                    ->with('success', 'Đã nộp bài thành công. Kết quả sẽ được cập nhật sau khi giảng viên chấm điểm.');
            }

            return redirect()
                ->route('student.minitests.result', $result->maKQDG)
                ->with('success', 'Đã nộp bài thành công. Điểm của bạn: ' . number_format($totalScore, 2) . '/' . $miniTest->max_score);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi nộp bài mini-test: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi nộp bài. Vui lòng thử lại.');
        }
    }

    /**
     * Xem kết quả làm bài.
     */
    public function result(MiniTestResult $result): View
    {
        $type = 'result';
        $user = Auth::user();
        $student = Student::where('maND', $user->maND)->first();

        // Kiểm tra quyền xem kết quả
        if ($result->maHV !== $student->maHV) {
            abort(403, 'Bạn không có quyền xem kết quả này.');
        }

        $result->load([
            'miniTest.chapter',
            'miniTest.course',
            'studentAnswers.question',
        ]);

        // Đếm số câu đúng/sai/tự luận
        $correctCount = $result->studentAnswers->where('is_correct', true)->count();
        $incorrectCount = $result->studentAnswers->where('is_correct', false)->count();
        $essayCount = $result->studentAnswers->whereNull('is_correct')->count();

        // Tính số lần còn lại
        $totalAttempts = MiniTestResult::where('maMT', $result->maMT)
            ->where('maHV', $student->maHV)
            ->where('maKH', $result->maKH)
            ->count();
        $attemptsLeft = $result->miniTest->attempts_allowed - $totalAttempts;

        return view('Student.minitests', compact('type', 'result', 'student', 'correctCount', 'incorrectCount', 'essayCount', 'attemptsLeft'));
    }
}
