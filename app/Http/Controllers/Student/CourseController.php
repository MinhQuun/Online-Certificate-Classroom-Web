<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use App\Models\CourseReview;
use App\Support\Cart\StudentCart;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $categorySlug = trim((string)$request->get('category'));

        $currentCategory = null;

        $query = Course::published()->with(['category', 'promotions']);

        $query->when($q, function ($query) use ($q) {
            $query->where('tenKH', 'like', "%$q%");
        });

        if ($categorySlug) {
            $currentCategory = Category::where('slug', $categorySlug)->first();

            $query->whereHas('category', function ($subQuery) use ($categorySlug) {
                $subQuery->where('slug', $categorySlug);
            });
        }

        $courses = $query->orderByDesc('created_at')->paginate(12);
        $cartIds = StudentCart::ids();
        $enrollment = $this->resolveStudentEnrollment();

        return view('Student.course-index', [
            'courses'            => $courses,
            'q'                  => $q,
            'cartIds'            => $cartIds,
            'currentCategory'    => $currentCategory,
            'enrolledCourseIds'  => $enrollment['enrolledCourseIds'],
            'activeCourseIds'    => $enrollment['activeCourseIds'],
        ]);
    }

    public function show(string $slug)
    {
        $course = Course::published()
            ->where('slug', $slug)
            ->with([
                'chapters' => function ($chapterQuery) {
                    $chapterQuery->with([
                        'lessons' => fn($lessonQuery) => $lessonQuery->orderBy('thuTu'),
                        'miniTests' => fn($miniTestQuery) => $miniTestQuery
                            ->visibleToStudents()
                            ->orderBy('thuTu')
                            ->with('materials'),
                    ]);
                },
                'teacher',
                'promotions',
            ])
            ->firstOrFail();

        $isInCart = StudentCart::has($course->maKH);
        $cartIds = StudentCart::ids();
        $enrollment = $this->resolveStudentEnrollment();
        $activeCourseIds = $enrollment['activeCourseIds'];
        $isAuthenticated = $enrollment['isAuthenticated'];
        $isEnrolled = in_array($course->maKH, $activeCourseIds, true);

        // Load student's best scores for each minitest
        $miniTestScores = [];
        if ($isAuthenticated && $enrollment['student']) {
            $studentId = $enrollment['student']->maHV;
            $miniTestIds = [];
            
            foreach ($course->chapters as $chapter) {
                foreach ($chapter->miniTests as $miniTest) {
                    $miniTestIds[] = $miniTest->maMT;
                }
            }

            if (!empty($miniTestIds)) {
                $scores = DB::table('ketqua_minitest')
                    ->select('maMT', DB::raw('MAX(diem) as best_score'), DB::raw('MAX(is_fully_graded) as is_fully_graded'))
                    ->where('maHV', $studentId)
                    ->whereIn('maMT', $miniTestIds)
                    ->groupBy('maMT')
                    ->get();

                foreach ($scores as $score) {
                    $miniTestScores[$score->maMT] = [
                        'best_score' => $score->best_score,
                        'is_fully_graded' => $score->is_fully_graded
                    ];
                }
            }
        }

        $relatedCourses = Course::published()
            ->where('maDanhMuc', $course->maDanhMuc)
            ->where('maKH', '!=', $course->maKH)
            ->with(['category', 'promotions'])
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        $reviewsQuery = CourseReview::with([
                'student.user' => function ($query) {
                    $query->select('maND', 'hoTen');
                },
                'student' => function ($query) {
                    $query->select('maHV', 'maND', 'hoTen');
                },
            ])
            ->where('maKH', $course->maKH)
            ->orderByDesc('created_at');

        $courseReviews = (clone $reviewsQuery)->paginate(10)->withQueryString();

        $ratingAggregate = CourseReview::selectRaw('AVG(diemSo) as average_rating, COUNT(*) as total_reviews')
            ->where('maKH', $course->maKH)
            ->first();

        $ratingBreakdown = CourseReview::selectRaw('CAST(diemSo AS UNSIGNED) as star, COUNT(*) as total')
            ->where('maKH', $course->maKH)
            ->groupBy('star')
            ->orderByDesc('star')
            ->pluck('total', 'star');

        $averageRating = $ratingAggregate && $ratingAggregate->average_rating !== null
            ? round((float) $ratingAggregate->average_rating, 1)
            : null;

        $studentReview = null;
        if ($isAuthenticated && $enrollment['student']) {
            $studentId = $enrollment['student']->maHV;
            $studentReview = CourseReview::where('maHV', $studentId)
                ->where('maKH', $course->maKH)
                ->first();
        }

        return view('Student.course-show', [
            'course'          => $course,
            'isInCart'        => $isInCart,
            'cartIds'         => $cartIds,
            'relatedCourses'  => $relatedCourses,
            'isAuthenticated' => $isAuthenticated,
            'isEnrolled'      => $isEnrolled,
            'enrolledCourseIds' => $activeCourseIds,
            'activeCourseIds'   => $activeCourseIds,
            'miniTestScores'  => $miniTestScores,
            'courseReviews'   => $courseReviews,
            'ratingSummary'   => [
                'average' => $averageRating,
                'total'   => (int) ($ratingAggregate->total_reviews ?? 0),
                'breakdown' => $ratingBreakdown,
            ],
            'studentReview'   => $studentReview,
        ]);
    }

    private function resolveStudentEnrollment(): array
    {
        $result = [
            'isAuthenticated'   => false,
            'student'           => null,
            'enrolledCourseIds' => [],
            'activeCourseIds'   => [],
        ];

        $userId = Auth::id();

        if (!$userId) {
            return $result;
        }

        $result['isAuthenticated'] = true;

        $student = DB::table('hocvien')->where('maND', $userId)->first();

        if (!$student) {
            return $result;
        }

        $result['student'] = $student;

        $rows = DB::table('hocvien_khoahoc')
            ->select('maKH', 'trangThai', 'maGoi', 'ngayNhapHoc', 'activated_at')
            ->where('maHV', $student->maHV)
            ->get();

        $active = [];
        $activeComboIds = [];
        $pendingRows = [];

        foreach ($rows as $row) {
            $courseId = (int) $row->maKH;

            if ($row->maGoi && $row->trangThai === 'ACTIVE') {
                $activeComboIds[] = (int) $row->maGoi;
            }

            if ($row->trangThai === 'ACTIVE') {
                $active[] = $courseId;
            } elseif ($row->trangThai === 'PENDING') {
                $pendingRows[$courseId] = $row;
                $active[] = $courseId;
            }
        }

        if (!empty($activeComboIds)) {
            $comboCoursesIds = DB::table('goi_khoa_hoc_chitiet')
                ->whereIn('maGoi', array_unique($activeComboIds))
                ->pluck('maKH')
                ->map(fn($id) => (int) $id)
                ->toArray();

            $active = array_unique(array_merge($active, $comboCoursesIds));
        }

        if (!empty($pendingRows)) {
            $this->activatePendingEnrollments($student->maHV, $pendingRows);
        }

        $active = array_values(array_unique($active));

        $result['enrolledCourseIds'] = $active;
        $result['activeCourseIds'] = $active;

        return $result;
    }

    /**
     * Convert legacy pending enrollments to ACTIVE so students can learn immediately.
     *
     * @param  int  $studentId
     * @param  array<int,object>  $pendingRows
     */
    private function activatePendingEnrollments(int $studentId, array $pendingRows): void
    {
        $courseIds = array_keys($pendingRows);

        if (empty($courseIds)) {
            return;
        }

        $durations = DB::table('khoahoc')
            ->whereIn('maKH', $courseIds)
            ->pluck('thoiHanNgay', 'maKH')
            ->map(fn($value) => (int) $value);

        $now = Carbon::now();

        foreach ($pendingRows as $courseId => $row) {
            $start = $row->ngayNhapHoc ? Carbon::parse($row->ngayNhapHoc) : $now->copy();
            $activatedAt = $row->activated_at ? Carbon::parse($row->activated_at) : $start->copy();
            $durationDays = (int) ($durations[$courseId] ?? 0);
            $expiresAt = $durationDays > 0 ? $activatedAt->copy()->addDays($durationDays) : null;

            DB::table('hocvien_khoahoc')
                ->where('maHV', $studentId)
                ->where('maKH', $courseId)
                ->update([
                    'trangThai' => 'ACTIVE',
                    'activated_at' => $activatedAt->toDateTimeString(),
                    'expires_at' => $expiresAt?->toDateTimeString(),
                    'updated_at' => $now->toDateTimeString(),
                ]);
        }
    }
}
