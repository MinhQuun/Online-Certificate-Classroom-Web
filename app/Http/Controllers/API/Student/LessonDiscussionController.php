<?php

namespace App\Http\Controllers\API\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonDiscussion;
use App\Models\LessonDiscussionReply;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LessonDiscussionController extends Controller
{
    public function index(Request $request, Lesson $lesson): JsonResponse
    {
        $lesson->loadMissing([
            'chapter.course' => fn ($query) => $query->select('maKH', 'maND'),
        ]);

        $perPage = (int) $request->query('per_page', 10);
        $perPage = max(5, min(25, $perPage));

        $discussions = LessonDiscussion::with([
            'author' => fn ($query) => $query->select('maND', 'hoTen', 'vaiTro'),
            'replies.author' => fn ($query) => $query->select('maND', 'hoTen', 'vaiTro'),
        ])
            ->visible()
            ->where('maBH', $lesson->maBH)
            ->orderByDesc('is_pinned')
            ->orderByDesc('last_replied_at')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $currentUser = $request->user();

        return response()->json([
            'data' => $discussions->getCollection()
                ->map(fn (LessonDiscussion $discussion) => $this->transformDiscussion($discussion, $currentUser))
                ->values(),
            'meta' => [
                'current_page' => $discussions->currentPage(),
                'per_page'     => $discussions->perPage(),
                'total'        => $discussions->total(),
                'has_more'     => $discussions->hasMorePages(),
            ],
        ]);
    }

    public function store(Request $request, Lesson $lesson): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            abort(401);
        }

        $lesson->loadMissing([
            'chapter.course' => fn ($query) => $query->select('maKH', 'maND'),
        ]);

        if (!$this->canStudentAskQuestion($user, $lesson)) {
            abort(403, 'Báº¡n chÆ°a Ä‘Æ°á»£c quyá»n Ä‘áº·t cÃ¢u há»i cho bÃ i há»c nÃ y.');
        }

        $validated = $request->validate([
            'noi_dung' => ['required', 'string', 'max:5000'],
        ], [
            'noi_dung.required' => 'Vui lÃ²ng nháº­p ná»™i dung cÃ¢u há»i.',
            'noi_dung.max'      => 'CÃ¢u há»i quÃ¡ dÃ i, hÃ£y rÃºt gá»n dÆ°á»›i 5000 kÃ½ tá»±.',
        ]);

        $content = $this->sanitizeContent($validated['noi_dung']);

        if ($this->visibleCharacterCount($content) < 8) {
            return response()->json([
                'message' => 'Ná»™i dung cÃ¢u há»i cáº§n tá»‘i thiá»ƒu 8 kÃ½ tá»± Ä‘á»ƒ giÃºp giÃ¡o viÃªn hiá»ƒu rÃµ váº¥n Ä‘á».',
            ], 422);
        }

        $discussion = DB::transaction(function () use ($lesson, $user, $content) {
            $record = LessonDiscussion::create([
                'maBH'            => $lesson->maBH,
                'maND'            => $user->maND,
                'noiDung'         => $content,
                'status'          => 'OPEN',
                'is_pinned'       => false,
                'is_locked'       => false,
                'reply_count'     => 0,
                'last_replied_at' => now(),
            ]);

            return $record->fresh([
                'author' => fn ($query) => $query->select('maND', 'hoTen', 'vaiTro'),
                'replies',
            ]);
        });

        return response()->json([
            'data' => $this->transformDiscussion($discussion, $user),
            'message' => 'ÄÃ£ Ä‘Äƒng cÃ¢u há»i thÃ nh cÃ´ng.',
        ], 201);
    }

    public function storeReply(Request $request, Lesson $lesson, LessonDiscussion $discussion): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            abort(401);
        }

        if ($discussion->maBH !== $lesson->maBH) {
            abort(404);
        }

        $lesson->loadMissing([
            'chapter.course' => fn ($query) => $query->select('maKH', 'maND'),
        ]);

        if ($discussion->is_locked) {
            return response()->json([
                'message' => 'Chá»§ Ä‘á» nÃ y Ä‘Ã£ bá»‹ khÃ³a bÃ¬nh luáº­n.',
            ], 423);
        }

        if (!$this->canParticipateInDiscussion($user, $lesson)) {
            abort(403, 'Báº¡n chÆ°a Ä‘Æ°á»£c quyá»n pháº£n há»“i trong chá»§ Ä‘á» nÃ y.');
        }

        $validated = $request->validate([
            'noi_dung'        => ['required', 'string', 'max:5000'],
            'parent_reply_id' => ['nullable', 'integer'],
        ], [
            'noi_dung.required' => 'Vui lÃ²ng nháº­p ná»™i dung pháº£n há»“i.',
            'noi_dung.max'      => 'Ná»™i dung pháº£n há»“i quÃ¡ dÃ i (tá»‘i Ä‘a 5000 kÃ½ tá»±).',
        ]);

        $content = $this->sanitizeContent($validated['noi_dung']);

        if ($this->visibleCharacterCount($content) < 3) {
            return response()->json([
                'message' => 'Pháº£n há»“i cáº§n tá»‘i thiá»ƒu 3 kÃ½ tá»±.',
            ], 422);
        }

        $parentReplyId = $validated['parent_reply_id'] ?? null;

        if ($parentReplyId) {
            $parentExists = LessonDiscussionReply::where('discussion_id', $discussion->id)
                ->where('id', $parentReplyId)
                ->exists();

            if (!$parentExists) {
                return response()->json([
                    'message' => 'Pháº£n há»“i gá»‘c khÃ´ng há»£p lá»‡.',
                ], 422);
            }
        }

        $isOfficial = $this->isOfficialResponder($user, $lesson);

        $reply = DB::transaction(function () use ($discussion, $user, $content, $parentReplyId, $isOfficial) {
            $createdReply = LessonDiscussionReply::create([
                'discussion_id'   => $discussion->id,
                'maND'            => $user->maND,
                'noiDung'         => $content,
                'parent_reply_id' => $parentReplyId,
                'is_official'     => $isOfficial,
            ]);

            $discussion->increment('reply_count');
            $discussion->update(['last_replied_at' => now()]);

            if ($isOfficial && !$discussion->isResolved()) {
                $discussion->markResolved(true);
            }

            return $createdReply->fresh([
                'author' => fn ($query) => $query->select('maND', 'hoTen', 'vaiTro'),
            ]);
        });

        return response()->json([
            'data' => [
                'reply'      => $this->transformReply($reply, $user),
                'discussion' => $this->transformDiscussion(
                    $discussion->fresh([
                        'author' => fn ($query) => $query->select('maND', 'hoTen', 'vaiTro'),
                        'replies.author' => fn ($query) => $query->select('maND', 'hoTen', 'vaiTro'),
                    ]),
                    $user
                ),
            ],
            'message' => 'ÄÃ£ gá»­i pháº£n há»“i.',
        ], 201);
    }

    public function destroy(Request $request, Lesson $lesson, LessonDiscussion $discussion): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            abort(401);
        }

        if ($discussion->maBH !== $lesson->maBH) {
            abort(404);
        }

        if (!$this->canDeleteDiscussion($user, $lesson, $discussion)) {
            abort(403, 'Báº¡n khÃ´ng thá»ƒ xÃ³a chá»§ Ä‘á» nÃ y.');
        }

        $discussion->hide();

        return response()->json([
            'message' => 'ÄÃ£ áº©n chá»§ Ä‘á» khá»i danh sÃ¡ch há»i Ä‘Ã¡p.',
        ]);
    }

    public function destroyReply(Request $request, Lesson $lesson, LessonDiscussion $discussion, LessonDiscussionReply $reply): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            abort(401);
        }

        if ($discussion->maBH !== $lesson->maBH || $reply->discussion_id !== $discussion->id) {
            abort(404);
        }

        if (!$this->canDeleteReply($user, $lesson, $reply)) {
            abort(403, 'Báº¡n khÃ´ng thá»ƒ xÃ³a pháº£n há»“i nÃ y.');
        }

        DB::transaction(function () use ($discussion, $reply) {
            $nestedCount = $this->countNestedReplies($reply);

            $reply->delete();

            $totalRemoved = 1 + $nestedCount;
            $discussion->reply_count = max(0, $discussion->reply_count - $totalRemoved);
            $discussion->last_replied_at = now();
            $discussion->save();
        });

        return response()->json([
            'message' => 'ÄÃ£ xÃ³a pháº£n há»“i.',
        ]);
    }

    private function countNestedReplies(LessonDiscussionReply $reply): int
    {
        $count = 0;
        $stack = [$reply->id];

        while ($stack) {
            $currentId = array_pop($stack);

            $children = LessonDiscussionReply::where('parent_reply_id', $currentId)->pluck('id');
            if ($children->isEmpty()) {
                continue;
            }

            $count += $children->count();
            foreach ($children as $childId) {
                $stack[] = $childId;
            }
        }

        return $count;
    }

    private function transformDiscussion(LessonDiscussion $discussion, ?User $currentUser): array
    {
        $author = $discussion->author;

        return [
            'id'                 => $discussion->id,
            'lesson_id'          => $discussion->maBH,
            'content'            => $discussion->noiDung,
            'status'             => $discussion->status,
            'is_pinned'          => (bool) $discussion->is_pinned,
            'is_locked'          => (bool) $discussion->is_locked,
            'reply_count'        => (int) $discussion->reply_count,
            'created_at'         => $discussion->created_at?->toIso8601String(),
            'updated_at'         => $discussion->updated_at?->toIso8601String(),
            'last_replied_at'    => $discussion->last_replied_at?->toIso8601String(),
            'created_human'      => $this->humanizeTime($discussion->created_at),
            'last_replied_human' => $this->humanizeTime($discussion->last_replied_at),
            'author' => [
                'id'       => $author?->maND,
                'name'     => $author?->hoTen,
                'role'     => $author?->vaiTro,
                'initials' => $this->resolveInitials($author?->hoTen),
            ],
            'can_delete' => $currentUser
                ? $this->canDeleteDiscussion($currentUser, $discussion->lesson ?? null, $discussion)
                : false,
            'replies' => $discussion->relationLoaded('replies')
                ? $discussion->replies->map(fn ($reply) => $this->transformReply($reply, $currentUser))->values()
                : [],
        ];
    }

    private function transformReply(LessonDiscussionReply $reply, ?User $currentUser): array
    {
        $author = $reply->author;

        return [
            'id'              => $reply->id,
            'discussion_id'   => $reply->discussion_id,
            'content'         => $reply->noiDung,
            'parent_reply_id' => $reply->parent_reply_id,
            'is_official'     => (bool) $reply->is_official,
            'created_at'      => $reply->created_at?->toIso8601String(),
            'updated_at'      => $reply->updated_at?->toIso8601String(),
            'created_human'   => $this->humanizeTime($reply->created_at),
            'author'          => [
                'id'       => $author?->maND,
                'name'     => $author?->hoTen,
                'role'     => $author?->vaiTro,
                'initials' => $this->resolveInitials($author?->hoTen),
            ],
            'can_delete'      => $currentUser
                ? $this->canDeleteReply($currentUser, null, $reply)
                : false,
        ];
    }

    private function humanizeTime(?Carbon $time): ?string
    {
        if (!$time) {
            return null;
        }

        return $time->copy()->locale('vi')->diffForHumans();
    }

    private function resolveInitials(?string $name): ?string
    {
        if (!$name) {
            return null;
        }

        $parts = preg_split('/\s+/u', trim($name));

        if (empty($parts)) {
            return null;
        }

        $initials = collect($parts)
            ->map(fn ($part) => Str::substr($part, 0, 1))
            ->take(2)
            ->implode('');

        return Str::upper($initials);
    }

    private function sanitizeContent(string $input): string
    {
        $normalized = str_replace(["\r\n", "\r"], "\n", $input);
        $normalized = preg_replace("/\n{3,}/", "\n\n", $normalized) ?? $normalized;

        return trim(strip_tags($normalized));
    }

    private function visibleCharacterCount(string $input): int
    {
        $plain = preg_replace('/\s+/u', '', $input);

        return mb_strlen($plain ?? '');
    }

    private function canStudentAskQuestion(User $user, Lesson $lesson): bool
    {
        if ($user->vaiTro !== 'HOC_VIEN') {
            return false;
        }

        return $this->hasActiveEnrollment($user, $lesson);
    }

    private function canParticipateInDiscussion(User $user, Lesson $lesson): bool
    {
        if (in_array($user->vaiTro, ['ADMIN'], true)) {
            return true;
        }

        if ($user->vaiTro === 'GIANG_VIEN') {
            $lesson->loadMissing('chapter.course');
            $courseOwnerId = $lesson->chapter?->course?->maND;

            return $courseOwnerId && $courseOwnerId === $user->maND;
        }

        if ($user->vaiTro === 'HOC_VIEN') {
            return $this->hasActiveEnrollment($user, $lesson);
        }

        return false;
    }

    private function hasActiveEnrollment(User $user, Lesson $lesson): bool
    {
        $lesson->loadMissing('chapter.course');
        $course = $lesson->chapter?->course;

        if (!$course) {
            return false;
        }

        $student = DB::table('hocvien')
            ->select('maHV')
            ->where('maND', $user->maND)
            ->first();

        if (!$student) {
            return false;
        }

        return DB::table('hocvien_khoahoc')
            ->where('maHV', $student->maHV)
            ->where('maKH', $course->maKH)
            ->where('trangThai', 'ACTIVE')
            ->exists();
    }

    private function isOfficialResponder(User $user, Lesson $lesson): bool
    {
        if ($user->vaiTro === 'ADMIN') {
            return true;
        }

        if ($user->vaiTro === 'GIANG_VIEN') {
            $lesson->loadMissing('chapter.course');
            $courseOwnerId = $lesson->chapter?->course?->maND;

            return $courseOwnerId && $courseOwnerId === $user->maND;
        }

        return false;
    }

    private function canDeleteDiscussion(User $user, ?Lesson $lesson, LessonDiscussion $discussion): bool
    {
        if ($user->vaiTro === 'ADMIN') {
            return true;
        }

        if ($user->maND === $discussion->maND) {
            return true;
        }

        if ($user->vaiTro === 'GIANG_VIEN') {
            $lesson ??= $discussion->lesson;
            $lesson?->loadMissing('chapter.course');
            $ownerId = $lesson?->chapter?->course?->maND;

            return $ownerId && $ownerId === $user->maND;
        }

        return false;
    }

    private function canDeleteReply(User $user, ?Lesson $lesson, LessonDiscussionReply $reply): bool
    {
        if ($user->vaiTro === 'ADMIN') {
            return true;
        }

        if ($user->maND === $reply->maND) {
            return true;
        }

        if ($user->vaiTro === 'GIANG_VIEN') {
            $lesson ??= $reply->discussion?->lesson;
            $lesson?->loadMissing('chapter.course');
            $ownerId = $lesson?->chapter?->course?->maND;

            return $ownerId && $ownerId === $user->maND;
        }

        return false;
    }
}

