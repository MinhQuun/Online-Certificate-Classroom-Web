<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    use LoadsTeacherContext;

    public function index(Request $request)
    {
        $teacherId = Auth::id() ?? 0;

        $courses = Course::query()
            ->where('maND', $teacherId)
            ->orderBy('tenKH')
            ->get(['maKH', 'tenKH', 'slug']);

        $selectedCourseId = (int) $request->query('course', 0);
        if ($courses->isNotEmpty()) {
            if (!$selectedCourseId || !$courses->contains('maKH', $selectedCourseId)) {
                $selectedCourseId = $courses->first()->maKH;
            }
        } else {
            $selectedCourseId = 0;
        }

        $filters = [
            'status' => strtoupper((string) $request->query('status', '')),
            'search' => trim((string) $request->query('search', '')),
        ];

        $entries = collect();
        $visibleRows = collect();
        $metrics = [
            'total'   => 0,
            'issued'  => 0,
            'pending' => 0,
            'revoked' => 0,
            'none'    => 0,
        ];

        if ($selectedCourseId) {
            $enrollments = Enrollment::query()
                ->with(['student.user'])
                ->where('maKH', $selectedCourseId)
                ->orderBy('created_at', 'desc')
                ->get();

            $certificates = Certificate::query()
                ->where('maKH', $selectedCourseId)
                ->where('loaiCC', Certificate::TYPE_COURSE)
                ->orderByDesc('issued_at')
                ->get()
                ->groupBy('maHV');

            $entries = $enrollments->map(function (Enrollment $enrollment) use ($certificates) {
                $latestCertificate = $certificates->get($enrollment->maHV)?->first();
                $status = $latestCertificate?->trangThai ?? 'NONE';

                return [
                    'enrollment' => $enrollment,
                    'student'    => $enrollment->student,
                    'email'      => $enrollment->student?->user?->email,
                    'progress'   => (int) ($enrollment->progress_percent ?? 0),
                    'certificate'=> $latestCertificate,
                    'status'     => $status,
                ];
            });

            $metrics = [
                'total'   => $entries->count(),
                'issued'  => $entries->where('status', Certificate::STATUS_ISSUED)->count(),
                'pending' => $entries->where('status', Certificate::STATUS_PENDING)->count(),
                'revoked' => $entries->where('status', Certificate::STATUS_REVOKED)->count(),
                'none'    => $entries->where('status', 'NONE')->count(),
            ];

            $visibleRows = $entries;

            if ($filters['status'] === 'NONE') {
                $visibleRows = $visibleRows->where('status', 'NONE');
            } elseif (in_array($filters['status'], [
                Certificate::STATUS_PENDING,
                Certificate::STATUS_ISSUED,
                Certificate::STATUS_REVOKED,
            ], true)) {
                $visibleRows = $visibleRows->where('status', $filters['status']);
            }

            if ($filters['search'] !== '') {
                $keyword = mb_strtolower($filters['search']);
                $visibleRows = $visibleRows->filter(function ($row) use ($keyword) {
                    $name = mb_strtolower($row['student']?->hoTen ?? '');
                    $email = mb_strtolower($row['email'] ?? '');

                    return str_contains($name, $keyword) || str_contains($email, $keyword);
                });
            }

            $visibleRows = $visibleRows->values();
        }

        return view('Teacher.certificates', [
            'courses'           => $courses,
            'activeCourseId'    => $selectedCourseId,
            'rows'              => $visibleRows,
            'filters'           => $filters,
            'metrics'           => $metrics,
            'statusLabels'      => $this->statusLabels(),
            'badges'            => $this->teacherSidebarBadges($teacherId),
        ]);
    }

    protected function statusLabels(): array
    {
        return [
            'NONE'                     => 'Chưa cấp',
            Certificate::STATUS_PENDING => 'Đang xét',
            Certificate::STATUS_ISSUED  => 'Đã cấp',
            Certificate::STATUS_REVOKED => 'Đã thu hồi',
        ];
    }
}
