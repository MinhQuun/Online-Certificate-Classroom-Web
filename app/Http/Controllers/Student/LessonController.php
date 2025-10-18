<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;

class LessonController extends Controller
{
    public function show(int $maBH)
    {
        $lesson = Lesson::with([
            'materials',
            'chapter' => function ($chapterQuery) {
                $chapterQuery->with([
                    'miniTests' => fn($miniTestQuery) => $miniTestQuery
                        ->where('is_active', 1)
                        ->orderBy('thuTu')
                        ->with('materials'),
                    'course' => fn($courseQuery) => $courseQuery->with([
                        'chapters' => fn($chaptersQuery) => $chaptersQuery->with([
                            'lessons' => fn($lessonsQuery) => $lessonsQuery->orderBy('thuTu'),
                            'miniTests' => fn($nestedMiniTestQuery) => $nestedMiniTestQuery
                                ->where('is_active', 1)
                                ->orderBy('thuTu')
                                ->with('materials'),
                        ])->orderBy('thuTu'),
                        'finalTests' => fn($testQuery) => $testQuery
                            ->orderBy('maTest')
                            ->with('materials'),
                    ]),
                ]);
            },
        ])->findOrFail($maBH);

        $course = $lesson->chapter->course;

        return view('Student.lesson-show', compact('lesson', 'course'));
    }
}
