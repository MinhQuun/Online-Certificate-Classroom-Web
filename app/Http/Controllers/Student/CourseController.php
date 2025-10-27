<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use App\Support\Cart\StudentCart;
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

        $query = Course::published()->with('category');

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

        return view('Student.course-index', compact(
            'courses',
            'q',
            'cartIds',
            'currentCategory'
        ));
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
                            ->where('is_active', 1)
                            ->orderBy('thuTu')
                            ->with('materials'),
                    ]);
                },
                'finalTests' => fn($testQuery) => $testQuery
                    ->orderBy('maTest')
                    ->with('materials'),
            ])
            ->firstOrFail();

        $isInCart = StudentCart::has($course->maKH);
        $cartIds = StudentCart::ids();

        // Determine auth + enrollment
        $userId = Auth::id();
        $isAuthenticated = !empty($userId);
        $isEnrolled = false;

        if ($isAuthenticated) {
            $student = DB::table('HOCVIEN')->where('maND', $userId)->first();
            if ($student) {
                $isEnrolled = DB::table('HOCVIEN_KHOAHOC')
                    ->where('maHV', $student->maHV)
                    ->where('maKH', $course->maKH)
                    ->exists();
            }
        }

        $relatedCourses = Course::published()
            ->where('maDanhMuc', $course->maDanhMuc)
            ->where('maKH', '!=', $course->maKH)
            ->with('category')
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        return view('Student.course-show', [
            'course'          => $course,
            'isInCart'        => $isInCart,
            'cartIds'         => $cartIds,
            'relatedCourses'  => $relatedCourses,
            'isAuthenticated' => $isAuthenticated,
            'isEnrolled'      => $isEnrolled,
        ]);
    }
}
