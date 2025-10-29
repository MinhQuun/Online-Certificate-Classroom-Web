<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Teacher\LoadsTeacherContext;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\MiniTest;
use App\Models\MiniTestMaterial;
use App\Models\MiniTestQuestion;
use App\Models\MiniTestAnswer;
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

        return view('Teacher.minitests-new', [
            'teacher' => $teacher,
            'courses' => $courses,
            'activeCourse' => $activeCourse,
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

        $chapter = Chapter::where('maChuong', $validated['chapter_id'])
            ->whereHas('course', fn ($query) => $query->where('maND', $teacherId))
            ->firstOrFail();

        return DB::transaction(function () use ($validated, $chapter) {
            $order = $validated['order'] ?? ($chapter->miniTests()->max('thuTu') + 1);

            if ($order > 1) {
                DB::table('CHUONG_MINITEST')
                    ->where('maChuong', $chapter->maChuong)
                    ->where('thuTu', '>=', $order)
                    ->increment('thuTu');
            }

            $miniTest = MiniTest::create([
                'maKH' => $validated['course_id'],
                'maChuong' => $chapter->maChuong,
                'title' => $validated['title'],
                'skill_type' => $validated['skill_type'],
                'thuTu' => $order,
                'max_score' => $validated['max_score'] ?? 10.00,
                'trongSo' => $validated['weight'] ?? 0.00,
                'time_limit_min' => $validated['time_limit'] ?? 10,
                'attempts_allowed' => $validated['attempts'] ?? 1,
                'is_active' => $validated['is_active'] ?? true,
                'is_published' => false, // Mặc định chưa công bố
            ]);

            // Tạo thư mục mini-test trên R2
            $courseDir = Str::slug($chapter->course->tenKH);
            $chapterDir = "{$courseDir}/" . Str::slug($chapter->tenChuong);
            $miniTestDir = "{$chapterDir}/MiniTest/" . Str::slug($miniTest->title);
            
            try {
                if (!Storage::disk('s3')->exists($miniTestDir)) {
                    Storage::disk('s3')->makeDirectory($miniTestDir);
                }
            } catch (\Exception $e) {
                Log::error('Lỗi tạo thư mục mini-test trên R2: ' . $e->getMessage());
                return redirect()
                    ->route('teacher.minitests.index', ['course' => $chapter->maKH])
                    ->with('error', 'Không thể tạo thư mục mini-test trên R2. Vui lòng thử lại.');
            }

            return redirect()
                ->route('teacher.minitests.index', [
                    'course' => $chapter->maKH,
                    '_fragment' => 'minitest-' . $miniTest->maMT,
                ])
                ->with('success', 'Đã tạo mini-test mới.');
        });
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

        return DB::transaction(function () use ($validated, $miniTest) {
            $newOrder = $validated['order'] ?? $miniTest->thuTu;
            $oldChapterId = $miniTest->maChuong;
            $newChapterId = $validated['chapter_id'];

            // Đổi tên thư mục trên R2 nếu tiêu đề thay đổi hoặc chuyển chương
            $course = Course::findOrFail($validated['course_id']);
            $oldChapter = Chapter::findOrFail($oldChapterId);
            $newChapter = Chapter::findOrFail($newChapterId);
            $courseDir = Str::slug($course->tenKH);
            $oldChapterDir = "{$courseDir}/" . Str::slug($oldChapter->tenChuong);
            $newChapterDir = "{$courseDir}/" . Str::slug($newChapter->tenChuong);
            $oldMiniTestDir = "{$oldChapterDir}/MiniTest/" . Str::slug($miniTest->title);
            $newMiniTestDir = "{$newChapterDir}/MiniTest/" . Str::slug($validated['title']);

            if ($miniTest->title !== $validated['title'] || $oldChapterId !== $newChapterId) {
                try {
                    if (Storage::disk('s3')->exists($oldMiniTestDir)) {
                        $files = Storage::disk('s3')->allFiles($oldMiniTestDir);
                        foreach ($files as $file) {
                            $newFile = str_replace($oldMiniTestDir, $newMiniTestDir, $file);
                            if (!Storage::disk('s3')->exists(dirname($newFile))) {
                                Storage::disk('s3')->makeDirectory(dirname($newFile));
                            }
                            Storage::disk('s3')->copy($file, $newFile);
                        }
                        Storage::disk('s3')->deleteDirectory($oldMiniTestDir);
                    } else {
                        Storage::disk('s3')->makeDirectory($newMiniTestDir);
                    }
                } catch (\Exception $e) {
                    Log::error('Lỗi đổi tên thư mục mini-test trên R2: ' . $e->getMessage());
                    return redirect()
                        ->route('teacher.minitests.index', ['course' => $miniTest->maKH])
                        ->with('error', 'Không thể đổi tên thư mục mini-test trên R2. Vui lòng thử lại.');
                }
            }

            // Cập nhật thứ tự mini-test
            if ($newOrder !== $miniTest->thuTu || $oldChapterId !== $newChapterId) {
                if ($oldChapterId === $newChapterId) {
                    if ($newOrder < $miniTest->thuTu) {
                        DB::table('CHUONG_MINITEST')
                            ->where('maChuong', $miniTest->maChuong)
                            ->whereBetween('thuTu', [$newOrder, $miniTest->thuTu - 1])
                            ->increment('thuTu');
                    } else {
                        DB::table('CHUONG_MINITEST')
                            ->where('maChuong', $miniTest->maChuong)
                            ->whereBetween('thuTu', [$miniTest->thuTu + 1, $newOrder])
                            ->decrement('thuTu');
                    }
                } else {
                    DB::table('CHUONG_MINITEST')
                        ->where('maChuong', $oldChapterId)
                        ->where('thuTu', '>', $miniTest->thuTu)
                        ->decrement('thuTu');
                    DB::table('CHUONG_MINITEST')
                        ->where('maChuong', $newChapterId)
                        ->where('thuTu', '>=', $newOrder)
                        ->increment('thuTu');
                }
            }

            $miniTest->update([
                'maKH' => $validated['course_id'],
                'maChuong' => $newChapterId,
                'title' => $validated['title'],
                'skill_type' => $validated['skill_type'],
                'thuTu' => $newOrder,
                'max_score' => $validated['max_score'] ?? 10.00,
                'trongSo' => $validated['weight'] ?? 0.00,
                'time_limit_min' => $validated['time_limit'] ?? 10,
                'attempts_allowed' => $validated['attempts'] ?? 1,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            return redirect()
                ->route('teacher.minitests.index', [
                    'course' => $miniTest->maKH,
                    '_fragment' => 'minitest-' . $miniTest->maMT,
                ])
                ->with('success', 'Đã cập nhật mini-test.');
        });
    }

    /**
     * Xóa mini-test.
     */
    public function destroy(MiniTest $miniTest): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;
        $this->authorizeMiniTest($miniTest, $teacherId);

        $courseId = $miniTest->maKH;

        return DB::transaction(function () use ($miniTest, $courseId) {
            // Xóa thư mục mini-test trên R2
            $course = $miniTest->course;
            $chapter = $miniTest->chapter;
            $courseDir = Str::slug($course->tenKH);
            $chapterDir = "{$courseDir}/" . Str::slug($chapter->tenChuong);
            $miniTestDir = "{$chapterDir}/MiniTest/" . Str::slug($miniTest->title);

            try {
                if (Storage::disk('s3')->exists($miniTestDir)) {
                    Storage::disk('s3')->deleteDirectory($miniTestDir);
                }
            } catch (\Exception $e) {
                Log::error('Lỗi xóa thư mục mini-test trên R2: ' . $e->getMessage());
            }

            // Cập nhật thứ tự các mini-test khác
            DB::table('CHUONG_MINITEST')
                ->where('maChuong', $miniTest->maChuong)
                ->where('thuTu', '>', $miniTest->thuTu)
                ->decrement('thuTu');

            $miniTest->delete();

            return redirect()
                ->route('teacher.minitests.index', ['course' => $courseId])
                ->with('success', 'Đã xóa mini-test.');
        });
    }

    /**
     * Lưu tài nguyên cho mini-test (câu hỏi, audio, PDF, v.v.).
     */
    public function storeMaterial(Request $request, MiniTest $miniTest): JsonResponse
    {
        Log::info('Received data for storeMaterial: ', $request->all());
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:50'],
            'url' => ['nullable', 'url', 'max:2048'],
            'file' => ['nullable', 'file', 'max:102400'], // 100MB
            'visibility' => ['required', Rule::in(['public', 'private'])],
        ]);

        try {
            $fileUrl = null;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();

                // Tạo đường dẫn thư mục dựa trên course, chapter, và minitest
                $course = $miniTest->course;
                $chapter = $miniTest->chapter;
                $courseDir = Str::slug($course->tenKH);
                $chapterDir = Str::slug($chapter->tenChuong);
                $miniTestDir = Str::slug($miniTest->title);
                $fullPath = "{$courseDir}/{$chapterDir}/MiniTest/{$miniTestDir}/{$fileName}";

                // Upload file vào thư mục cụ thể
                $filePath = $file->storeAs('', $fullPath, 's3');
                Storage::disk('s3')->setVisibility($filePath, 'public');

                // Tạo URL công khai
                $publicUrl = env('PUBLIC_R2_URL') . '/' . $fullPath;
                $fileUrl = $publicUrl;
                Log::info("Uploaded file to R2 with public access: {$fileUrl}");
            } elseif ($request->filled('url')) {
                $fileUrl = $request->input('url');
            }

            if ($fileUrl) {
                $miniTest->materials()->create([
                    'tenTL' => $validated['name'],
                    'loai' => $validated['type'],
                    'public_url' => $fileUrl,
                    'mime_type' => $request->hasFile('file') ? $file->getMimeType() : $this->guessMime($validated['type'], $fileUrl),
                    'visibility' => $validated['visibility'],
                ]);
                Log::info("Material created successfully for mini-test ID: {$miniTest->maMT}");
            }

            return response()->json(['success' => true, 'message' => 'Đã thêm tài nguyên cho mini-test.']);
        } catch (\Exception $e) {
            Log::error('Lỗi upload tài nguyên: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Không thể upload file. Vui lòng thử lại. ' . $e->getMessage()], 500);
        }
    }

    /**
     * Hiển thị form tạo câu hỏi cho mini-test.
     */
    public function showQuestionForm(MiniTest $miniTest)
    {
        $teacherId = Auth::id() ?? 0;
        $this->authorizeMiniTest($miniTest, $teacherId);

        // Load questions
        $miniTest->load(['questions' => fn($q) => $q->orderBy('thuTu'), 'course', 'chapter']);

        return view('Teacher.minitest-questions-simple', [
            'miniTest' => $miniTest,
            'teacher' => Auth::user(),
            'badges' => $this->teacherSidebarBadges($teacherId),
        ]);
    }

    /**
     * Lưu câu hỏi cho mini-test.
     */
    public function storeQuestions(Request $request, MiniTest $miniTest): JsonResponse
    {
        $teacherId = Auth::id() ?? 0;
        $this->authorizeMiniTest($miniTest, $teacherId);

        $validated = $request->validate([
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.question_text' => ['required', 'string'],
            'questions.*.question_type' => ['required', 'string', Rule::in(['single_choice', 'essay'])],
            'questions.*.points' => ['required', 'numeric', 'min:0'],
            'questions.*.image' => ['nullable', 'file', 'image', 'max:10240'], // 10MB
            'questions.*.audio' => ['nullable', 'file', 'mimes:mp3,wav,m4a,ogg', 'max:20480'], // 20MB
            'questions.*.pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'], // 10MB
            'questions.*.option_a' => ['nullable', 'string'],
            'questions.*.option_b' => ['nullable', 'string'],
            'questions.*.option_c' => ['nullable', 'string'],
            'questions.*.option_d' => ['nullable', 'string'],
            'questions.*.correct_answer' => ['nullable', 'string'], // Không bắt buộc với essay
        ]);

        try {
            DB::beginTransaction();

            // Xóa tất cả câu hỏi cũ (nếu có)
            $miniTest->questions()->delete();

            $courseDir = Str::slug($miniTest->course->tenKH);
            $chapterDir = Str::slug($miniTest->chapter->tenChuong);
            $miniTestDir = Str::slug($miniTest->title);
            $questionsDir = "{$courseDir}/{$chapterDir}/MiniTest/{$miniTestDir}/Questions";

            foreach ($validated['questions'] as $index => $questionData) {
                $imageUrl = null;
                $audioUrl = null;
                $pdfUrl = null;

                // Upload image nếu có
                if ($request->hasFile("questions.{$index}.image")) {
                    $imageFile = $request->file("questions.{$index}.image");
                    $imageName = 'q' . ($index + 1) . '_img_' . Str::uuid() . '.' . $imageFile->getClientOriginalExtension();
                    $imagePath = "{$questionsDir}/{$imageName}";
                    $imageFile->storeAs('', $imagePath, 's3');
                    Storage::disk('s3')->setVisibility($imagePath, 'public');
                    $imageUrl = env('PUBLIC_R2_URL') . '/' . $imagePath;
                }

                // Upload audio nếu có (LISTENING/SPEAKING)
                if ($request->hasFile("questions.{$index}.audio")) {
                    $audioFile = $request->file("questions.{$index}.audio");
                    $audioName = 'q' . ($index + 1) . '_audio_' . Str::uuid() . '.' . $audioFile->getClientOriginalExtension();
                    $audioPath = "{$questionsDir}/{$audioName}";
                    $audioFile->storeAs('', $audioPath, 's3');
                    Storage::disk('s3')->setVisibility($audioPath, 'public');
                    $audioUrl = env('PUBLIC_R2_URL') . '/' . $audioPath;
                }

                // Upload PDF nếu có (READING)
                if ($request->hasFile("questions.{$index}.pdf")) {
                    $pdfFile = $request->file("questions.{$index}.pdf");
                    $pdfName = 'q' . ($index + 1) . '_doc_' . Str::uuid() . '.pdf';
                    $pdfPath = "{$questionsDir}/{$pdfName}";
                    $pdfFile->storeAs('', $pdfPath, 's3');
                    Storage::disk('s3')->setVisibility($pdfPath, 'public');
                    $pdfUrl = env('PUBLIC_R2_URL') . '/' . $pdfPath;
                }

                // Tạo câu hỏi
                MiniTestQuestion::create([
                    'maMT' => $miniTest->maMT,
                    'thuTu' => $index + 1,
                    'loai' => $questionData['question_type'],
                    'noiDungCauHoi' => $questionData['question_text'],
                    'phuongAnA' => $questionData['option_a'] ?? null,
                    'phuongAnB' => $questionData['option_b'] ?? null,
                    'phuongAnC' => $questionData['option_c'] ?? null,
                    'phuongAnD' => $questionData['option_d'] ?? null,
                    'dapAnDung' => $questionData['correct_answer'] ?? null, // NULL cho essay
                    'diem' => $questionData['points'] ?? 1.0,
                    'audio_url' => $audioUrl,
                    'image_url' => $imageUrl,
                    'pdf_url' => $pdfUrl,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã lưu ' . count($validated['questions']) . ' câu hỏi thành công.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi lưu câu hỏi mini-test: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Không thể lưu câu hỏi. Vui lòng thử lại. Chi tiết: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Công bố mini-test (cho phép học viên thấy và làm bài).
     */
    public function publish(MiniTest $miniTest): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;
        $this->authorizeMiniTest($miniTest, $teacherId);

        // Kiểm tra xem minitest đã có câu hỏi chưa
        if ($miniTest->questions()->count() === 0) {
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
