<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Teacher\LoadsTeacherContext;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\MiniTest;
use App\Models\MiniTestMaterial;
use App\Models\MiniTestQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Log;

class MiniTestController extends Controller
{
    use LoadsTeacherContext;

    /**
     * Hiển thị danh sách mini-test của giảng viên.
     */
    public function index(Request $request)
    {
        $type = 'index';
        $teacher = Auth::user();
        $teacherId = $teacher?->getKey() ?? 0;

        // Lấy tất cả khóa học của giảng viên với các chương và mini-test
        $courses = Course::with(['chapters.miniTests.materials' => fn ($query) => $query->orderBy('created_at'),
                                 'chapters.miniTests.questions'])
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

        return view('Teacher.minitests', [
            'type' => $type,
            'teacher' => $teacher,
            'courses' => $courses,
            'activeCourse' => $activeCourse,
            'badges' => $this->teacherSidebarBadges($teacherId),
        ]);
    }

    /**
     * Hiển thị form quản lý câu hỏi cho mini-test.
     */
    public function questions(MiniTest $miniTest)
    {
        $type = 'questions';
        $teacherId = Auth::id() ?? 0;
        $this->authorizeMiniTest($miniTest, $teacherId);

        $miniTest->load(['course', 'chapter', 'questions']);

        return view('Teacher.minitests', [
            'type' => $type,
            'teacher' => Auth::user(),
            'miniTest' => $miniTest,
            'badges' => $this->teacherSidebarBadges($teacherId),
        ]);
    }

    /**
     * Lưu mini-test mới.
     */
    public function store(Request $request): RedirectResponse
    {
        $teacher = Auth::user();
        $teacherId = $teacher?->getKey() ?? 0;

        $validated = $request->validate([
            'course_id' => ['required', 'integer'],
            'chapter_id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'skill_type' => ['required', 'string', Rule::in(['LISTENING', 'SPEAKING', 'READING', 'WRITING'])],
            'order' => ['nullable', 'integer', 'min:1'],
            'max_score' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'time_limit' => ['nullable', 'integer', 'min:1'],
            'attempts' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ]);

        // Kiểm tra quyền trên khóa học
        $course = Course::findOrFail($validated['course_id']);
        abort_if($course->maND !== $teacherId, 403, 'Bạn không có quyền trên khóa học này.');

        // Kiểm tra chương thuộc khóa học
        $chapter = Chapter::findOrFail($validated['chapter_id']);
        abort_if($chapter->maKH !== $validated['course_id'], 400, 'Chương không thuộc khóa học.');

        $miniTest = MiniTest::create([
            'maKH' => $validated['course_id'],
            'maChuong' => $validated['chapter_id'],
            'title' => $validated['title'],
            'skill_type' => $validated['skill_type'],
            'thuTu' => $validated['order'] ?? MiniTest::where('maChuong', $validated['chapter_id'])->max('thuTu') + 1,
            'max_score' => $validated['max_score'] ?? 0,
            'weight' => $validated['weight'] ?? 100,
            'time_limit_min' => $validated['time_limit'] ?? 30,
            'attempts_allowed' => $validated['attempts'] ?? 3,
            'is_active' => $validated['is_active'] ?? false,
            'created_by' => $teacherId,
        ]);

        return redirect()
            ->route('teacher.minitests.index', [
                'course' => $validated['course_id'],
                '_fragment' => 'minitest-' . $miniTest->maMT,
            ])
            ->with('success', 'Đã tạo mini-test mới thành công.');
    }

    /**
     * Cập nhật mini-test.
     */
    public function update(Request $request, MiniTest $miniTest): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;
        $this->authorizeMiniTest($miniTest, $teacherId);

        $validated = $request->validate([
            'course_id' => ['required', 'integer'],
            'chapter_id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'skill_type' => ['required', 'string', Rule::in(['LISTENING', 'SPEAKING', 'READING', 'WRITING'])],
            'order' => ['nullable', 'integer', 'min:1'],
            'max_score' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'time_limit' => ['nullable', 'integer', 'min:1'],
            'attempts' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ]);

