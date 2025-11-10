<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonDiscussion;
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
                        ->visibleToStudents()
                        ->orderBy('thuTu')
                        ->with('materials'),
                    'course' => fn($courseQuery) => $courseQuery->with([
                        'chapters' => fn($chaptersQuery) => $chaptersQuery->with([
                            'lessons' => fn($lessonsQuery) => $lessonsQuery->orderBy('thuTu'),
                            'miniTests' => fn($nestedMiniTestQuery) => $nestedMiniTestQuery
                                ->visibleToStudents()
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
            $student = DB::table('hocvien')->where('maND', $userId)->first();
            if ($student) {
                $isEnrolled = DB::table('hocvien_khoahoc')
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

            // Láº¥y káº¿t quáº£ tá»‘t nháº¥t cá»§a tá»«ng mini-test trong chÆ°Æ¡ng nÃ y
            $chapterMiniTestIds = $lesson->chapter->miniTests->pluck('maMT');
            if ($chapterMiniTestIds->isNotEmpty()) {
                $miniTestResults = DB::table('ketqua_minitest')
                    ->whereIn('maMT', $chapterMiniTestIds)
                    ->where('maHV', $student->maHV)
                    ->where('is_fully_graded', 1)
                    ->select('maMT', DB::raw('MAX(diem) as best_score'), DB::raw('COUNT(*) as attempts_used'))
                    ->groupBy('maMT')
                    ->get()
                    ->keyBy('maMT');
            }
        }

        $authUser = $isAuthenticated ? Auth::user() : null;

        $discussionCount = LessonDiscussion::visible()
            ->where('maBH', $lesson->maBH)
            ->count();

        $discussionPermissions = [
            'can_post'     => false,
            'can_reply'    => false,
            'can_moderate' => false,
            'role'         => $authUser?->vaiTro,
        ];

        if ($authUser) {
            if ($authUser->vaiTro === 'ADMIN') {
                $discussionPermissions['can_post'] = true;
                $discussionPermissions['can_reply'] = true;
                $discussionPermissions['can_moderate'] = true;
            } elseif ($authUser->vaiTro === 'GIANG_VIEN') {
                $isOwner = $course->maND === $authUser->maND;
                $discussionPermissions['can_reply'] = $isOwner;
                $discussionPermissions['can_moderate'] = $isOwner;
            } elseif ($authUser->vaiTro === 'HOC_VIEN') {
                $discussionPermissions['can_post'] = $isEnrolled;
                $discussionPermissions['can_reply'] = $isEnrolled;
            }
        }

        $discussionBootstrap = [
            'lessonId'    => $lesson->maBH,
            'courseId'    => $course->maKH,
            'total'       => $discussionCount,
            'fetchUrl'    => route('student.lessons.discussions.index', ['lesson' => $lesson->maBH]),
            'storeUrl'    => $discussionPermissions['can_post']
                ? route('student.lessons.discussions.store', ['lesson' => $lesson->maBH])
                : null,
            'replyUrlTemplate' => $discussionPermissions['can_reply']
                ? route('student.lessons.discussions.replies.store', ['lesson' => $lesson->maBH, 'discussion' => '__DISCUSSION__'])
                : null,
            'deleteUrlTemplate' => $authUser
                ? route('student.lessons.discussions.destroy', ['lesson' => $lesson->maBH, 'discussion' => '__DISCUSSION__'])
                : null,
            'deleteReplyUrlTemplate' => $authUser
                ? route('student.lessons.discussions.replies.destroy', [
                    'lesson'      => $lesson->maBH,
                    'discussion'  => '__DISCUSSION__',
                    'reply'       => '__REPLY__',
                ])
                : null,
            'moderation' => $discussionPermissions['can_moderate']
                ? [
                    'pinUrlTemplate' => route('teacher.discussions.pin', ['discussion' => '__DISCUSSION__']),
                    'lockUrlTemplate' => route('teacher.discussions.lock', ['discussion' => '__DISCUSSION__']),
                    'statusUrlTemplate' => route('teacher.discussions.status', ['discussion' => '__DISCUSSION__']),
                ]
                : null,
            'permissions' => $discussionPermissions,
            'user' => $authUser ? [
                'id'    => $authUser->maND,
                'name'  => $authUser->hoTen,
                'role'  => $authUser->vaiTro,
            ] : null,
        ];

        return view('Student.lesson-show', [
            'lesson' => $lesson,
            'course' => $course,
            'isEnrolled' => $isEnrolled,
            'enrollment' => $enrollment,
            'lessonProgress' => $lessonProgress,
            'miniTestResults' => $miniTestResults,
            'discussionBootstrap' => $discussionBootstrap,
        ]);
    }
}
