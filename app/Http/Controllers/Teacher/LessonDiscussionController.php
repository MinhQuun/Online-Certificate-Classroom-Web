<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\LessonDiscussion;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonDiscussionController extends Controller
{
    public function togglePin(Request $request, LessonDiscussion $discussion): JsonResponse
    {
        $teacher = $request->user();
        $this->guardOwnership($teacher, $discussion);

        $discussion->is_pinned = !$discussion->is_pinned;
        $discussion->save();

        return response()->json([
            'data' => [
                'id'        => $discussion->id,
                'is_pinned' => (bool) $discussion->is_pinned,
            ],
            'message' => $discussion->is_pinned ? 'Đã ghim câu hỏi.' : 'Đã bỏ ghim câu hỏi.',
        ]);
    }

    public function toggleLock(Request $request, LessonDiscussion $discussion): JsonResponse
    {
        $teacher = $request->user();
        $this->guardOwnership($teacher, $discussion);

        $discussion->is_locked = !$discussion->is_locked;
        $discussion->save();

        return response()->json([
            'data' => [
                'id'        => $discussion->id,
                'is_locked' => (bool) $discussion->is_locked,
            ],
            'message' => $discussion->is_locked
                ? 'Đã khóa bình luận cho chủ đề này.'
                : 'Đã mở lại bình luận cho chủ đề này.',
        ]);
    }

    public function updateStatus(Request $request, LessonDiscussion $discussion): JsonResponse
    {
        $teacher = $request->user();
        $this->guardOwnership($teacher, $discussion);

        $validated = $request->validate([
            'status' => ['required', 'in:OPEN,RESOLVED,HIDDEN'],
        ]);

        $discussion->status = $validated['status'];
        $discussion->save();

        return response()->json([
            'data' => [
                'id'     => $discussion->id,
                'status' => $discussion->status,
            ],
            'message' => 'Đã cập nhật trạng thái chủ đề.',
        ]);
    }

    private function guardOwnership(?User $teacher, LessonDiscussion $discussion): void
    {
        if (!$teacher || $teacher->vaiTro !== 'GIANG_VIEN') {
            abort(403);
        }

        $lesson = $discussion->lesson()->with(['chapter.course' => fn ($query) => $query->select('maKH', 'maND')])->first();

        if (!$lesson || !$lesson->chapter?->course || $lesson->chapter->course->maND !== $teacher->maND) {
            abort(403);
        }
    }
}

