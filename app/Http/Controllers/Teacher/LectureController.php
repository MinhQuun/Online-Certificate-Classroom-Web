<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Teacher\LoadsTeacherContext;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonDiscussion;
use App\Models\Material;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Log;

class LectureController extends Controller
{
    use LoadsTeacherContext;

    /**
     * CÃ¡c loáº¡i tÃ i nguyÃªn Ä‘Æ°á»£c há»— trá»£ vá»›i thÃ´ng tin hiá»ƒn thá»‹.
     *
     * @var array
     */
    protected $resourcePresets = [
        'video/mp4' => ['label' => 'Video (.mp4)', 'default_type' => 'Video', 'icon' => 'bi-play-circle'],
        'application/pdf' => ['label' => 'PDF (.pdf)', 'default_type' => 'PDF', 'icon' => 'bi-file-earmark-pdf'],
        'application/vnd.ms-powerpoint' => ['label' => 'PowerPoint (.ppt)', 'default_type' => 'PowerPoint', 'icon' => 'bi-easel3'],
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => ['label' => 'PowerPoint (.pptx)', 'default_type' => 'PowerPoint', 'icon' => 'bi-easel3'],
        'application/zip' => ['label' => 'Táº­p tin nÃ©n (.zip)', 'default_type' => 'ZIP', 'icon' => 'bi-file-zip'],
        'audio/mpeg' => ['label' => 'Audio (.mp3)', 'default_type' => 'Audio', 'icon' => 'bi-music-note-beamed'],
        'text/html' => ['label' => 'LiÃªn káº¿t', 'default_type' => 'Link', 'icon' => 'bi-link-45deg'],
    ];

    /**
     * CÃ¡c loáº¡i bÃ i giáº£ng Ä‘Æ°á»£c há»— trá»£.
     *
     * @var array
     */
    protected $lessonTypes = [
        'video' => ['label' => 'Video bÃ i giáº£ng', 'icon' => 'bi-camera-video'],
        'doc' => ['label' => 'TÃ i liá»‡u', 'icon' => 'bi-file-earmark-text'],
        'assignment' => ['label' => 'BÃ i táº­p', 'icon' => 'bi-pencil-square'],
    ];

