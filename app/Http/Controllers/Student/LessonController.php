<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                        // 'finalTests' => fn($testQuery) => $testQuery
                        //     ->orderBy('maTest')
                        //     ->with('materials'),
                    ]),
                ]);
            },
        ])->findOrFail($maBH);

        $course = $lesson->chapter->course;

        // Gate: only enrolled users can access lessons except for Lesson 1 of Chapter 1
        $userId = Auth::id();
        $isAuthenticated = !empty($userId);
        $isEnrolled = false;

        if ($isAuthenticated) {
            $student = DB::table('HOCVIEN')->where('maND', $userId)->first();
            if ($student) {
                $isEnrolled = DB::table('HOCVIEN_KHOAHOC')
                    ->where('maHV', $student->maHV)
                    ->where('maKH', $course->maKH)
                    ->where('trangThai', 'ACTIVE')
                    ->exists();
            }
        }

        if (!$isEnrolled) {
            // Find the first lesson of the first chapter as the free preview
            $firstChapter = $course->chapters->sortBy('thuTu')->first();
            $firstLesson = $firstChapter?->lessons?->sortBy('thuTu')->first();
            $isFreePreview = $firstLesson && ($lesson->maBH === $firstLesson->maBH);

            if (!$isFreePreview) {
                return redirect()->route('student.courses.show', [
                    'slug'        => $course->slug,
                    'prompt'      => 'locked',
                    'locked'      => 'lesson',
                    'lesson_id'   => $lesson->maBH,
                ]);
            }
        }

        $lessonProgress = null;
        $enrollment = null;
        $miniTestResults = collect();

        if (!empty($student) && $isEnrolled) {
            $enrollment = Enrollment::where('maHV', $student->maHV)
                ->where('maKH', $course->maKH)
                ->where('trangThai', 'ACTIVE')
                ->first();

            $lessonProgress = LessonProgress::where('maHV', $student->maHV)
                ->where('maKH', $course->maKH)
                ->where('maBH', $lesson->maBH)
                ->first();

            // Lấy kết quả tốt nhất của từng mini-test trong chương này
            $chapterMiniTestIds = $lesson->chapter->miniTests->pluck('maMT');
            if ($chapterMiniTestIds->isNotEmpty()) {
                $miniTestResults = DB::table('KETQUA_MINITEST')
                    ->whereIn('maMT', $chapterMiniTestIds)
                    ->where('maHV', $student->maHV)
                    ->where('is_fully_graded', 1)
                    ->select('maMT', DB::raw('MAX(diem) as best_score'), DB::raw('COUNT(*) as attempts_used'))
                    ->groupBy('maMT')
                    ->get()
                    ->keyBy('maMT');
            }
        }

        return view('Student.lesson-show', [
            'lesson' => $lesson,
            'course' => $course,
            'isEnrolled' => $isEnrolled,
            'enrollment' => $enrollment,
            'lessonProgress' => $lessonProgress,
            'miniTestResults' => $miniTestResults,
        ]);
    }
}
