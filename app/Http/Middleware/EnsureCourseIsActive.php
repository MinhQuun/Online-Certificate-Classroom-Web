<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Student;

class EnsureCourseIsActive
{
    public function handle($request, Closure $next, $maKHParam = 'maKH')
    {
        $hocVienId = auth()->user()->hocVien->maHV;
        $maKH = $request->route($maKHParam); // ví dụ route('lesson.show', ['maKH'=>..., 'maBH'=>...])

        $enroll = Student::where('maHV', $hocVienId)
            ->where('maKH', $maKH)
            ->first();

        if (!$enroll || $enroll->trangThai !== 'ACTIVE') {
            return redirect('/kich-hoat')
                ->with('warning', 'Bạn cần kích hoạt khoá học trước.');
        }

        return $next($request);
    }
}
