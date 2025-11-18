<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected function requireStudentUser()
    {
        $user = Auth::user();

        if (!$user || !$user->student) {
            abort(403, 'Chỉ dành cho học viên');
        }

        return $user;
    }

    public function index(Request $request): JsonResponse
    {
        $user = $this->requireStudentUser();
        $limit = (int) $request->input('limit', 12);
        $limit = max(6, min($limit, 50));

        $notifications = StudentNotification::with(['course', 'combo'])
            ->forUser($user->maND)
            ->latestFirst()
            ->limit($limit)
            ->get();

        $unreadCount = StudentNotification::forUser($user->maND)
            ->unread()
            ->count();

        $payload = $notifications->map(function (StudentNotification $notification) {
            return [
                'id' => $notification->maTB,
                'title' => $notification->tieuDe,
                'content' => $notification->noiDung,
                'type' => $notification->loai,
                'type_label' => $notification->type_label,
                'badge_tone' => $notification->badge_tone,
                'is_read' => (bool) $notification->is_read,
                'read_at' => optional($notification->read_at)?->toIso8601String(),
                'created_at' => optional($notification->created_at)?->toIso8601String(),
                'time_label' => $notification->time_label,
                'action_url' => $notification->resolved_action_url,
                'action_label' => $notification->action_label ?? 'Xem chi tiết',
                'thumbnail' => $notification->thumbnail_url,
                'course' => $notification->course
                    ? [
                        'id' => $notification->course->maKH,
                        'name' => $notification->course->tenKH,
                        'slug' => $notification->course->slug,
                    ]
                    : null,
                'combo' => $notification->combo
                    ? [
                        'id' => $notification->combo->maGoi,
                        'name' => $notification->combo->tenGoi,
                        'slug' => $notification->combo->slug,
                    ]
                    : null,
            ];
        });

        return response()->json([
            'data' => $payload,
            'meta' => [
                'unread' => $unreadCount,
                'limit' => $limit,
            ],
        ]);
    }

    public function markAsRead(StudentNotification $notification): JsonResponse
    {
        $user = $this->requireStudentUser();

        if ($notification->maND !== $user->maND) {
            abort(403);
        }

        $notification->markAsRead();

        $unreadCount = StudentNotification::forUser($user->maND)
            ->unread()
            ->count();

        return response()->json([
            'status' => true,
            'unread' => $unreadCount,
        ]);
    }

    public function markAllAsRead(): JsonResponse
    {
        $user = $this->requireStudentUser();

        StudentNotification::forUser($user->maND)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'status' => true,
            'unread' => 0,
        ]);
    }
}
