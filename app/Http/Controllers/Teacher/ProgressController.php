<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Teacher\LoadsTeacherContext;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProgressController extends Controller
{
    use LoadsTeacherContext;

    public function index(Request $request)
    {
        $teacherId = Auth::id() ?? 0;

        $courses = Course::with(['chapters.lessons' => fn ($query) => $query->orderBy('thuTu')])
            ->where('maND', $teacherId)
            ->orderBy('tenKH')
            ->get();

        $activeCourseId = (int) $request->query('course');
        if ($activeCourseId && !$courses->contains('maKH', $activeCourseId)) {
            $activeCourseId = 0;
        }

        $activeCourse = $activeCourseId
            ? $courses->firstWhere('maKH', $activeCourseId)
            : $courses->first();

        $filters = [
            'status' => strtoupper($request->query('status', '')),
            'search' => trim($request->query('search', '')),
        ];

        $enrollments = collect();
        $metrics = [
            'total'     => 0,
            'average'   => 0,
            'completed' => 0,
            'active'    => 0,
            'at_risk'   => 0,
        ];

        if ($activeCourse) {
            $query = DB::table('HOCVIEN_KHOAHOC as hk')
                ->join('hocvien as hv', 'hk.maHV', '=', 'hv.maHV')
                ->leftJoin('nguoidung as nd', 'hv.maND', '=', 'nd.maND')
                ->leftJoin('baihoc as lessons', 'hk.last_lesson_id', '=', 'lessons.maBH')
                ->where('hk.maKH', $activeCourse->maKH);

            if ($filters['status'] && in_array($filters['status'], ['PENDING', 'ACTIVE', 'EXPIRED'], true)) {
                $query->where('hk.trangThai', $filters['status']);
            }

            if ($filters['search'] !== '') {
                $keyword = '%' . $filters['search'] . '%';
                $query->where(function ($builder) use ($keyword) {
                    $builder->where('hv.hoTen', 'like', $keyword)
                        ->orWhere('nd.email', 'like', $keyword);
                });
            }

            $enrollments = $query
                ->select([
                    'hk.maHV',
                    'hk.maKH',
                    'hk.progress_percent',
                    'hk.trangThai',
                    'hk.ngayNhapHoc',
                    'hk.updated_at',
                    'hv.hoTen as student_name',
                    'nd.email',
                    'lessons.tieuDe as last_lesson_title',
                    'lessons.thuTu as last_lesson_order',
                ])
                ->orderBy('hv.hoTen')
                ->get()
                ->map(function ($item) {
                    $item->joined_at = $item->ngayNhapHoc
                        ? Carbon::parse($item->ngayNhapHoc)->format('d/m/Y')
                        : null;

                    return $item;
                });

            $metrics['total'] = $enrollments->count();
            $metrics['average'] = $metrics['total'] > 0
                ? round($enrollments->avg('progress_percent'), 1)
                : 0;
            $metrics['completed'] = $enrollments->where('progress_percent', '>=', 100)->count();
            $metrics['active'] = $enrollments->where('trangThai', 'ACTIVE')->count();
            $metrics['at_risk'] = $enrollments->where('progress_percent', '<', 40)->count();
        }

        return view('Teacher.progress', [
            'courses'       => $courses,
            'activeCourse'  => $activeCourse,
            'enrollments'   => $enrollments,
            'filters'       => $filters,
            'metrics'       => $metrics,
            'statusLabels'  => [
                'PENDING' => 'Chá» kÃ­ch hoáº¡t',
                'ACTIVE'  => 'Äang há»c',
                'EXPIRED' => 'Háº¿t háº¡n',
            ],
            'badges'        => $this->teacherSidebarBadges($teacherId),
        ]);
    }

    public function update(Request $request, int $courseId, int $studentId): RedirectResponse
    {
        $teacherId = Auth::id() ?? 0;

        $course = Course::where('maKH', $courseId)
            ->where('maND', $teacherId)
            ->firstOrFail();

        $validated = $request->validate([
            'progress_percent' => ['required', 'integer', 'min:0', 'max:100'],
            'status'           => ['required', Rule::in(['PENDING', 'ACTIVE', 'EXPIRED'])],
            'last_lesson_id'   => ['nullable', 'integer'],
        ]);

        if (!empty($validated['last_lesson_id'])) {
            $lessonExists = Lesson::where('maBH', $validated['last_lesson_id'])
                ->whereHas('chapter', fn ($query) => $query->where('maKH', $course->maKH))
                ->exists();

            if (!$lessonExists) {
                return back()
                    ->withErrors(['last_lesson_id' => 'BÃ i há»c khÃ´ng thuá»™c khÃ³a nÃ y.'])
                    ->withInput();
            }
        }

        DB::table('hocvien_khoahoc')
            ->where('maKH', $course->maKH)
            ->where('maHV', $studentId)
            ->update([
                'progress_percent' => $validated['progress_percent'],
                'trangThai'        => $validated['status'],
                'last_lesson_id'   => $validated['last_lesson_id'] ?? null,
                'updated_at'       => now(),
            ]);

        return redirect()
            ->route('Teacher.progress', [
                'course' => $course->maKH,
            ])
            ->with('success', 'ÄÃ£ cáº­p nháº­t tiáº¿n Ä‘á»™ há»c táº­p.');
    }
}
