<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $courses = Course::published()
            ->when($q, fn($query) => $query->where('tenKH', 'like', "%$q%"))
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('Student.course-index', compact('courses', 'q'));
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

        return view('Student.course-show', compact('course'));
    }
}
