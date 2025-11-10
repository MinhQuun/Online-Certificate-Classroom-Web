<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Teacher\LoadsTeacherContext;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\MiniTest;
use App\Models\MiniTestMaterial;
use App\Models\MiniTestQuestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class MiniTestController extends Controller
{
    use LoadsTeacherContext;

    public function index(Request $request)
    {
        $teacher = Auth::user();
        $teacherId = $teacher?->getKey() ?? 0;

        $courses = Course::query()
            ->with([
                'chapters' => fn ($query) => $query->orderBy('thuTu'),
                'chapters.miniTests' => fn ($query) => $query
                    ->with([
                        'materials' => fn ($q) => $q->orderBy('created_at'),
                        'questions' => fn ($q) => $q->orderBy('thuTu'),
                    ])
                    ->orderBy('thuTu'),
            ])
            ->where('maND', $teacherId)
            ->orderBy('tenKH')
            ->get();

        $selectedCourseId = (int) $request->query('course', 0);
        if ($selectedCourseId && !$courses->contains('maKH', $selectedCourseId)) {
            $selectedCourseId = 0;
        }

        $activeCourse = $selectedCourseId
            ? $courses->firstWhere('maKH', $selectedCourseId)
            : $courses->first();

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

        return view('Teacher.minitests', [
            'type' => 'index',
            'teacher' => $teacher,
            'courses' => $courses,
            'activeCourse' => $activeCourse,
            'badges' => $this->teacherSidebarBadges($teacherId),
        ]);
    }

    public function showQuestionForm(MiniTest $miniTest)
    {
        $teacherId = Auth::id() ?? 0;
        $this->authorizeMiniTest($miniTest, $teacherId);

        $miniTest->load([
            'course',
            'chapter',
            'questions' => fn ($query) => $query->orderBy('thuTu'),
        ]);

        return view('Teacher.minitests', [
            'type' => 'questions',
            'teacher' => Auth::user(),
            'miniTest' => $miniTest,
            'badges' => $this->teacherSidebarBadges($teacherId),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;

        $validated = $request->validate([
            'course_id' => ['required', 'integer'],
            'chapter_id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'skill_type' => ['required', Rule::in([
                MiniTest::SKILL_LISTENING,
                MiniTest::SKILL_READING,
                MiniTest::SKILL_WRITING,
                MiniTest::SKILL_SPEAKING,
            ])],
            'order' => ['nullable', 'integer', 'min:1'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'time_limit' => ['nullable', 'integer', 'min:0', 'max:600'],
            'attempts' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $course = Course::where('maKH', $validated['course_id'])
            ->where('maND', $teacherId)
            ->firstOrFail();

        $chapter = Chapter::where('maChuong', $validated['chapter_id'])
            ->where('maKH', $course->maKH)
            ->firstOrFail();

        $nextOrder = $validated['order']
            ?? ((MiniTest::where('maChuong', $chapter->maChuong)->max('thuTu') ?? 0) + 1);

        $miniTest = MiniTest::create([
            'maKH' => $course->maKH,
            'maChuong' => $chapter->maChuong,
            'title' => $validated['title'],
            'skill_type' => $validated['skill_type'],
            'thuTu' => $nextOrder,
            'max_score' => 0,
            'trongSo' => $validated['weight'] ?? 0,
            'time_limit_min' => $validated['time_limit'] ?? 0,
            'attempts_allowed' => $validated['attempts'] ?? 1,
            'is_active' => false,
            'is_published' => false,
        ]);

        return redirect()
            ->route('teacher.minitests.index', [
                'course' => $course->maKH,
                '_fragment' => 'minitest-' . $miniTest->maMT,
            ])
            ->with('success', 'ÄÃ£ táº¡o mini-test má»›i thÃ nh cÃ´ng.');
    }

    public function update(Request $request, MiniTest $miniTest): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;
        $this->authorizeMiniTest($miniTest, $teacherId);

        $validated = $request->validate([
            'course_id' => ['required', 'integer'],
            'chapter_id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'skill_type' => ['required', Rule::in([
                MiniTest::SKILL_LISTENING,
                MiniTest::SKILL_READING,
                MiniTest::SKILL_WRITING,
                MiniTest::SKILL_SPEAKING,
            ])],
            'order' => ['nullable', 'integer', 'min:1'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'time_limit' => ['nullable', 'integer', 'min:0', 'max:600'],
            'attempts' => ['nullable', 'integer', 'min:1', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $course = Course::where('maKH', $validated['course_id'])
            ->where('maND', $teacherId)
            ->firstOrFail();

        $chapter = Chapter::where('maChuong', $validated['chapter_id'])
            ->where('maKH', $course->maKH)
            ->firstOrFail();

        $miniTest->update([
            'maKH' => $course->maKH,
            'maChuong' => $chapter->maChuong,
            'title' => $validated['title'],
            'skill_type' => $validated['skill_type'],
            'thuTu' => $validated['order'] ?? $miniTest->thuTu,
            'trongSo' => $validated['weight'] ?? $miniTest->weight,
            'time_limit_min' => $validated['time_limit'] ?? $miniTest->time_limit_min,
            'attempts_allowed' => $validated['attempts'] ?? $miniTest->attempts_allowed,
            'is_active' => (bool) ($validated['is_active'] ?? $miniTest->is_active),
        ]);

        return redirect()
            ->route('teacher.minitests.index', [
                'course' => $course->maKH,
                '_fragment' => 'minitest-' . $miniTest->maMT,
            ])
            ->with('success', 'ÄÃ£ cáº­p nháº­t mini-test.');
    }

    public function destroy(MiniTest $miniTest): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;
        $this->authorizeMiniTest($miniTest, $teacherId);

        $courseId = $miniTest->maKH;

        DB::beginTransaction();

        try {
            $miniTest->load(['questions', 'materials']);

            foreach ($miniTest->questions as $question) {
                $this->deleteQuestionAssets($question);
            }

            foreach ($miniTest->materials as $material) {
                $this->deleteStoredFile($material->public_url ?? null);
                $material->delete();
            }

            $miniTest->questions()->delete();
            $miniTest->delete();

            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            Log::error('Delete mini-test failed', [
                'mini_test_id' => $miniTest->maMT,
                'error' => $throwable->getMessage(),
            ]);

            return back()->with('error', 'CÃ³ lá»—i xáº£y ra khi xÃ³a mini-test. Vui lÃ²ng thá»­ láº¡i.');
        }

        return redirect()
            ->route('teacher.minitests.index', ['course' => $courseId])
            ->with('success', 'ÄÃ£ xÃ³a mini-test.');
    }

    public function storeQuestions(Request $request, MiniTest $miniTest): JsonResponse
    {
        $teacherId = Auth::id() ?? 0;
        $this->authorizeMiniTest($miniTest, $teacherId);

        $validated = $request->validate([
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.content' => ['required', 'string'],
            'questions.*.type' => ['required', Rule::in([
                MiniTestQuestion::TYPE_SINGLE_CHOICE,
                MiniTestQuestion::TYPE_MULTIPLE_CHOICE,
                MiniTestQuestion::TYPE_TRUE_FALSE,
                MiniTestQuestion::TYPE_ESSAY,
            ])],
            'questions.*.points' => ['required', 'numeric', 'min:0'],
            'questions.*.explanation' => ['nullable', 'string'],
        ]);

        $questionPayloads = collect($request->input('questions', []));

        DB::beginTransaction();

        try {
            $existingQuestions = $miniTest->questions()->get();
            foreach ($existingQuestions as $existingQuestion) {
                $this->deleteQuestionAssets($existingQuestion);
            }
            $miniTest->questions()->delete();

            $totalScore = 0;

            foreach ($questionPayloads as $index => $payload) {
                $questionType = $payload['type'];

                $options = Arr::get($payload, 'options', []);
                $options = is_array($options) ? $options : [];

                $correctRaw = Arr::get($payload, 'correct');
                $correctAnswers = $this->normalizeCorrectAnswers($correctRaw);

                if (in_array($questionType, [
                    MiniTestQuestion::TYPE_SINGLE_CHOICE,
                    MiniTestQuestion::TYPE_MULTIPLE_CHOICE,
                ], true)) {
                    $options = $this->prepareChoiceOptions($options);

                    if (count($options) < 2) {
                        throw new \InvalidArgumentException('Cáº§n tá»‘i thiá»ƒu 2 Ä‘Ã¡p Ã¡n cho cÃ¢u há»i tráº¯c nghiá»‡m.');
                    }

                    if (empty($correctAnswers)) {
                        throw new \InvalidArgumentException('Vui lÃ²ng chá»n Ä‘Ã¡p Ã¡n Ä‘Ãºng cho cÃ¢u há»i tráº¯c nghiá»‡m.');
                    }

                    if (
                        $questionType === MiniTestQuestion::TYPE_SINGLE_CHOICE
                        && count($correctAnswers) !== 1
                    ) {
                        throw new \InvalidArgumentException('CÃ¢u há»i má»™t Ä‘Ã¡p Ã¡n chá»‰ Ä‘Æ°á»£c chá»n má»™t Ä‘Ã¡p Ã¡n Ä‘Ãºng.');
                    }

                    foreach ($correctAnswers as $answer) {
                        if (!array_key_exists($answer, $options)) {
                            throw new \InvalidArgumentException('ÄÃ¡p Ã¡n Ä‘Ãºng khÃ´ng há»£p lá»‡.');
                        }
                    }
                } elseif ($questionType === MiniTestQuestion::TYPE_TRUE_FALSE) {
                    if (empty($correctAnswers)) {
                        throw new \InvalidArgumentException('Vui lÃ²ng chá»n TRUE hoáº·c FALSE cho cÃ¢u há»i Ä‘Ãºng sai.');
                    }
                    $firstAnswer = strtoupper($correctAnswers[0]);
                    if (!in_array($firstAnswer, ['TRUE', 'FALSE'], true)) {
                        throw new \InvalidArgumentException('ÄÃ¡p Ã¡n cho cÃ¢u Ä‘Ãºng sai pháº£i lÃ  TRUE hoáº·c FALSE.');
                    }
                    $correctAnswers = [$firstAnswer];
                    $options = [
                        'A' => 'TRUE',
                        'B' => 'FALSE',
                    ];
                } else {
                    // essay
                    $options = [];
                    $correctAnswers = [];
                }

                $question = new MiniTestQuestion([
                    'maMT' => $miniTest->maMT,
                    'thuTu' => (int) (Arr::get($payload, 'order') ?: $index + 1),
                    'loai' => $questionType,
                    'noiDungCauHoi' => $payload['content'],
                    'giaiThich' => Arr::get($payload, 'explanation'),
                    'diem' => (float) $payload['points'],
                ]);

                $question->phuongAnA = $options['A'] ?? null;
                $question->phuongAnB = $options['B'] ?? null;
                $question->phuongAnC = $options['C'] ?? null;
                $question->phuongAnD = $options['D'] ?? null;
                $question->dapAnDung = implode(';', $correctAnswers);

                $question->save();

                $this->storeQuestionMedia($request, $question, $index);

                $totalScore += (float) $payload['points'];
            }

            $miniTest->update(['max_score' => $totalScore]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'ÄÃ£ lÆ°u cÃ¢u há»i mini-test.',
                'redirect' => route('teacher.minitests.index', [
                    'course' => $miniTest->maKH,
                    '_fragment' => 'minitest-' . $miniTest->maMT,
                ]),
            ]);
        } catch (Throwable $throwable) {
            DB::rollBack();

            Log::error('Save mini-test questions failed', [
                'mini_test_id' => $miniTest->maMT,
                'error' => $throwable->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $throwable->getMessage() ?: 'ÄÃ£ xáº£y ra lá»—i khi lÆ°u cÃ¢u há»i.',
            ], 422);
        }
    }

    public function storeMaterial(Request $request, MiniTest $miniTest): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;
        $this->authorizeMiniTest($miniTest, $teacherId);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['pdf', 'image', 'zip', 'audio'])],
            'source_type' => ['required', Rule::in(['file', 'url'])],
            'file' => ['required_if:source_type,file', 'file', 'max:102400', 'mimetypes:application/pdf,image/jpeg,image/png,image/jpg,application/zip,application/x-zip-compressed,audio/mpeg,audio/mp3,audio/wav'],
            'url' => ['required_if:source_type,url', 'url', 'max:700'],
            'visibility' => ['required', Rule::in(['public', 'private'])],
        ], [
            'file.mimetypes' => 'File khÃ´ng Ä‘Ãºng Ä‘á»‹nh dáº¡ng cho phÃ©p.',
        ]);

        try {
            $publicUrl = null;
            $mimeType = null;

            if ($validated['source_type'] === 'file') {
                $file = $request->file('file');
                $mimeType = $file->getMimeType();
                $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $extension = $file->getClientOriginalExtension();
                $path = $file->storeAs(
                    'mini-tests/materials/' . $miniTest->maMT,
                    $fileName . '-' . now()->timestamp . '.' . $extension,
                    's3'
                );
                $publicUrl = Storage::disk('s3')->url($path);
            } else {
                $publicUrl = $validated['url'];
                $mimeType = $this->guessMime($validated['type'], $publicUrl);
            }

            MiniTestMaterial::create([
                'maMT' => $miniTest->maMT,
                'tenTL' => $validated['name'],
                'loai' => $validated['type'],
                'mime_type' => $mimeType ?? 'application/octet-stream',
                'visibility' => $validated['visibility'],
                'public_url' => $publicUrl,
            ]);

            return redirect()
                ->route('teacher.minitests.index', [
                    'course' => $miniTest->maKH,
                    '_fragment' => 'minitest-' . $miniTest->maMT,
                ])
                ->with('success', 'ÄÃ£ thÃªm tÃ i liá»‡u thÃ nh cÃ´ng.');
        } catch (Throwable $throwable) {
            Log::error('Store mini-test material failed', [
                'mini_test_id' => $miniTest->maMT,
                'error' => $throwable->getMessage(),
            ]);

            return back()->with('error', 'KhÃ´ng thá»ƒ lÆ°u tÃ i liá»‡u. Vui lÃ²ng thá»­ láº¡i.');
        }
    }

    public function publish(MiniTest $miniTest): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;
        $this->authorizeMiniTest($miniTest, $teacherId);

        $miniTest->loadCount('questions');

        if ($miniTest->questions_count === 0) {
            return redirect()
                ->route('teacher.minitests.index', ['course' => $miniTest->maKH])
                ->with('error', 'Mini-test cáº§n cÃ³ Ã­t nháº¥t má»™t cÃ¢u há»i trÆ°á»›c khi cÃ´ng bá»‘.');
        }

        $miniTest->update(['is_published' => true, 'is_active' => true]);

        return redirect()
            ->route('teacher.minitests.index', [
                'course' => $miniTest->maKH,
                '_fragment' => 'minitest-' . $miniTest->maMT,
            ])
            ->with('success', 'ÄÃ£ cÃ´ng bá»‘ mini-test.');
    }

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
            ->with('success', 'ÄÃ£ há»§y cÃ´ng bá»‘ mini-test.');
    }

    public function destroyMaterial(MiniTestMaterial $material): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;
        $miniTest = $material->miniTest()->firstOrFail();

        $this->authorizeMiniTest($miniTest, $teacherId);

        $courseId = $miniTest->maKH;

        try {
            $this->deleteStoredFile($material->public_url ?? null);
            $material->delete();

            return redirect()
                ->route('teacher.minitests.index', [
                    'course' => $courseId,
                    '_fragment' => 'minitest-' . $miniTest->maMT,
                ])
                ->with('success', 'ÄÃ£ xÃ³a tÃ i liá»‡u.');
        } catch (Throwable $throwable) {
            Log::error('Delete mini-test material failed', [
                'material_id' => $material->id,
                'error' => $throwable->getMessage(),
            ]);

            return back()->with('error', 'KhÃ´ng thá»ƒ xÃ³a tÃ i liá»‡u. Vui lÃ²ng thá»­ láº¡i.');
        }
    }

    protected function authorizeMiniTest(MiniTest $miniTest, int $teacherId): void
    {
        $ownerId = $miniTest->course()
            ->value('maND');

        abort_if($ownerId !== $teacherId, 403, 'Báº¡n khÃ´ng cÃ³ quyá»n truy cáº­p mini-test nÃ y.');
    }

    protected function normalizeCorrectAnswers(mixed $raw): array
    {
        if (is_null($raw)) {
            return [];
        }

        if (is_array($raw)) {
            return array_values(array_filter(array_map(
                fn ($value) => is_string($value) ? strtoupper(trim($value)) : $value,
                $raw
            ), fn ($value) => $value !== '' && !is_null($value)));
        }

        $stringValue = trim((string) $raw);
        if ($stringValue === '') {
            return [];
        }

        if (str_contains($stringValue, ';')) {
            return array_values(array_filter(array_map('trim', explode(';', $stringValue))));
        }

        return [$stringValue];
    }

    protected function prepareChoiceOptions(array $options): array
    {
        $prepared = [];

        foreach (['A', 'B', 'C', 'D'] as $label) {
            $value = Arr::get($options, $label);
            if (!is_null($value) && trim((string) $value) !== '') {
                $prepared[$label] = trim((string) $value);
            }
        }

        return $prepared;
    }

    protected function storeQuestionMedia(Request $request, MiniTestQuestion $question, int $index): void
    {
        foreach (['audio' => 'audio/mpeg', 'image' => null, 'pdf' => 'application/pdf'] as $type => $defaultMime) {
            $file = $request->file("questions.$index.$type");
            if (!$file) {
                continue;
            }

            $folder = match ($type) {
                'audio' => 'audio',
                'image' => 'images',
                'pdf' => 'pdf',
                default => 'files',
            };

            $path = $file->storeAs(
                "mini-tests/questions/{$question->maMT}/{$folder}",
                Str::uuid()->toString() . '.' . $file->getClientOriginalExtension(),
                's3'
            );

            $url = Storage::disk('s3')->url($path);

            if ($type === 'audio') {
                $question->audio_url = $url;
            } elseif ($type === 'image') {
                $question->image_url = $url;
            } else {
                $question->pdf_url = $url;
            }

            $question->save();
        }
    }

    protected function deleteQuestionAssets(MiniTestQuestion $question): void
    {
        $this->deleteStoredFile($question->audio_url);
        $this->deleteStoredFile($question->image_url);
        $this->deleteStoredFile($question->pdf_url);
    }

    protected function deleteStoredFile(?string $url): void
    {
        if (!$url) {
            return;
        }

        $path = parse_url($url, PHP_URL_PATH);
        if (!$path) {
            return;
        }

        $path = ltrim($path, '/');

        try {
            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
            }
        } catch (Throwable $throwable) {
            Log::warning('Unable to delete stored file from R2', [
                'path' => $path,
                'error' => $throwable->getMessage(),
            ]);
        }
    }

    protected function guessMime(string $type, string $url): string
    {
        $type = strtolower($type);

        return match ($type) {
            'pdf' => 'application/pdf',
            'audio' => 'audio/mpeg',
            'zip' => 'application/zip',
            'image' => 'image/jpeg',
            default => $this->inferMimeFromUrl($url),
        };
    }

    protected function inferMimeFromUrl(string $url): string
    {
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));

        return match ($extension) {
            'pdf' => 'application/pdf',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'zip' => 'application/zip',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            default => 'application/octet-stream',
        };
    }
}