    /**
     * Hiá»ƒn thá»‹ danh sÃ¡ch bÃ i giáº£ng cá»§a giáº£ng viÃªn.
     */
    public function index(Request $request)
    {
        $teacher = Auth::user();
        $teacherId = $teacher?->getKey() ?? 0;

        $courses = Course::with(['chapters.lessons.materials' => fn ($query) => $query->orderBy('created_at')])
            ->where('maND', $teacherId)
            ->orderBy('tenKH')
            ->get();

        if ($courses->isNotEmpty()) {
            $studentCounts = DB::table('hocvien_khoahoc')
                ->select('maKH', DB::raw('COUNT(DISTINCT maHV) as total'))
                ->whereIn('maKH', $courses->pluck('maKH'))
                ->groupBy('maKH')
                ->pluck('total', 'maKH');

            foreach ($courses as $course) {
                $course->students_count = $studentCounts[$course->maKH] ?? 0;
            }
        }

        $selectedCourseId = (int) $request->query('course');
        if ($selectedCourseId && !$courses->contains('maKH', $selectedCourseId)) {
            $selectedCourseId = 0;
        }

        $activeCourse = $selectedCourseId
            ? $courses->firstWhere('maKH', $selectedCourseId)
            : $courses->first();

        $discussionConfigs = [];

        if ($activeCourse) {
            $lessonIds = $activeCourse->chapters
                ->flatMap(fn ($chapter) => $chapter->lessons->pluck('maBH'))
                ->filter()
                ->values();

            $discussionCounts = $lessonIds->isNotEmpty()
                ? LessonDiscussion::visible()
                    ->whereIn('maBH', $lessonIds)
                    ->select('maBH', DB::raw('COUNT(*) as total'))
                    ->groupBy('maBH')
                    ->pluck('total', 'maBH')
                : collect();

            foreach ($activeCourse->chapters as $chapter) {
                foreach ($chapter->lessons as $lesson) {
                    $lessonId = $lesson->maBH;

                    $discussionConfigs[$lessonId] = [
                        'lessonId'     => $lessonId,
                        'lessonTitle'  => $lesson->tieuDe,
                        'lessonOrder'  => $lesson->thuTu,
                        'chapterTitle' => $chapter->tenChuong,
                        'courseId'     => $activeCourse->maKH,
                        'total'        => (int) ($discussionCounts[$lessonId] ?? 0),
                        'fetchUrl'     => route('student.lessons.discussions.index', ['lesson' => $lessonId]),
                        'storeUrl'     => null,
                        'replyUrlTemplate' => route('student.lessons.discussions.replies.store', [
                            'lesson'     => $lessonId,
                            'discussion' => '__DISCUSSION__',
                        ]),
                        'deleteUrlTemplate' => route('student.lessons.discussions.destroy', [
                            'lesson'     => $lessonId,
                            'discussion' => '__DISCUSSION__',
                        ]),
                        'deleteReplyUrlTemplate' => route('student.lessons.discussions.replies.destroy', [
                            'lesson'     => $lessonId,
                            'discussion' => '__DISCUSSION__',
                            'reply'      => '__REPLY__',
                        ]),
                        'moderation' => [
                            'pinUrlTemplate' => route('teacher.discussions.pin', ['discussion' => '__DISCUSSION__']),
                            'lockUrlTemplate' => route('teacher.discussions.lock', ['discussion' => '__DISCUSSION__']),
                            'statusUrlTemplate' => route('teacher.discussions.status', ['discussion' => '__DISCUSSION__']),
                        ],
                        'permissions' => [
                            'can_post'     => false,
                            'can_reply'    => true,
                            'can_moderate' => true,
                            'role'         => $teacher?->vaiTro,
                        ],
                        'user' => [
                            'id'   => $teacher?->maND,
                            'name' => $teacher?->hoTen,
                            'role' => $teacher?->vaiTro,
                        ],
                    ];
                }
            }
        }

        return view('Teacher.lectures', [
            'teacher' => $teacher,
            'courses' => $courses,
            'activeCourse' => $activeCourse,
            'lessonTypes' => $this->lessonTypes,
            'resourcePresets' => $this->resourcePresets,
            'badges' => $this->teacherSidebarBadges($teacherId),
            'discussionConfigs' => $discussionConfigs,
        ]);
    }

    /**
     * LÆ°u bÃ i giáº£ng má»›i.
     */
    public function store(Request $request): RedirectResponse
    {
        $teacher = Auth::user();
        $teacherId = $teacher?->getKey() ?? 0;

        $validated = $request->validate([
            'course_id' => ['required', 'integer'],
            'chapter_id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:150'],
            'order' => ['nullable', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:1000'],
            'type' => ['required', Rule::in(array_keys($this->lessonTypes))],
        ]);

        $chapter = Chapter::where('maChuong', $validated['chapter_id'])
            ->whereHas('course', fn ($query) => $query->where('maND', $teacherId))
            ->firstOrFail();

        return DB::transaction(function () use ($validated, $chapter, $request) {
            $order = $validated['order'] ?? ($chapter->lessons()->max('thuTu') + 1);

            if ($order > 1) {
                DB::table('baihoc')
                    ->where('maChuong', $chapter->maChuong)
                    ->where('thuTu', '>=', $order)
                    ->increment('thuTu');
            }

            $lesson = Lesson::create([
                'maChuong' => $chapter->maChuong,
                'tieuDe' => $validated['title'],
                'moTa' => $validated['description'] ?? null,
                'thuTu' => $order,
                'loai' => $validated['type'],
            ]);

            // Táº¡o thÆ° má»¥c bÃ i giáº£ng trÃªn R2
            $courseDir = Str::slug($chapter->course->tenKH);
            $chapterDir = "{$courseDir}/" . Str::slug($chapter->tenChuong);
            $lessonDir = "{$chapterDir}/" . Str::slug($lesson->tieuDe);
            try {
                if (!Storage::disk('s3')->exists($lessonDir)) {
                    Storage::disk('s3')->makeDirectory($lessonDir);
                }
            } catch (\Exception $e) {
                Log::error('Lá»—i táº¡o thÆ° má»¥c bÃ i giáº£ng trÃªn R2: ' . $e->getMessage());
                return redirect()
                    ->route('teacher.lectures.index', ['course' => $chapter->maKH])
                    ->with('error', 'KhÃ´ng thá»ƒ táº¡o thÆ° má»¥c bÃ i giáº£ng trÃªn R2. Vui lÃ²ng thá»­ láº¡i.');
            }

            return redirect()
                ->route('teacher.lectures.index', [
                    'course' => $chapter->maKH,
                    '_fragment' => 'lesson-' . $lesson->maBH,
                ])
                ->with('success', 'ÄÃ£ táº¡o bÃ i giáº£ng má»›i.');
        });
    }

