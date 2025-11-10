<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Teacher\LoadsTeacherContext;
use App\Models\Chapter;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChapterController extends Controller
{
    use LoadsTeacherContext;

    public function index(Request $request)
    {
        $teacher = Auth::user();
        $teacherId = $teacher?->getKey() ?? 0;

        $courses = Course::with('chapters')
            ->where('maND', $teacherId)
            ->orderBy('tenKH')
            ->get();

        $selectedCourseId = (int) $request->query('course');
        if ($selectedCourseId && !$courses->contains('maKH', $selectedCourseId)) {
            $selectedCourseId = 0;
        }

        $activeCourse = $selectedCourseId
            ? $courses->firstWhere('maKH', $selectedCourseId)
            : $courses->first();

        return view('Teacher.chapters', [
            'teacher' => $teacher,
            'courses' => $courses,
            'activeCourse' => $activeCourse,
            'badges' => $this->teacherSidebarBadges($teacherId),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = Auth::user();
        $teacherId = $teacher?->getKey() ?? 0;

        $validated = $request->validate([
            'course_id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'order' => ['nullable', 'integer', 'min:1'],
        ]);

        $course = Course::where('maKH', $validated['course_id'])
            ->where('maND', $teacherId)
            ->firstOrFail();

        return DB::transaction(function () use ($validated, $course, $request) {
            $order = $validated['order'] ?? ($course->chapters()->max('thuTu') + 1);

            if ($order > 1) {
                DB::table('chuong')
                    ->where('maKH', $course->maKH)
                    ->where('thuTu', '>=', $order)
                    ->increment('thuTu');
            }

            $chapter = Chapter::create([
                'maKH' => $course->maKH,
                'tenChuong' => $validated['title'],
                'moTa' => $validated['description'] ?? null,
                'thuTu' => $order,
            ]);

            // Tạo thư mục cho chương bên trong thư mục khóa học trên R2
            $courseDir = Str::slug($course->tenKH); // Slug của tên khóa học (ví dụ: khoa-hoc-1)
            $chapterDir = "{$courseDir}/" . Str::slug($chapter->tenChuong); // Slug của tên chương
            if (!Storage::disk('r2')->exists($chapterDir)) {
                Storage::disk('r2')->makeDirectory($chapterDir);
            }

            return redirect()
                ->route('teacher.chapters.index', ['course' => $course->maKH])
                ->with('success', 'Đã tạo chương mới.')
                ->with('newChapterId', $chapter->maChuong);
        });
    }

    public function update(Request $request, Chapter $chapter): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;
        $this->authorizeChapter($chapter, $teacherId);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'order' => ['nullable', 'integer', 'min:1'],
        ]);

        return DB::transaction(function () use ($chapter, $validated) {
            $newOrder = $validated['order'] ?? $chapter->thuTu;

            // Kiểm tra và xử lý đổi tên thư mục nếu tên chương thay đổi
            $oldChapterDir = Str::slug($chapter->tenChuong); // Tên cũ (trước khi cập nhật)
            $newChapterDir = Str::slug($validated['title']); // Tên mới
            $courseDir = Str::slug($chapter->course->tenKH);

            if ($oldChapterDir !== $newChapterDir) {
                $oldDir = "{$courseDir}/{$oldChapterDir}";
                $newDir = "{$courseDir}/{$newChapterDir}";

                if (Storage::disk('r2')->exists($oldDir)) {
                    try {
                        // Tạo thư mục mới trước
                        if (!Storage::disk('r2')->exists($newDir)) {
                            Storage::disk('r2')->makeDirectory($newDir);
                        }

                        // Sao chép nội dung từ thư mục cũ sang thư mục mới
                        $files = Storage::disk('r2')->allFiles($oldDir);
                        foreach ($files as $file) {
                            $newFile = str_replace($oldDir, $newDir, $file);
                            if (!Storage::disk('r2')->exists(dirname($newFile))) {
                                Storage::disk('r2')->makeDirectory(dirname($newFile));
                            }
                            Storage::disk('r2')->copy($file, $newFile);
                        }

                        // Xóa thư mục cũ sau khi sao chép thành công
                        Storage::disk('r2')->deleteDirectory($oldDir);
                    } catch (\Exception $e) {
                        \Log::error('Lỗi đổi tên thư mục trên R2: ' . $e->getMessage());
                        // Nếu thất bại, giữ nguyên thư mục cũ và thông báo lỗi (tùy chọn)
                        return redirect()
                            ->route('teacher.chapters.index', ['course' => $chapter->maKH])
                            ->with('error', 'Không thể đổi tên thư mục trên R2. Vui lòng thử lại.');
                    }
                }
            }

            if ($newOrder !== $chapter->thuTu) {
                if ($newOrder < $chapter->thuTu) {
                    DB::table('chuong')
                        ->where('maKH', $chapter->maKH)
                        ->whereBetween('thuTu', [$newOrder, $chapter->thuTu - 1])
                        ->increment('thuTu');
                } else {
                    DB::table('chuong')
                        ->where('maKH', $chapter->maKH)
                        ->whereBetween('thuTu', [$chapter->thuTu + 1, $newOrder])
                        ->decrement('thuTu');
                }
            }

            $chapter->update([
                'tenChuong' => $validated['title'],
                'moTa' => $validated['description'] ?? null,
                'thuTu' => $newOrder,
            ]);

            return redirect()
                ->route('teacher.chapters.index', ['course' => $chapter->maKH])
                ->with('success', 'Đã cập nhật chương.');
        });
    }

    public function destroy(Chapter $chapter): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;
        $this->authorizeChapter($chapter, $teacherId);

        $courseId = $chapter->maKH;

        return DB::transaction(function () use ($chapter, $courseId) {
            // Xóa thư mục chương trên R2
            $courseDir = Str::slug($chapter->course->tenKH); // Lấy slug từ tên khóa học
            $chapterDir = "{$courseDir}/" . Str::slug($chapter->tenChuong); // Slug của tên chương
            if (Storage::disk('r2')->exists($chapterDir)) {
                Storage::disk('r2')->deleteDirectory($chapterDir);
            }

            $chapter->delete();

            return redirect()
                ->route('teacher.chapters.index', ['course' => $courseId])
                ->with('success', 'Đã xóa chương.');
        });
    }

    protected function authorizeChapter(Chapter $chapter, int $teacherId): void
    {
        $courseTeacher = $chapter->course()->value('maND');
        abort_if($courseTeacher !== $teacherId, 403, 'Bạn không có quyền trên chương này.');
    }
}