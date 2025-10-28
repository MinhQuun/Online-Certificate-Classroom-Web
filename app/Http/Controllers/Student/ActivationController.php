<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ActivationCode;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ActivationController extends Controller
{
    public function showForm()
    {
        return view('activation_form');
    }

    public function redeem(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $student = auth()->user()->student;
        $maHV = $student->maHV;
        $inputCode = $request->code;

        $record = ActivationCode::where('code', $inputCode)
            ->where('maHV', $maHV)
            ->whereIn('trangThai', ['CREATED','SENT'])
            ->first();

        if (!$record) {
            return back()->withErrors(['code' => 'Mã không hợp lệ hoặc đã sử dụng.']);
        }

        if (!$record->isStillValid()) {
            return back()->withErrors(['code' => 'Mã đã hết hạn.']);
        }

        // Đổi trạng thái mã -> USED
        $record->update([
            'trangThai' => 'USED',
            'used_at'   => Carbon::now(),
        ]);

        // Tìm enrollment
        $enrollment = Enrollment::where('maHV', $record->maHV)
            ->where('maKH', $record->maKH)
            ->first();

        if ($enrollment) {
            // Lấy hạn dùng từ Course (VD cột thoiHanNgay trong KHOAHOC)
            $days = $enrollment->course->thoiHanNgay ?? null;
            $expireAt = $days
                ? Carbon::now()->addDays($days)
                : null;

            $enrollment->update([
                'trangThai'    => 'ACTIVE',
                'activated_at' => Carbon::now(),
                'expires_at'   => $expireAt,
            ]);
        }

        return redirect()->route('student.dashboard')
            ->with('success', 'Kích hoạt thành công! Bạn đã có quyền truy cập khóa học.');
    }
}