    /**
     * Cáº­p nháº­t bÃ i giáº£ng.
     */
    public function update(Request $request, Lesson $lesson): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;
        $this->authorizeLesson($lesson, $teacherId);

        $validated = $request->validate([
            'course_id' => ['required', 'integer'],
            'chapter_id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:150'],
            'order' => ['nullable', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:1000'],
            'type' => ['required', Rule::in(array_keys($this->lessonTypes))],
        ]);

        return DB::transaction(function () use ($validated, $lesson, $request) {
            $newOrder = $validated['order'] ?? $lesson->thuTu;
            $oldChapterId = $lesson->maChuong;
            $newChapterId = $validated['chapter_id'];

            // Äá»•i tÃªn thÆ° má»¥c trÃªn R2 náº¿u tiÃªu Ä‘á» thay Ä‘á»•i hoáº·c chuyá»ƒn chÆ°Æ¡ng
            $course = Course::findOrFail($validated['course_id']);
            $oldChapter = Chapter::findOrFail($oldChapterId);
            $newChapter = Chapter::findOrFail($newChapterId);
            $courseDir = Str::slug($course->tenKH);
            $oldChapterDir = "{$courseDir}/" . Str::slug($oldChapter->tenChuong);
            $newChapterDir = "{$courseDir}/" . Str::slug($newChapter->tenChuong);
            $oldLessonDir = "{$oldChapterDir}/" . Str::slug($lesson->tieuDe);
            $newLessonDir = "{$newChapterDir}/" . Str::slug($validated['title']);

            if ($lesson->tieuDe !== $validated['title'] || $oldChapterId !== $newChapterId) {
                try {
                    if (Storage::disk('s3')->exists($oldLessonDir)) {
                        $files = Storage::disk('s3')->allFiles($oldLessonDir);
                        foreach ($files as $file) {
                            $newFile = str_replace($oldLessonDir, $newLessonDir, $file);
                            if (!Storage::disk('s3')->exists(dirname($newFile))) {
                                Storage::disk('s3')->makeDirectory(dirname($newFile));
                            }
                            Storage::disk('s3')->copy($file, $newFile);
                        }
                        Storage::disk('s3')->deleteDirectory($oldLessonDir);
                    } else {
                        Storage::disk('s3')->makeDirectory($newLessonDir);
                    }
                } catch (\Exception $e) {
                    Log::error('Lá»—i Ä‘á»•i tÃªn thÆ° má»¥c bÃ i giáº£ng trÃªn R2: ' . $e->getMessage());
                    return redirect()
                        ->route('teacher.lectures.index', ['course' => $lesson->chapter->maKH])
                        ->with('error', 'KhÃ´ng thá»ƒ Ä‘á»•i tÃªn thÆ° má»¥c bÃ i giáº£ng trÃªn R2. Vui lÃ²ng thá»­ láº¡i.');
                }
            }

            // Cáº­p nháº­t thá»© tá»± bÃ i giáº£ng
            if ($newOrder !== $lesson->thuTu || $oldChapterId !== $newChapterId) {
                if ($oldChapterId === $newChapterId) {
                    if ($newOrder < $lesson->thuTu) {
                        DB::table('baihoc')
                            ->where('maChuong', $lesson->maChuong)
                            ->whereBetween('thuTu', [$newOrder, $lesson->thuTu - 1])
                            ->increment('thuTu');
                    } else {
                        DB::table('baihoc')
                            ->where('maChuong', $lesson->maChuong)
                            ->whereBetween('thuTu', [$lesson->thuTu + 1, $newOrder])
                            ->decrement('thuTu');
                    }
                } else {
                    DB::table('baihoc')
                        ->where('maChuong', $oldChapterId)
                        ->where('thuTu', '>', $lesson->thuTu)
                        ->decrement('thuTu');
                    DB::table('baihoc')
                        ->where('maChuong', $newChapterId)
                        ->where('thuTu', '>=', $newOrder)
                        ->increment('thuTu');
                }
            }

            $lesson->update([
                'maChuong' => $newChapterId,
                'tieuDe' => $validated['title'],
                'moTa' => $validated['description'] ?? null,
                'thuTu' => $newOrder,
                'loai' => $validated['type'],
            ]);

            return redirect()
                ->route('teacher.lectures.index', [
                    'course' => $lesson->chapter->maKH,
                    '_fragment' => 'lesson-' . $lesson->maBH,
                ])
                ->with('success', 'ÄÃ£ cáº­p nháº­t bÃ i giáº£ng.');
        });
    }

    /**
     * LÆ°u tÃ i nguyÃªn cho bÃ i giáº£ng.
     */
    public function storeMaterial(Request $request, Lesson $lesson): JsonResponse
    {
        Log::info('Received data for storeMaterial: ', $request->all());
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys($this->resourcePresets))],
            'url' => ['nullable', 'url', 'max:2048'],
            'file' => ['nullable', 'file', 'max:51200'], // 50MB
            'visibility' => ['required', Rule::in(['public', 'private'])],
            'summary' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $fileUrl = null;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();

                // Táº¡o Ä‘Æ°á»ng dáº«n thÆ° má»¥c dá»±a trÃªn course, chapter, vÃ  lesson
                $course = $lesson->chapter->course;
                $courseDir = Str::slug($course->tenKH);
                $chapterDir = Str::slug($lesson->chapter->tenChuong);
                $lessonDir = Str::slug($lesson->tieuDe);
                $fullPath = "{$courseDir}/{$chapterDir}/{$lessonDir}/{$fileName}";

                // Upload file vÃ o thÆ° má»¥c cá»¥ thá»ƒ
                $filePath = $file->storeAs('', $fullPath, 's3');
                Storage::disk('s3')->setVisibility($filePath, 'public'); // Äáº·t quyá»n public

                // Táº¡o URL cÃ´ng khai sá»­ dá»¥ng PUBLIC_R2_URL
                $publicUrl = env('PUBLIC_R2_URL') . '/' . $fullPath;
                $fileUrl = $publicUrl;
                Log::info("Uploaded file to R2 with public access: {$fileUrl}");
            } elseif ($request->filled('url')) {
                $fileUrl = $request->input('url');
            }

            if ($fileUrl) {
                $this->createMaterialFromPayload($lesson, [
                    'name' => $validated['name'],
                    'type' => $this->resourcePresets[$validated['type']]['default_type'] ?? $validated['type'],
                    'url' => $fileUrl,
                    'size' => $request->hasFile('file') ? $this->formatFileSize($file->getSize()) : null,
                    'visibility' => $validated['visibility'],
                    'summary' => $validated['summary'],
                    'mime' => $request->hasFile('file') ? $file->getMimeType() : $this->guessMime($validated['type'], $fileUrl),
                ]);
                Log::info("Material created successfully for lesson ID: {$lesson->maBH}");
            }

            return response()->json(['success' => true, 'message' => 'ÄÃ£ thÃªm tÃ i nguyÃªn cho bÃ i giáº£ng.']);
        } catch (\Exception $e) {
            Log::error('Lá»—i upload tÃ i nguyÃªn: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'KhÃ´ng thá»ƒ upload file lÃªn R2. Vui lÃ²ng thá»­ láº¡i. ' . $e->getMessage()], 500);
        }
    }
    /**
     * XÃ³a tÃ i nguyÃªn.
     */
    public function destroyMaterial(Material $material): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;
        $lesson = $material->lesson()->with('chapter')->firstOrFail();

        $this->authorizeLesson($lesson, $teacherId);

        $courseId = $lesson->chapter->maKH;

        // XÃ³a file trÃªn R2 náº¿u tá»“n táº¡i
        try {
            $filePath = parse_url($material->public_url, PHP_URL_PATH);
            if ($filePath && Storage::disk('s3')->exists($filePath)) {
                Storage::disk('s3')->delete($filePath);
            }
        } catch (\Exception $e) {
            Log::error('Lá»—i xÃ³a file trÃªn R2: ' . $e->getMessage());
        }

        $material->delete();

        return redirect()
            ->route('teacher.lectures.index', [
                'course' => $courseId,
                '_fragment' => 'lesson-' . $lesson->maBH,
            ])
            ->with('success', 'ÄÃ£ xÃ³a tÃ i nguyÃªn.');
    }

    /**
     * Kiá»ƒm tra quyá»n truy cáº­p bÃ i giáº£ng.
     */
    protected function authorizeLesson(Lesson $lesson, int $teacherId): void
    {
        $courseTeacher = $lesson->chapter
            ->course()
            ->value('maND');

        abort_if($courseTeacher !== $teacherId, 403, 'Báº¡n khÃ´ng cÃ³ quyá»n trÃªn bÃ i giáº£ng nÃ y.');
    }

    /**
     * Táº¡o tÃ i nguyÃªn tá»« payload.
     */
    protected function createMaterialFromPayload(Lesson $lesson, array $resource): void
    {
        try {
            $name = $resource['name'] ?? $lesson->tieuDe;
            $type = $resource['type'] ?? 'TÃ i nguyÃªn';
            $mime = $resource['mime'] ?? $this->guessMime($type, $resource['url'] ?? '');

            $lesson->materials()->create([
                'tenTL' => $name,
                'loai' => $type,
                'public_url' => $resource['url'],
                'mime_type' => $mime,
                'kichThuoc' => $resource['size'] ?? null,
                'moTa' => $resource['summary'] ?? null,
                'visibility' => $resource['visibility'] ?? 'public',
            ]);
        } catch (\Exception $e) {
            Log::error('Lá»—i táº¡o material: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ÄoÃ¡n MIME type dá»±a trÃªn loáº¡i hoáº·c URL.
     */
    protected function guessMime(string $type, string $url): string
    {
        $type = strtolower($type);
        $map = [
            'video' => 'video/mp4',
            'mp4' => 'video/mp4',
            'pdf' => 'application/pdf',
            'powerpoint' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'zip' => 'application/zip',
            'audio' => 'audio/mpeg',
            'mp3' => 'audio/mpeg',
            'link' => 'text/html',
        ];

        if (isset($map[$type])) {
            return $map[$type];
        }

        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));

        return $map[$extension] ?? 'application/octet-stream';
    }

    /**
     * Äá»‹nh dáº¡ng kÃ­ch thÆ°á»›c file.
     */
    protected function formatFileSize($bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . 'GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . 'MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . 'KB';
        }
        return $bytes . 'B';
    }
}
