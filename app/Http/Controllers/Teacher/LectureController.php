<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Teacher\LoadsTeacherContext;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Material;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LectureController extends Controller
{
    use LoadsTeacherContext;

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

        $lessonTypes = [
            'video'      => ['label' => 'Video bài giảng', 'icon' => 'bi-camera-video'],
            'doc'        => ['label' => 'Tài liệu', 'icon' => 'bi-file-earmark-text'],
            'assignment' => ['label' => 'Bài tập', 'icon' => 'bi-pencil-square'],
        ];

        $resourcePresets = [
            'video/mp4'         => ['label' => 'Video (.mp4)', 'default_type' => 'Video', 'icon' => 'bi-play-circle'],
            'application/pdf'   => ['label' => 'PDF (.pdf)', 'default_type' => 'PDF', 'icon' => 'bi-file-earmark-pdf'],
            'application/vnd.ms-powerpoint' => ['label' => 'PowerPoint (.ppt)', 'default_type' => 'PowerPoint', 'icon' => 'bi-easel3'],
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => ['label' => 'PowerPoint (.pptx)', 'default_type' => 'PowerPoint', 'icon' => 'bi-easel3'],
            'application/zip'   => ['label' => 'Tập tin nén (.zip)', 'default_type' => 'ZIP', 'icon' => 'bi-file-zip'],
            'audio/mpeg'        => ['label' => 'Audio (.mp3)', 'default_type' => 'Audio', 'icon' => 'bi-music-note-beamed'],
            'text/html'         => ['label' => 'Liên kết', 'default_type' => 'Link', 'icon' => 'bi-link-45deg'],
        ];

        return view('Teacher.lectures', [
            'teacher'          => $teacher,
            'courses'          => $courses,
            'activeCourse'     => $activeCourse,
            'lessonTypes'      => $lessonTypes,
            'resourcePresets'  => $resourcePresets,
            'badges'           => $this->teacherSidebarBadges($teacherId),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = Auth::user();
        $teacherId = $teacher?->getKey() ?? 0;

        $validated = $request->validate([
            'course_id'          => ['required', 'integer'],
            'chapter_id'         => ['required', 'integer'],
            'title'              => ['required', 'string', 'max:150'],
            'description'        => ['nullable', 'string', 'max:1000'],
            'type'               => ['required', Rule::in(['video', 'doc', 'assignment'])],
            'order'              => ['nullable', 'integer', 'min:1'],
            'resource.name'      => ['nullable', 'string', 'max:150'],
            'resource.url'       => ['nullable', 'url', 'max:700'],
            'resource.type'      => ['nullable', 'string', 'max:50'],
            'resource.mime'      => ['nullable', 'string', 'max:120'],
            'resource.size'      => ['nullable', 'string', 'max:50'],
            'resource.summary'   => ['nullable', 'string', 'max:400'],
            'resource.visibility'=> ['nullable', Rule::in(['public', 'private'])],
        ]);

        $course = Course::where('maKH', $validated['course_id'])
            ->where('maND', $teacherId)
            ->firstOrFail();

        $chapter = $course->chapters()->where('maChuong', $validated['chapter_id'])->firstOrFail();

        return DB::transaction(function () use ($validated, $chapter, $teacherId) {
            $order = $validated['order'] ?? null;

            if (!$order) {
                $order = (int) $chapter->lessons()->max('thuTu') + 1;
            } else {
                DB::table('BAIHOC')
                    ->where('maChuong', $chapter->getKey())
                    ->where('thuTu', '>=', $order)
                    ->increment('thuTu');
            }

            /** @var Lesson $lesson */
            $lesson = Lesson::create([
                'maChuong' => $chapter->getKey(),
                'tieuDe'   => $validated['title'],
                'moTa'     => $validated['description'] ?? null,
                'thuTu'    => $order,
                'loai'     => $validated['type'],
            ]);

            $resource = Arr::get($validated, 'resource', []);

            if ($resource && Arr::get($resource, 'url')) {
                $this->createMaterialFromPayload($lesson, $resource);
            }

            return redirect()
                ->route('Teacher.lectures', ['course' => $chapter->maKH])
                ->with('success', 'Đã tạo bài giảng mới.');
        });
    }

    public function update(Request $request, Lesson $lesson): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;

        $this->authorizeLesson($lesson, $teacherId);

        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'order'       => ['nullable', 'integer', 'min:1'],
            'type'        => ['required', Rule::in(['video', 'doc', 'assignment'])],
        ]);

        return DB::transaction(function () use ($lesson, $validated) {
            $chapterId = $lesson->maChuong;
            $newOrder = $validated['order'] ?? $lesson->thuTu;

            if ($newOrder !== $lesson->thuTu) {
                if ($newOrder < $lesson->thuTu) {
                    DB::table('BAIHOC')
                        ->where('maChuong', $chapterId)
                        ->whereBetween('thuTu', [$newOrder, $lesson->thuTu - 1])
                        ->increment('thuTu');
                } else {
                    DB::table('BAIHOC')
                        ->where('maChuong', $chapterId)
                        ->whereBetween('thuTu', [$lesson->thuTu + 1, $newOrder])
                        ->decrement('thuTu');
                }
            }

            $lesson->update([
                'tieuDe' => $validated['title'],
                'moTa'   => $validated['description'] ?? null,
                'thuTu'  => $newOrder,
                'loai'   => $validated['type'],
            ]);

            return redirect()
                ->route('Teacher.lectures', ['course' => $lesson->chapter->maKH])
                ->with('success', 'Đã cập nhật bài giảng.');
        });
    }

    public function destroy(Lesson $lesson): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;

        $this->authorizeLesson($lesson, $teacherId);

        $courseId = $lesson->chapter->maKH;

        return DB::transaction(function () use ($lesson, $courseId) {
            $lesson->delete();

            return redirect()
                ->route('Teacher.lectures', ['course' => $courseId])
                ->with('success', 'Đã xoá bài giảng.');
        });
    }

    public function storeMaterial(Request $request, Lesson $lesson): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;

        $this->authorizeLesson($lesson, $teacherId);

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:150'],
            'type'       => ['required', 'string', 'max:50'],
            'url'        => ['required', 'url', 'max:700'],
            'mime_type'  => ['nullable', 'string', 'max:120'],
            'size'       => ['nullable', 'string', 'max:50'],
            'summary'    => ['nullable', 'string', 'max:400'],
            'visibility' => ['nullable', Rule::in(['public', 'private'])],
        ]);

        $payload = [
            'tenTL'      => $validated['name'],
            'loai'       => $validated['type'],
            'public_url' => $validated['url'],
            'mime_type'  => $validated['mime_type'] ?? $this->guessMime($validated['type'], $validated['url']),
            'kichThuoc'  => $validated['size'] ?? null,
            'moTa'       => $validated['summary'] ?? null,
            'visibility' => $validated['visibility'] ?? 'public',
        ];

        $lesson->materials()->create($payload);

        return redirect()
            ->route('Teacher.lectures', [
                'course'     => $lesson->chapter->maKH,
                '_fragment'  => 'lesson-' . $lesson->getKey(),
            ])
            ->with('success', 'Đã thêm tài nguyên cho bài giảng.');
    }

    public function destroyMaterial(Material $material): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;
        $lesson = $material->lesson()->with('chapter')->firstOrFail();

        $this->authorizeLesson($lesson, $teacherId);

        $courseId = $lesson->chapter->maKH;

        $material->delete();

        return redirect()
            ->route('Teacher.lectures', [
                'course'    => $courseId,
                '_fragment' => 'lesson-' . $lesson->getKey(),
            ])
            ->with('success', 'Đã xoá tài nguyên.');
    }

    protected function authorizeLesson(Lesson $lesson, int $teacherId): void
    {
        $courseTeacher = $lesson->chapter
            ->course()
            ->value('maND');

        abort_if($courseTeacher !== $teacherId, 403, 'Bạn không có quyền trên bài giảng này.');
    }

    protected function createMaterialFromPayload(Lesson $lesson, array $resource): void
    {
        $name = $resource['name'] ?? $lesson->tieuDe;
        $type = $resource['type'] ?? 'Tài nguyên';
        $mime = $resource['mime'] ?? $this->guessMime($type, $resource['url'] ?? '');

        $lesson->materials()->create([
            'tenTL'      => $name,
            'loai'       => $type,
            'public_url' => $resource['url'],
            'mime_type'  => $mime,
            'kichThuoc'  => $resource['size'] ?? null,
            'moTa'       => $resource['summary'] ?? null,
            'visibility' => $resource['visibility'] ?? 'public',
        ]);
    }

    protected function guessMime(string $type, string $url): string
    {
        $type = strtolower($type);
        $map = [
            'video'     => 'video/mp4',
            'mp4'       => 'video/mp4',
            'pdf'       => 'application/pdf',
            'powerpoint'=> 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'ppt'       => 'application/vnd.ms-powerpoint',
            'pptx'      => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'zip'       => 'application/zip',
            'audio'     => 'audio/mpeg',
            'mp3'       => 'audio/mpeg',
            'link'      => 'text/html',
        ];

        if (isset($map[$type])) {
            return $map[$type];
        }

        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));

        return $map[$extension] ?? 'application/octet-stream';
    }
}
