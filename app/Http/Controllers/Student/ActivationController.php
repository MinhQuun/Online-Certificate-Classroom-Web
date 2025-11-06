<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ActivationCode;
use App\Models\ComboActivationCode;
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

        $comboActivationCodes = ComboActivationCode::with('combo')
            ->where('maHV', $student->maHV)
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();
        $comboActivationCodes->load('combo.courses');

        return view('Student.activation-code', [
            'student' => $student,
            'pendingEnrollments' => $pendingEnrollments,
            'activationCodes' => $activationCodes,
            'comboActivationCodes' => $comboActivationCodes,
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

        $inputCode = strtoupper(trim($validated['code']));

        $result = DB::transaction(function () use ($student, $inputCode) {
            $courseRecord = ActivationCode::where('code', $inputCode)
                ->where('maHV', $student->maHV)
                ->whereIn('trangThai', ['CREATED', 'SENT'])
                ->lockForUpdate()
                ->first();

            if ($courseRecord) {
                if (!$courseRecord->isStillValid()) {
                    return [
                        'status' => 'error',
                        'message' => 'Mã kích hoạt đã hết hạn. Vui lòng liên hệ hỗ trợ để được cấp lại.',
                    ];
                }

                $enrollment = Enrollment::where('maHV', $student->maHV)
                    ->where('maKH', $courseRecord->maKH)
                    ->lockForUpdate()
                    ->first();

                if (!$enrollment) {
                    return [
                        'status' => 'error',
                        'message' => 'Không tìm thấy ghi nhận khóa học tương ứng.',
                    ];
                }

                $courseRecord->loadMissing('course', 'invoice');
                $course = $courseRecord->course ?? $enrollment->course;

                $now = Carbon::now();
                $durationDays = $course?->thoiHanNgay;
                $expiresAt = $durationDays ? $now->copy()->addDays((int) $durationDays) : null;

                $courseRecord->update([
                    'trangThai' => 'USED',
                    'used_at' => $now,
                ]);

                ActivationCode::where('maHV', $student->maHV)
                    ->where('maKH', $courseRecord->maKH)
                    ->where('id', '!=', $courseRecord->id)
                    ->whereIn('trangThai', ['CREATED', 'SENT'])
                    ->update([
                        'trangThai' => 'EXPIRED',
                        'updated_at' => $now,
                        'expires_at' => $now,
                    ]);

                $enrollment->update([
                    'trangThai' => 'ACTIVE',
                    'activated_at' => $now,
                    'expires_at' => $expiresAt,
                ]);

                return [
                    'status' => 'ok',
                    'type' => 'course',
                    'course_name' => $course?->tenKH,
                    'expires_at' => $expiresAt,
                ];
            }

            $comboRecord = ComboActivationCode::where('code', $inputCode)
                ->where('maHV', $student->maHV)
                ->whereIn('trangThai', ['CREATED', 'SENT'])
                ->lockForUpdate()
                ->first();

            if (!$comboRecord) {
                return [
                    'status' => 'error',
                    'message' => 'Mã kích hoạt không hợp lệ hoặc đã được sử dụng.',
                ];
            }

            if (!$comboRecord->isStillValid()) {
                return [
                    'status' => 'error',
                    'message' => 'Mã kích hoạt đã hết hạn. Vui lòng liên hệ hỗ trợ để được cấp lại.',
                ];
            }

            $comboRecord->loadMissing('combo.courses');
            $combo = $comboRecord->combo;

            if (!$combo) {
                return [
                    'status' => 'error',
                    'message' => 'Không tìm thấy combo tương ứng với mã này.',
                ];
            }

            $now = Carbon::now();
            $activated = [];
            $alreadyActive = [];

            foreach ($combo->courses as $course) {
                $enrollment = Enrollment::where('maHV', $student->maHV)
                    ->where('maKH', $course->maKH)
                    ->lockForUpdate()
                    ->first();

                if (!$enrollment) {
                    $enrollment = new Enrollment([
                        'maHV' => $student->maHV,
                        'maKH' => $course->maKH,
                    ]);
                    $enrollment->ngayNhapHoc = $now->toDateString();
                    $enrollment->progress_percent = 0;
                    $enrollment->video_progress_percent = 0;
                    $enrollment->avg_minitest_score = 0;
                    $enrollment->last_lesson_id = null;
                }

                if (!$enrollment->ngayNhapHoc) {
                    $enrollment->ngayNhapHoc = $now->toDateString();
                }

                $enrollment->maGoi = $combo->maGoi;

                if ($enrollment->trangThai === 'ACTIVE') {
                    $alreadyActive[] = [
                        'maKH' => $course->maKH,
                        'tenKH' => $course->tenKH,
                    ];
                    continue;
                }

                $expiresAt = $course->thoiHanNgay ? $now->copy()->addDays((int) $course->thoiHanNgay) : null;

                $enrollment->trangThai = 'ACTIVE';
                $enrollment->activated_at = $now;
                $enrollment->expires_at = $expiresAt;
                $enrollment->updated_at = $now;
                $enrollment->save();

                ActivationCode::where('maHV', $student->maHV)
                    ->where('maKH', $course->maKH)
                    ->whereIn('trangThai', ['CREATED', 'SENT'])
                    ->update([
                        'trangThai' => 'EXPIRED',
                        'updated_at' => $now,
                        'expires_at' => $now,
                    ]);

                $activated[] = [
                    'maKH' => $course->maKH,
                    'tenKH' => $course->tenKH,
                    'expires_at' => $expiresAt,
                ];
            }

            $comboRecord->update([
                'trangThai' => 'USED',
                'used_at' => $now,
            ]);

            ComboActivationCode::where('maHV', $student->maHV)
                ->where('maGoi', $combo->maGoi)
                ->where('id', '!=', $comboRecord->id)
                ->whereIn('trangThai', ['CREATED', 'SENT'])
                ->update([
                    'trangThai' => 'EXPIRED',
                    'updated_at' => $now,
                    'expires_at' => $now,
                ]);

            return [
                'status' => 'ok',
                'type' => 'combo',
                'combo_name' => $combo->tenGoi,
                'activated_courses' => $activated,
                'already_active_courses' => $alreadyActive,
            ];
        });

        if (($result['status'] ?? null) !== 'ok') {
            return back()->withErrors(['code' => $result['message'] ?? 'Không thể kích hoạt mã này.']);
        }

        if (($result['type'] ?? null) === 'combo') {
            $activatedCourses = $result['activated_courses'] ?? [];
            $alreadyActive = $result['already_active_courses'] ?? [];

            $courseNames = array_map(fn ($item) => $item['tenKH'] ?? '', $activatedCourses);
            $courseNames = array_filter($courseNames);

            if (!empty($courseNames)) {
                $message = sprintf(
                    'Combo %s đã kích hoạt thành công %d khóa học: %s.',
                    $result['combo_name'] ?? 'bạn chọn',
                    count($courseNames),
                    implode(', ', $courseNames)
                );
            } else {
                $message = sprintf(
                    'Combo %s đã được xác nhận. Tất cả khóa học trong combo đã được kích hoạt trước đó.',
                    $result['combo_name'] ?? 'bạn chọn'
                );
            }

            $firstExpire = null;
            foreach ($activatedCourses as $course) {
                if (!empty($course['expires_at'])) {
                    $firstExpire = $course['expires_at'];
                    break;
                }
            }

            if ($firstExpire) {
                $message .= ' Khóa học mới sẽ hết hạn vào: ' . Carbon::parse($firstExpire)->format('d/m/Y H:i');
            }

            if (!empty($alreadyActive)) {
                $alreadyNames = array_map(fn ($item) => $item['tenKH'] ?? '', $alreadyActive);
                $alreadyNames = array_filter($alreadyNames);

                if (!empty($alreadyNames)) {
                    $message .= ' Đã kích hoạt trước đó: ' . implode(', ', $alreadyNames) . '.';
                }
            }
        } else {
            $message = sprintf(
                'Khóa học %s đã được kích hoạt thành công.',
                $result['course_name'] ?? 'bạn chọn'
            );

            if (!empty($result['expires_at'])) {
                $message .= ' Hiệu lực đến: ' . Carbon::parse($result['expires_at'])->format('d/m/Y H:i');
            }
        }

        return redirect()
            ->route('student.activations.form')
            ->with('success', $message);
    }
}
