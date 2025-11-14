<?php

namespace App\Http\Controllers\API\Student;

use App\Http\Controllers\Controller;
use App\Models\ActivationCode;
use App\Models\Enrollment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = $request->user()->loadMissing('student');

        if (! $user->student) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tài khoản chưa gắn với hồ sơ học viên.',
            ], 403);
        }

        $inputCode = trim($validated['code']);

        $result = DB::transaction(function () use ($user, $inputCode) {
            $record = ActivationCode::query()
                ->where('code', $inputCode)
                ->where('maHV', $user->student->maHV)
                ->whereIn('trangThai', ['CREATED', 'SENT'])
                ->lockForUpdate()
                ->first();

            if (! $record) {
                return [
                    'status'  => 'error',
                    'message' => 'Mã kích hoạt không hợp lệ hoặc đã được sử dụng.',
                ];
            }

            if (! $record->isStillValid()) {
                return [
                    'status'  => 'error',
                    'message' => 'Mã kích hoạt đã hết hạn. Vui lòng liên hệ hỗ trợ.',
                ];
            }

            $enrollment = Enrollment::query()
                ->where('maHV', $user->student->maHV)
                ->where('maKH', $record->maKH)
                ->lockForUpdate()
                ->first();

            if (! $enrollment) {
                return [
                    'status'  => 'error',
                    'message' => 'Không tìm thấy lượt ghi danh phù hợp.',
                ];
            }

            $record->loadMissing('course');
            $course = $record->course ?? $enrollment->course;

            $now = Carbon::now();
            $durationDays = $course?->thoiHanNgay;
            $expiresAt = $durationDays ? $now->copy()->addDays((int) $durationDays) : null;

            $record->update([
                'trangThai' => 'USED',
                'used_at'   => $now,
            ]);

            ActivationCode::query()
                ->where('maHV', $user->student->maHV)
                ->where('maKH', $record->maKH)
                ->where('id', '!=', $record->id)
                ->whereIn('trangThai', ['CREATED', 'SENT'])
                ->update([
                    'trangThai' => 'EXPIRED',
                    'updated_at'=> $now,
                    'expires_at'=> $now,
                ]);

            $enrollment->update([
                'trangThai'    => 'ACTIVE',
                'activated_at' => $now,
                'expires_at'   => $expiresAt,
            ]);

            return [
                'status'      => 'success',
                'course_name' => $course?->tenKH,
                'course_id'   => $course?->maKH,
                'expires_at'  => $expiresAt?->toIso8601String(),
            ];
        });

        if (($result['status'] ?? null) !== 'success') {
            return response()->json([
                'status'  => 'error',
                'message' => $result['message'] ?? 'Không thể kích hoạt mã này.',
            ], 422);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Kích hoạt khóa học thành công.',
            'data'    => [
                'course_id'   => $result['course_id'],
                'course_name' => $result['course_name'],
                'expires_at'  => $result['expires_at'],
            ],
        ]);
    }
}