        $miniTest->update([
            'maKH' => $validated['course_id'],
            'maChuong' => $validated['chapter_id'],
            'title' => $validated['title'],
            'skill_type' => $validated['skill_type'],
            'thuTu' => $validated['order'] ?? $miniTest->thuTu,
            'max_score' => $validated['max_score'] ?? $miniTest->max_score,
            'weight' => $validated['weight'] ?? $miniTest->weight,
            'time_limit_min' => $validated['time_limit'] ?? $miniTest->time_limit_min,
            'attempts_allowed' => $validated['attempts'] ?? $miniTest->attempts_allowed,
            'is_active' => $validated['is_active'] ?? $miniTest->is_active,
            'updated_by' => $teacherId,
        ]);

        return redirect()
            ->route('teacher.minitests.index', [
                'course' => $validated['course_id'],
                '_fragment' => 'minitest-' . $miniTest->maMT,
            ])
            ->with('success', 'Đã cập nhật mini-test thành công.');
    }

    /**
     * Xóa mini-test.
     */
    public function destroy(MiniTest $miniTest): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;
        $this->authorizeMiniTest($miniTest, $teacherId);

        $courseId = $miniTest->maKH;

        try {
            DB::beginTransaction();

            // Xóa câu hỏi
            $miniTest->questions()->delete();

            // Xóa tài liệu và file trên R2
            foreach ($miniTest->materials as $material) {
                $filePath = parse_url($material->public_url, PHP_URL_PATH);
                if ($filePath && Storage::disk('s3')->exists($filePath)) {
                    Storage::disk('s3')->delete($filePath);
                }
                $material->delete();
            }

            // Xóa mini-test
            $miniTest->delete();

            DB::commit();

            return redirect()
                ->route('teacher.minitests.index', ['course' => $courseId])
                ->with('success', 'Đã xóa mini-test thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi xóa mini-test: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi xóa. Vui lòng thử lại.');
        }
    }

    /**
     * Lưu câu hỏi cho mini-test.
     */
    public function storeQuestions(Request $request, MiniTest $miniTest): JsonResponse
    {
        $teacherId = Auth::id() ?? 0;
        $this->authorizeMiniTest($miniTest, $teacherId);

        $validated = $request->validate([
            'questions' => ['required', 'array'],
            'questions.*.content' => ['required', 'string'],
            'questions.*.type' => ['required', Rule::in(['multiple_choice', 'essay'])],
            'questions.*.points' => ['required', 'numeric', 'min:0'],
            'questions.*.options' => ['required_if:questions.*.type,multiple_choice', 'array'],
            'questions.*.options.*' => ['required_if:questions.*.type,multiple_choice', 'string'],
            'questions.*.correct' => ['required_if:questions.*.type,multiple_choice', 'string', Rule::in(['A', 'B', 'C', 'D'])],
            'questions.*.audio' => ['nullable', 'file', 'mimes:mp3,wav', 'max:51200'],
            'questions.*.pdf' => ['nullable', 'file', 'mimes:pdf', 'max:51200'],
            'questions.*.image' => ['nullable', 'file', 'mimes:jpg,png,jpeg', 'max:10240'],
        ]);

        try {
            DB::beginTransaction();

            // Xóa câu hỏi cũ
            $miniTest->questions()->delete();

            $totalScore = 0;

            foreach ($validated['questions'] as $index => $qData) {
                $question = MiniTestQuestion::create([
                    'maMT' => $miniTest->maMT,
                    'noiDungCauHoi' => $qData['content'],
                    'loai' => $qData['type'],
                    'diem' => $qData['points'],
                    'thuTu' => $index + 1,
                ]);

                $totalScore += (float) $qData['points'];

                if ($qData['type'] === 'multiple_choice') {
                    $question->update([
                        'phuongAnA' => $qData['options']['A'] ?? '',
                        'phuongAnB' => $qData['options']['B'] ?? '',
                        'phuongAnC' => $qData['options']['C'] ?? '',
                        'phuongAnD' => $qData['options']['D'] ?? '',
                        'dapAnDung' => $qData['correct'],
                    ]);
                }

                // Xử lý file upload
                foreach (['audio', 'pdf', 'image'] as $fileType) {
                    if ($request->hasFile("questions.{$index}.{$fileType}")) {
                        $file = $request->file("questions.{$index}.{$fileType}");
                        $path = $file->store('minitest_questions/' . $miniTest->maMT, 's3');
                        $url = Storage::disk('s3')->url($path);

                        $question->update(["{$fileType}_url" => $url]);
                    }
                }
            }

            // Cập nhật max_score của mini-test
            $miniTest->update(['max_score' => $totalScore]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã lưu câu hỏi thành công.',
                'redirect' => route('teacher.minitests.index', ['course' => $miniTest->maKH]),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi lưu câu hỏi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra khi lưu câu hỏi. Vui lòng thử lại.',
            ], 500);
        }
    }

    /**
     * Thêm tài liệu cho mini-test.
     */
    public function storeMaterial(Request $request, MiniTest $miniTest): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;
        $this->authorizeMiniTest($miniTest, $teacherId);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string'],
            'source_type' => ['required', 'in:file,url'],
            'file' => ['required_if:source_type,file', 'file', 'max:102400'],
            'url' => ['required_if:source_type,url', 'url'],
            'visibility' => ['required', 'in:public,private'],
        ]);

        try {
            DB::beginTransaction();

            $publicUrl = null;
            $privateUrl = null;

            if ($validated['source_type'] === 'file') {
                $file = $request->file('file');
                $mime = $file->getMimeType();
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::slug($validated['name']) . '.' . $extension;
                $path = $file->storeAs('minitest_materials/' . $miniTest->maMT, $fileName, 's3');
                $publicUrl = Storage::disk('s3')->url($path);
            } else {
                $publicUrl = $validated['url'];
                $mime = $this->guessMime($validated['type'], $publicUrl);
            }

            MiniTestMaterial::create([
                'maMT' => $miniTest->maMT,
                'name' => $validated['name'],
                'mime_type' => $mime,
                'public_url' => $publicUrl,
                'private_url' => $privateUrl,
                'visibility' => $validated['visibility'],
                'uploaded_by' => $teacherId,
            ]);

            DB::commit();

            return redirect()
                ->route('teacher.minitests.index', [
                    'course' => $miniTest->maKH,
                    '_fragment' => 'minitest-' . $miniTest->maMT,
                ])
                ->with('success', 'Đã thêm tài liệu thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi thêm tài liệu: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi thêm tài liệu. Vui lòng thử lại.');
        }
    }

    /**
     * Công bố mini-test.
     */
    public function publish(MiniTest $miniTest): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;
        $this->authorizeMiniTest($miniTest, $teacherId);

        if ($miniTest->questions->isEmpty()) {
            return redirect()
                ->route('teacher.minitests.index', ['course' => $miniTest->maKH])
                ->with('error', 'Không thể công bố mini-test chưa có câu hỏi.');
        }

        $miniTest->update(['is_published' => true]);

        return redirect()
            ->route('teacher.minitests.index', [
                'course' => $miniTest->maKH,
                '_fragment' => 'minitest-' . $miniTest->maMT,
            ])
            ->with('success', 'Đã công bố mini-test thành công.');
    }

    /**
     * Hủy công bố mini-test.
     */
    public function unpublish(MiniTest $miniTest): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;
        $this->authorizeMiniTest($miniTest, $teacherId);

        $miniTest->update(['is_published' => false]);

        return redirect()
            ->route('teacher.minitests.index', [
                'course' => $miniTest->maKH,
                '_fragment' => 'minitest-' . $miniTest->maMT,
            ])
            ->with('success', 'Đã hủy công bố mini-test.');
    }

    /**
     * Xóa tài nguyên.
     */
    public function destroyMaterial(MiniTestMaterial $material): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;
        $miniTest = $material->miniTest()->with('chapter')->firstOrFail();

        $this->authorizeMiniTest($miniTest, $teacherId);

        $courseId = $miniTest->maKH;

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
            ->route('teacher.minitests.index', [
                'course' => $courseId,
                '_fragment' => 'minitest-' . $miniTest->maMT,
            ])
            ->with('success', 'Đã xóa tài nguyên.');
    }

    /**
     * Kiểm tra quyền truy cập mini-test.
     */
    protected function authorizeMiniTest(MiniTest $miniTest, int $teacherId): void
    {
        $courseTeacher = $miniTest->course()->value('maND');

        abort_if($courseTeacher !== $teacherId, 403, 'Bạn không có quyền trên mini-test này.');
    }

    /**
     * Đoán MIME type dựa trên loại hoặc URL.
     */
    protected function guessMime(string $type, string $url): string
    {
        $type = strtolower($type);
        $map = [
            'pdf' => 'application/pdf',
            'audio' => 'audio/mpeg',
            'mp3' => 'audio/mpeg',
            'video' => 'video/mp4',
            'mp4' => 'video/mp4',
            'image' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];

        if (isset($map[$type])) {
            return $map[$type];
        }

        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));

        return $map[$extension] ?? 'application/octet-stream';
    }
}
