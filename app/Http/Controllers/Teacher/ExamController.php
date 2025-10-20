<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Teacher\LoadsTeacherContext;
use App\Models\Course;
use App\Models\CourseTest;
use App\Models\CourseTestMaterial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ExamController extends Controller
{
    use LoadsTeacherContext;

    public function index(Request $request)
    {
        $teacherId = Auth::id() ?? 0;

        $courses = Course::with(['finalTests.materials' => fn ($query) => $query->orderBy('created_at')])
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

        $stats = [
            'tests'     => $activeCourse ? $activeCourse->finalTests->count() : 0,
            'materials' => $activeCourse ? $activeCourse->finalTests->sum(fn ($test) => $test->materials->count()) : 0,
        ];

        return view('Teacher.exams', [
            'courses'      => $courses,
            'activeCourse' => $activeCourse,
            'stats'        => $stats,
            'badges'       => $this->teacherSidebarBadges($teacherId),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;

        $validated = $request->validate([
            'course_id'       => ['required', 'integer'],
            'title'           => ['required', 'string', 'max:150'],
            'dotTest'         => ['nullable', 'string', 'max:50'],
            'time_limit_min'  => ['nullable', 'integer', 'min:0', 'max:600'],
            'total_questions' => ['nullable', 'integer', 'min:0', 'max:500'],
        ]);

        $course = Course::where('maKH', $validated['course_id'])
            ->where('maND', $teacherId)
            ->firstOrFail();

        CourseTest::create([
            'maKH'            => $course->maKH,
            'dotTest'         => $validated['dotTest'] ?? null,
            'title'           => $validated['title'],
            'time_limit_min'  => $validated['time_limit_min'] ?? null,
            'total_questions' => $validated['total_questions'] ?? null,
        ]);

        return redirect()
            ->route('Teacher.exams', ['course' => $course->maKH])
            ->with('success', 'Đã tạo kỳ thi mới.');
    }

    public function update(Request $request, CourseTest $exam): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;
        $this->authorizeExam($exam, $teacherId);

        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:150'],
            'dotTest'         => ['nullable', 'string', 'max:50'],
            'time_limit_min'  => ['nullable', 'integer', 'min:0', 'max:600'],
            'total_questions' => ['nullable', 'integer', 'min:0', 'max:500'],
        ]);

        $exam->update($validated);

        return redirect()
            ->route('Teacher.exams', ['course' => $exam->maKH])
            ->with('success', 'Đã cập nhật kỳ thi.');
    }

    public function destroy(CourseTest $exam): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;
        $this->authorizeExam($exam, $teacherId);

        $courseId = $exam->maKH;
        $exam->delete();

        return redirect()
            ->route('Teacher.exams', ['course' => $courseId])
            ->with('success', 'Đã xóa kỳ thi.');
    }

    public function storeMaterial(Request $request, CourseTest $exam): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;
        $this->authorizeExam($exam, $teacherId);

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:150'],
            'type'       => ['required', 'string', 'max:50'],
            'url'        => ['required', 'url', 'max:700'],
            'mime_type'  => ['nullable', 'string', 'max:120'],
            'visibility' => ['nullable', Rule::in(['public', 'private'])],
        ]);

        $payload = [
            'tenTL'      => $validated['name'],
            'loai'       => $validated['type'],
            'public_url' => $validated['url'],
            'mime_type'  => $validated['mime_type'] ?? $this->guessMime($validated['type'], $validated['url']),
            'visibility' => $validated['visibility'] ?? 'public',
        ];

        $exam->materials()->create($payload);

        return redirect()
            ->route('Teacher.exams', [
                'course' => $exam->maKH,
                '_fragment' => 'exam-' . $exam->getKey(),
            ])
            ->with('success', 'Đã thêm tài liệu cho kỳ thi.');
    }

    public function destroyMaterial(CourseTestMaterial $material): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;
        $exam = $material->courseTest()->firstOrFail();

        $this->authorizeExam($exam, $teacherId);

        $courseId = $exam->maKH;
        $material->delete();

        return redirect()
            ->route('Teacher.exams', [
                'course' => $courseId,
                '_fragment' => 'exam-' . $exam->getKey(),
            ])
            ->with('success', 'Đã xóa tài liệu kỳ thi.');
    }

    protected function authorizeExam(CourseTest $exam, int $teacherId): void
    {
        $ownerId = $exam->course()->value('maND');
        abort_if($ownerId !== $teacherId, 403, 'Bạn không có quyền với kỳ thi này.');
    }

    protected function guessMime(string $type, string $url): string
    {
        $type = strtolower($type);
        $map = [
            'pdf'    => 'application/pdf',
            'video'  => 'video/mp4',
            'zip'    => 'application/zip',
            'audio'  => 'audio/mpeg',
            'link'   => 'text/html',
            'slides' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ];

        if (isset($map[$type])) {
            return $map[$type];
        }

        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));

        return $map[$extension] ?? 'application/octet-stream';
    }
}
