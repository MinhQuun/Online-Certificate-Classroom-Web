<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\MiniTest;
use App\Models\MiniTestResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ResultController extends Controller
{
    use LoadsTeacherContext;

    /**
     * Hiển thị danh sách điểm của học viên.
     */
    public function index(Request $request): View
    {
        $teacher = Auth::user();
        $teacherId = $teacher?->getKey() ?? 0;

        // Lấy các khóa học của giảng viên
        $courses = Course::where('maND', $teacherId)->get();

        $selectedCourseId = (int) $request->query('course');
        $selectedMiniTestId = (int) $request->query('minitest');
        $searchStudent = trim((string) $request->query('student', ''));

        if ($selectedCourseId && !$courses->contains('maKH', $selectedCourseId)) {
            $selectedCourseId = 0;
        }

        // Query results - chỉ lấy lần làm bài gần nhất của mỗi học viên
        $query = MiniTestResult::query()
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
            ])
            ->whereIn('maKQDG', function ($subQuery) use ($teacherId, $selectedCourseId, $selectedMiniTestId, $searchStudent) {
                $subQuery->select(DB::raw('MAX(maKQDG)'))
                    ->from('KETQUA_MINITEST')
                    ->whereExists(function ($existsQuery) use ($teacherId, $selectedCourseId) {
                        $existsQuery->select(DB::raw(1))
                            ->from('CHUONG_MINITEST')
                            ->whereColumn('CHUONG_MINITEST.maMT', 'KETQUA_MINITEST.maMT')
                            ->whereExists(function ($courseQuery) use ($teacherId) {
                                $courseQuery->select(DB::raw(1))
                                    ->from('KHOAHOC')
                                    ->whereColumn('KHOAHOC.maKH', 'CHUONG_MINITEST.maKH')
                                    ->where('KHOAHOC.maND', $teacherId);
                            })
                            ->when($selectedCourseId, fn($q) => $q->where('CHUONG_MINITEST.maKH', $selectedCourseId));
                    })
                    ->when($selectedMiniTestId, fn($q) => $q->where('maMT', $selectedMiniTestId))
                    ->when($searchStudent, function ($q) use ($searchStudent) {
                        $q->whereExists(function ($studentQuery) use ($searchStudent) {
                            $studentQuery->select(DB::raw(1))
                                ->from('HOCVIEN')
                                ->whereColumn('HOCVIEN.maHV', 'KETQUA_MINITEST.maHV')
                                ->whereExists(function ($userQuery) use ($searchStudent) {
                                    $userQuery->select(DB::raw(1))
                                        ->from('users')
                                        ->whereColumn('users.id', 'HOCVIEN.maND')
                                        ->where('users.name', 'like', "%{$searchStudent}%");
                                });
                        });
                    })
                    ->groupBy('maHV', 'maMT');
            });

        $results = $query->orderBy('nop_luc', 'desc')->paginate(30);

        // Get minitests for filter
        $miniTests = MiniTest::query()
            ->whereHas('course', fn($q) => $q->where('maND', $teacherId))
            ->when($selectedCourseId, fn($q) => $q->where('maKH', $selectedCourseId))
            ->with('chapter')
            ->orderBy('created_at', 'desc')
            ->get();

        // Statistics
        $stats = [
            'total_submissions' => MiniTestResult::whereHas('miniTest.course', fn($q) => $q->where('maND', $teacherId))->count(),
            'fully_graded' => MiniTestResult::whereHas('miniTest.course', fn($q) => $q->where('maND', $teacherId))->where('is_fully_graded', true)->count(),
            'pending_grading' => MiniTestResult::whereHas('miniTest.course', fn($q) => $q->where('maND', $teacherId))->where('is_fully_graded', false)->count(),
            'avg_score' => MiniTestResult::whereHas('miniTest.course', fn($q) => $q->where('maND', $teacherId))->where('is_fully_graded', true)->avg('diem') ?? 0,
        ];

        return view('Teacher.results.index', [
            'teacher' => $teacher,
            'courses' => $courses,
            'miniTests' => $miniTests,
            'selectedCourseId' => $selectedCourseId,
            'selectedMiniTestId' => $selectedMiniTestId,
            'searchStudent' => $searchStudent,
            'results' => $results,
            'stats' => $stats,
            'badges' => $this->teacherSidebarBadges($teacherId),
        ]);
    }

    /**
     * Xem chi tiết kết quả của một học viên.
     */
    public function show(MiniTestResult $result): View
    {
        $teacherId = Auth::id() ?? 0;

        // Kiểm tra quyền
        $courseTeacher = $result->miniTest->course->maND;
        if ($courseTeacher !== $teacherId) {
            abort(403, 'Bạn không có quyền xem kết quả này.');
        }

        $result->load([
            'miniTest.chapter',
            'miniTest.course',
            'student.user',
            'studentAnswers.question',
        ]);

        // Get all attempts for this student and minitest
        $allAttempts = MiniTestResult::where('maMT', $result->maMT)
            ->where('maHV', $result->maHV)
            ->orderBy('attempt_no', 'asc')
            ->get();

        return view('Teacher.results.show', [
            'teacher' => Auth::user(),
            'result' => $result,
            'allAttempts' => $allAttempts,
            'badges' => $this->teacherSidebarBadges($teacherId),
        ]);
    }
}
