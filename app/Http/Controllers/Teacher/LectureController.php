<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Teacher\LoadsTeacherContext;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
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
     * Các loại tài nguyên được hỗ trợ với thông tin hiển thị.
     *
     * @var array
     */
    protected $resourcePresets = [
        'video/mp4' => ['label' => 'Video (.mp4)', 'default_type' => 'Video', 'icon' => 'bi-play-circle'],
        'application/pdf' => ['label' => 'PDF (.pdf)', 'default_type' => 'PDF', 'icon' => 'bi-file-earmark-pdf'],
        'application/vnd.ms-powerpoint' => ['label' => 'PowerPoint (.ppt)', 'default_type' => 'PowerPoint', 'icon' => 'bi-easel3'],
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => ['label' => 'PowerPoint (.pptx)', 'default_type' => 'PowerPoint', 'icon' => 'bi-easel3'],
        'application/zip' => ['label' => 'Tập tin nén (.zip)', 'default_type' => 'ZIP', 'icon' => 'bi-file-zip'],
        'audio/mpeg' => ['label' => 'Audio (.mp3)', 'default_type' => 'Audio', 'icon' => 'bi-music-note-beamed'],
        'text/html' => ['label' => 'Liên kết', 'default_type' => 'Link', 'icon' => 'bi-link-45deg'],
    ];

    /**
     * Các loại bài giảng được hỗ trợ.
     *
     * @var array
     */
    protected $lessonTypes = [
        'video' => ['label' => 'Video bài giảng', 'icon' => 'bi-camera-video'],
        'doc' => ['label' => 'Tài liệu', 'icon' => 'bi-file-earmark-text'],
        'assignment' => ['label' => 'Bài tập', 'icon' => 'bi-pencil-square'],
    ];

    /**
     * Hiển thị danh sách bài giảng của giảng viên.
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
            $studentCounts = DB::table('HOCVIEN_KHOAHOC')
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

        return view('Teacher.lectures', [
            'teacher' => $teacher,
            'courses' => $courses,
            'activeCourse' => $activeCourse,
            'lessonTypes' => $this->lessonTypes,
            'resourcePresets' => $this->resourcePresets,
            'badges' => $this->teacherSidebarBadges($teacherId),
        ]);
    }

    /**
     * Lưu bài giảng mới.
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
                DB::table('BAIHOC')
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

            // Tạo thư mục bài giảng trên R2
            $courseDir = Str::slug($chapter->course->tenKH);
            $chapterDir = "{$courseDir}/" . Str::slug($chapter->tenChuong);
            $lessonDir = "{$chapterDir}/" . Str::slug($lesson->tieuDe);
            try {
                if (!Storage::disk('s3')->exists($lessonDir)) {
                    Storage::disk('s3')->makeDirectory($lessonDir);
                }
            } catch (\Exception $e) {
                Log::error('Lỗi tạo thư mục bài giảng trên R2: ' . $e->getMessage());
                return redirect()
                    ->route('teacher.lectures.index', ['course' => $chapter->maKH])
                    ->with('error', 'Không thể tạo thư mục bài giảng trên R2. Vui lòng thử lại.');
            }

            return redirect()
                ->route('teacher.lectures.index', [
                    'course' => $chapter->maKH,
                    '_fragment' => 'lesson-' . $lesson->maBH,
                ])
                ->with('success', 'Đã tạo bài giảng mới.');
        });
    }

    /**
     * Cập nhật bài giảng.
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

            // Đổi tên thư mục trên R2 nếu tiêu đề thay đổi hoặc chuyển chương
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
                    Log::error('Lỗi đổi tên thư mục bài giảng trên R2: ' . $e->getMessage());
                    return redirect()
                        ->route('teacher.lectures.index', ['course' => $lesson->chapter->maKH])
                        ->with('error', 'Không thể đổi tên thư mục bài giảng trên R2. Vui lòng thử lại.');
                }
            }

            // Cập nhật thứ tự bài giảng
            if ($newOrder !== $lesson->thuTu || $oldChapterId !== $newChapterId) {
                if ($oldChapterId === $newChapterId) {
                    if ($newOrder < $lesson->thuTu) {
                        DB::table('BAIHOC')
                            ->where('maChuong', $lesson->maChuong)
                            ->whereBetween('thuTu', [$newOrder, $lesson->thuTu - 1])
                            ->increment('thuTu');
                    } else {
                        DB::table('BAIHOC')
                            ->where('maChuong', $lesson->maChuong)
                            ->whereBetween('thuTu', [$lesson->thuTu + 1, $newOrder])
                            ->decrement('thuTu');
                    }
                } else {
                    DB::table('BAIHOC')
                        ->where('maChuong', $oldChapterId)
                        ->where('thuTu', '>', $lesson->thuTu)
                        ->decrement('thuTu');
                    DB::table('BAIHOC')
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
                ->with('success', 'Đã cập nhật bài giảng.');
        });
    }

    /**
     * Lưu tài nguyên cho bài giảng.
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

                // Tạo đường dẫn thư mục dựa trên course, chapter, và lesson
                $course = $lesson->chapter->course;
                $courseDir = Str::slug($course->tenKH);
                $chapterDir = Str::slug($lesson->chapter->tenChuong);
                $lessonDir = Str::slug($lesson->tieuDe);
                $fullPath = "{$courseDir}/{$chapterDir}/{$lessonDir}/{$fileName}";

                // Upload file vào thư mục cụ thể
                $filePath = $file->storeAs('', $fullPath, 's3');
                Storage::disk('s3')->setVisibility($filePath, 'public'); // Đặt quyền public

                // Tạo URL công khai sử dụng PUBLIC_R2_URL
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

            return response()->json(['success' => true, 'message' => 'Đã thêm tài nguyên cho bài giảng.']);
        } catch (\Exception $e) {
            Log::error('Lỗi upload tài nguyên: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Không thể upload file lên R2. Vui lòng thử lại. ' . $e->getMessage()], 500);
        }
    }
    /**
     * Xóa tài nguyên.
     */
    public function destroyMaterial(Material $material): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;
        $lesson = $material->lesson()->with('chapter')->firstOrFail();

        $this->authorizeLesson($lesson, $teacherId);

        $courseId = $lesson->chapter->maKH;

        // Xóa file trên R2 nếu tồn tại
        try {
            $filePath = parse_url($material->public_url, PHP_URL_PATH);
            if ($filePath && Storage::disk('s3')->exists($filePath)) {
                Storage::disk('s3')->delete($filePath);
            }
        } catch (\Exception $e) {
            Log::error('Lỗi xóa file trên R2: ' . $e->getMessage());
        }

        $material->delete();

        return redirect()
            ->route('teacher.lectures.index', [
                'course' => $courseId,
                '_fragment' => 'lesson-' . $lesson->maBH,
            ])
            ->with('success', 'Đã xóa tài nguyên.');
    }

    /**
     * Kiểm tra quyền truy cập bài giảng.
     */
    protected function authorizeLesson(Lesson $lesson, int $teacherId): void
    {
        $courseTeacher = $lesson->chapter
            ->course()
            ->value('maND');

        abort_if($courseTeacher !== $teacherId, 403, 'Bạn không có quyền trên bài giảng này.');
    }

    /**
     * Tạo tài nguyên từ payload.
     */
    protected function createMaterialFromPayload(Lesson $lesson, array $resource): void
    {
        try {
            $name = $resource['name'] ?? $lesson->tieuDe;
            $type = $resource['type'] ?? 'Tài nguyên';
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
            Log::error('Lỗi tạo material: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Đoán MIME type dựa trên loại hoặc URL.
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
     * Định dạng kích thước file.
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