<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ActivationCode;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActivationController extends Controller
{
    public function showForm()
    {
        $user = Auth::user();
        $student = $user?->student;

        if (!$student) {
            abort(403, 'Không tìm thấy hồ sơ học viên.');
        }

        $pendingEnrollments = Enrollment::with('course')
            ->where('maHV', $student->maHV)
            ->where('trangThai', 'PENDING')
            ->orderByDesc('updated_at')
            ->get();

        $activationCodes = ActivationCode::with('course')
            ->where('maHV', $student->maHV)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return view('Student.activation-code', [
            'student' => $student,
            'pendingEnrollments' => $pendingEnrollments,
            'activationCodes' => $activationCodes,
        ]);
    }

    public function redeem(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = Auth::user();
        $student = $user?->student;

        if (!$student) {
            return redirect()->route('student.courses.index')
                ->withErrors(['code' => 'Không tìm thấy hồ sơ học viên.']);
        }

        $inputCode = trim($validated['code']);

        $result = DB::transaction(function () use ($student, $inputCode) {
            $record = ActivationCode::where('code', $inputCode)
                ->where('maHV', $student->maHV)
                ->whereIn('trangThai', ['CREATED', 'SENT'])
                ->lockForUpdate()
                ->first();

            if (!$record) {
                return [
                    'status' => 'error',
                    'message' => 'Mã kích hoạt không hợp lệ hoặc đã được sử dụng.',
                ];
            }

            if (!$record->isStillValid()) {
                return [
                    'status' => 'error',
                    'message' => 'Mã kích hoạt đã hết hạn. Vui lòng liên hệ hỗ trợ để được cấp lại.',
                ];
            }

            $enrollment = Enrollment::where('maHV', $student->maHV)
                ->where('maKH', $record->maKH)
                ->lockForUpdate()
                ->first();

            if (!$enrollment) {
                return [
                    'status' => 'error',
                    'message' => 'Không tìm thấy ghi nhận khóa học tương ứng.',
                ];
            }

            $record->loadMissing('course', 'invoice');
            $course = $record->course ?? $enrollment->course;

            $now = Carbon::now();
            $durationDays = $course?->thoiHanNgay;
            $expiresAt = $durationDays ? $now->copy()->addDays((int) $durationDays) : null;

            $record->update([
                'trangThai' => 'USED',
                'used_at'   => $now,
            ]);

            ActivationCode::where('maHV', $student->maHV)
                ->where('maKH', $record->maKH)
                ->where('id', '!=', $record->id)
                ->whereIn('trangThai', ['CREATED', 'SENT'])
                ->update([
                    'trangThai' => 'EXPIRED',
                    'updated_at' => $now,
                    'expires_at' => $now,
                ]);

            $enrollment->update([
                'trangThai'    => 'ACTIVE',
                'activated_at' => $now,
                'expires_at'   => $expiresAt,
            ]);

            return [
                'status' => 'ok',
                'course_name' => $course?->tenKH,
                'expires_at' => $expiresAt,
            ];
        });

        if (($result['status'] ?? null) !== 'ok') {
            return back()->withErrors(['code' => $result['message'] ?? 'Không thể kích hoạt mã này.']);
        }

        $message = sprintf(
            'Khóa học %s đã được kích hoạt thành công.',
            $result['course_name'] ?? 'bạn chọn'
        );

        if (!empty($result['expires_at'])) {
            $message .= ' Hiệu lực đến: ' . Carbon::parse($result['expires_at'])->format('d/m/Y H:i');
        }

        return redirect()
            ->route('student.activations.form')
            ->with('success', $message);
    }
}