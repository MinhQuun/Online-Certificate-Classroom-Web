<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(Request $request): View
    {
        $student = $this->resolveStudent();

        $filters = [
            'status' => strtoupper((string) $request->query('status', '')),
            'type'   => strtoupper((string) $request->query('type', '')),
            'search' => trim((string) $request->query('q', '')),
        ];

        $query = Certificate::query()
            ->with(['course', 'combo'])
            ->where('maHV', $student->maHV);

        if (in_array($filters['status'], array_keys($this->statusLabels()), true)) {
            $query->where('trangThai', $filters['status']);
        }

        if (in_array($filters['type'], array_keys($this->typeLabels()), true)) {
            $query->where('loaiCC', $filters['type']);
        }

        if ($filters['search'] !== '') {
            $keyword = '%' . $filters['search'] . '%';
            $query->where(function ($builder) use ($keyword) {
                $builder->where('code', 'like', $keyword)
                    ->orWhere('tenCC', 'like', $keyword)
                    ->orWhereHas('course', fn ($sub) => $sub->where('tenKH', 'like', $keyword))
                    ->orWhereHas('combo', fn ($sub) => $sub->where('tenGoi', 'like', $keyword));
            });
        }

        $certificates = $query
            ->orderByDesc('issued_at')
            ->orderByDesc('maCC')
            ->paginate(9)
            ->withQueryString();

        $metrics = [
            'total'   => Certificate::where('maHV', $student->maHV)->count(),
            'issued'  => Certificate::where('maHV', $student->maHV)
                ->where('trangThai', Certificate::STATUS_ISSUED)
                ->count(),
            'pending' => Certificate::where('maHV', $student->maHV)
                ->where('trangThai', Certificate::STATUS_PENDING)
                ->count(),
            'revoked' => Certificate::where('maHV', $student->maHV)
                ->where('trangThai', Certificate::STATUS_REVOKED)
                ->count(),
        ];

        return view('Student.certificates', [
            'student'        => $student,
            'certificates'   => $certificates,
            'filters'        => $filters,
            'metrics'        => $metrics,
            'statusLabels'   => $this->statusLabels(),
            'statusBadges'   => $this->statusBadges(),
            'typeLabels'     => $this->typeLabels(),
        ]);
    }

    public function show(Certificate $certificate): View
    {
        $student = $this->resolveStudent();
        $this->ensureOwnership($certificate, $student);

        $certificate->loadMissing(['course', 'combo', 'student.user']);

        return view('Student.certificate-detail', [
            'student'      => $student,
            'certificate'  => $certificate,
            'statusLabels' => $this->statusLabels(),
            'statusBadges' => $this->statusBadges(),
            'typeLabels'   => $this->typeLabels(),
        ]);
    }

    public function download(Certificate $certificate): RedirectResponse
    {
        $student = $this->resolveStudent();
        $this->ensureOwnership($certificate, $student);

        if ($certificate->trangThai !== Certificate::STATUS_ISSUED || !$certificate->pdf_url) {
            return redirect()
                ->route('student.certificates.show', $certificate->maCC)
                ->with('error', 'Chứng chỉ chưa sẵn sàng để tải xuống.');
        }

        return redirect()->to($certificate->pdf_url);
    }

    protected function resolveStudent(): Student
    {
        $user = Auth::user();
        $student = $user?->student;

        if (!$student) {
            abort(403, 'Không tìm thấy hồ sơ học viên.');
        }

        return $student;
    }

    protected function ensureOwnership(Certificate $certificate, Student $student): void
    {
        if ($certificate->maHV !== $student->maHV) {
            abort(404);
        }
    }

    protected function statusLabels(): array
    {
        return [
            Certificate::STATUS_PENDING => 'Chờ xét',
            Certificate::STATUS_ISSUED  => 'Đã cấp',
            Certificate::STATUS_REVOKED => 'Đã thu hồi',
        ];
    }

    protected function statusBadges(): array
    {
        return [
            Certificate::STATUS_PENDING => 'status-pending',
            Certificate::STATUS_ISSUED  => 'status-issued',
            Certificate::STATUS_REVOKED => 'status-revoked',
        ];
    }

    protected function typeLabels(): array
    {
        return [
            Certificate::TYPE_COURSE => 'Khóa học',
            Certificate::TYPE_COMBO  => 'Combo',
        ];
    }
}
