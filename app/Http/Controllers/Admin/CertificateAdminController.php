<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Services\CertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class CertificateAdminController extends Controller
{
    public function __construct(
        private readonly CertificateService $certificateService
    ) {
    }

    public function index(Request $request)
    {
        $filters = [
            'status'     => strtoupper((string) $request->query('status', '')),
            'issue_mode' => strtoupper((string) $request->query('issue_mode', '')),
            'search'     => trim((string) $request->query('search', '')),
        ];

        $certificatesQuery = Certificate::query()
            ->with(['student.user', 'course'])
            ->when(in_array($filters['status'], [
                Certificate::STATUS_PENDING,
                Certificate::STATUS_ISSUED,
                Certificate::STATUS_REVOKED,
            ], true), function ($query) use ($filters) {
                $query->where('trangThai', $filters['status']);
            })
            ->when(in_array($filters['issue_mode'], [
                Certificate::ISSUE_MODE_AUTO,
                Certificate::ISSUE_MODE_MANUAL,
            ], true), function ($query) use ($filters) {
                $query->where('issue_mode', $filters['issue_mode']);
            })
            ->when($filters['search'] !== '', function ($query) use ($filters) {
                $keyword = '%' . $filters['search'] . '%';
                $query->where(function ($builder) use ($keyword) {
                    $builder->where('code', 'like', $keyword)
                        ->orWhere('tenCC', 'like', $keyword)
                        ->orWhereHas('student', fn ($sub) => $sub->where('hoTen', 'like', $keyword))
                        ->orWhereHas('student.user', fn ($sub) => $sub->where('email', 'like', $keyword))
                        ->orWhereHas('course', fn ($sub) => $sub->where('tenKH', 'like', $keyword));
                });
            })
            ->orderByDesc('issued_at')
            ->orderByDesc('maCC');

        $certificates = $certificatesQuery->paginate(15)->withQueryString();

        $stats = [
            'total'        => Certificate::count(),
            'issued'       => Certificate::where('trangThai', Certificate::STATUS_ISSUED)->count(),
            'revoked'      => Certificate::where('trangThai', Certificate::STATUS_REVOKED)->count(),
            'auto'         => Certificate::where('issue_mode', Certificate::ISSUE_MODE_AUTO)->count(),
            'manual'       => Certificate::where('issue_mode', Certificate::ISSUE_MODE_MANUAL)->count(),
            'todayIssued'  => Certificate::whereDate('issued_at', now())->count(),
        ];

        $coursePolicies = Course::query()
            ->select(['maKH', 'tenKH', 'slug', 'certificate_enabled', 'certificate_progress_required'])
            ->orderBy('tenKH')
            ->get();

        $templates = CertificateTemplate::query()
            ->with(['course', 'creator'])
            ->orderByDesc('updated_at')
            ->get();

        $statusLabels = [
            Certificate::STATUS_PENDING => 'Chờ xử lý',
            Certificate::STATUS_ISSUED  => 'Đã cấp',
            Certificate::STATUS_REVOKED => 'Đã thu hồi',
        ];

        $issueModeLabels = [
            Certificate::ISSUE_MODE_AUTO   => 'Tự động',
            Certificate::ISSUE_MODE_MANUAL => 'Thủ công',
        ];

        $templateStatuses = [
            CertificateTemplate::STATUS_ACTIVE   => 'Đang dùng',
            CertificateTemplate::STATUS_DRAFT    => 'Bản nháp',
            CertificateTemplate::STATUS_ARCHIVED => 'Đã lưu trữ',
        ];

        return view('Admin.certificates', [
            'certificates'     => $certificates,
            'filters'          => $filters,
            'stats'            => $stats,
            'statusLabels'     => $statusLabels,
            'issueModeLabels'  => $issueModeLabels,
            'coursePolicies'   => $coursePolicies,
            'templates'        => $templates,
            'templateStatuses' => $templateStatuses,
        ]);
    }

    public function storeManual(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id'  => ['required', 'integer', 'exists:hocvien,maHV'],
            'course_id'   => ['required', 'integer', 'exists:khoahoc,maKH'],
            'issued_at'   => ['nullable', 'date'],
            'title'       => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $admin = $request->user();
        $student = Student::with('user')->findOrFail($data['student_id']);

        try {
            $course = Course::findOrFail($data['course_id']);
            $this->certificateService->issueManualCourseCertificate($student, $course, $admin, [
                'issued_at'   => $data['issued_at'] ?? null,
                'title'       => $data['title'] ?? null,
                'description' => $data['description'] ?? null,
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withErrors(['manual_issue' => 'Không thể cấp chứng chỉ. Vui lòng thử lại.'])
                ->withInput();
        }

        return redirect()
            ->route('admin.certificates.index')
            ->with('success', 'Đã cấp chứng chỉ thủ công thành công.');
    }

    public function revoke(Request $request, Certificate $certificate): RedirectResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:240'],
        ]);

        if ($certificate->trangThai === Certificate::STATUS_REVOKED) {
            return back()->with('status', 'Chứng chỉ này đã bị thu hồi trước đó.');
        }

        $this->certificateService->revokeCertificate($certificate, $request->user(), $data['reason']);

        return redirect()
            ->route('admin.certificates.index')
            ->with('success', 'Đã thu hồi chứng chỉ #' . $certificate->code);
    }

    public function updateCoursePolicy(Request $request, Course $course): RedirectResponse
    {
        $data = $request->validate([
            'certificate_enabled'            => ['required', 'boolean'],
            'certificate_progress_required'  => ['required', 'integer', 'between:0,100'],
        ]);

        $course->forceFill([
            'certificate_enabled'           => $data['certificate_enabled'] ? 1 : 0,
            'certificate_progress_required' => (int) $data['certificate_progress_required'],
        ])->save();

        $course->refresh();

        if ($course->certificate_enabled) {
            Enrollment::query()
                ->where('maKH', $course->maKH)
                ->where('progress_percent', '>=', $course->certificateProgressThreshold())
                ->orderBy('maHV')
                ->chunk(100, function ($enrollments) {
                    foreach ($enrollments as $enrollment) {
                        $this->certificateService->issueCourseCertificateIfEligible($enrollment);
                    }
                });
        }

        return back()->with('success', 'Đã cập nhật cấu hình chứng chỉ cho khóa ' . $course->tenKH);
    }

    public function storeTemplate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tenTemplate'   => ['required', 'string', 'max:150'],
            'maKH'          => ['nullable', 'integer', 'exists:khoahoc,maKH'],
            'template_url'  => ['nullable', 'string', 'max:700'],
            'design_json'   => ['nullable', 'string'],
            'moTa'          => ['nullable', 'string', 'max:500'],
            'trangThai'     => ['required', Rule::in([
                CertificateTemplate::STATUS_ACTIVE,
                CertificateTemplate::STATUS_DRAFT,
                CertificateTemplate::STATUS_ARCHIVED,
            ])],
        ]);

        $designPayload = $this->decodeDesignJson($data['design_json'] ?? null);

        CertificateTemplate::create([
            'tenTemplate'  => $data['tenTemplate'],
            'loaiTemplate' => CertificateTemplate::TYPE_COURSE,
            'maKH'         => $data['maKH'],
            'maGoi'        => null,
            'template_url' => $data['template_url'],
            'design_json'  => $designPayload,
            'moTa'         => $data['moTa'] ?? null,
            'trangThai'    => $data['trangThai'],
            'created_by'   => $request->user()->getKey(),
        ]);

        return back()->with('success', 'Đã thêm mẫu chứng chỉ mới.');
    }

    public function updateTemplate(Request $request, CertificateTemplate $template): RedirectResponse
    {
        $data = $request->validate([
            'tenTemplate'   => ['required', 'string', 'max:150'],
            'maKH'          => ['nullable', 'integer', 'exists:khoahoc,maKH'],
            'template_url'  => ['nullable', 'string', 'max:700'],
            'design_json'   => ['nullable', 'string'],
            'moTa'          => ['nullable', 'string', 'max:500'],
            'trangThai'     => ['required', Rule::in([
                CertificateTemplate::STATUS_ACTIVE,
                CertificateTemplate::STATUS_DRAFT,
                CertificateTemplate::STATUS_ARCHIVED,
            ])],
        ]);

        $designPayload = $this->decodeDesignJson($data['design_json'] ?? null);

        $template->forceFill([
            'tenTemplate'  => $data['tenTemplate'],
            'loaiTemplate' => CertificateTemplate::TYPE_COURSE,
            'maKH'         => $data['maKH'],
            'maGoi'        => null,
            'template_url' => $data['template_url'],
            'design_json'  => $designPayload,
            'moTa'         => $data['moTa'] ?? null,
            'trangThai'    => $data['trangThai'],
        ])->save();

        return back()->with('success', 'Đã cập nhật mẫu chứng chỉ.');
    }

    public function searchStudents(Request $request): JsonResponse
    {
        $keyword = trim((string) $request->query('q', ''));

        if (mb_strlen($keyword) < 2) {
            return response()->json(['data' => []]);
        }

        $students = Student::query()
            ->with('user')
            ->where(function ($query) use ($keyword) {
                $query->where('hoTen', 'like', "%{$keyword}%")
                    ->orWhereHas('user', fn ($sub) => $sub->where('email', 'like', "%{$keyword}%"));
            })
            ->orderBy('hoTen')
            ->limit(10)
            ->get();

        return response()->json([
            'data' => $students->map(function (Student $student) {
                return [
                    'id'    => $student->maHV,
                    'label' => $student->hoTen ?? $student->user?->hoTen ?? 'Học viên #' . $student->maHV,
                    'email' => $student->user?->email,
                ];
            }),
        ]);
    }

    public function searchCourses(Request $request): JsonResponse
    {
        $keyword = trim((string) $request->query('q', ''));

        if (mb_strlen($keyword) < 2) {
            return response()->json(['data' => []]);
        }

        $courses = Course::query()
            ->select(['maKH', 'tenKH', 'slug'])
            ->where(function ($query) use ($keyword) {
                $query->where('tenKH', 'like', "%{$keyword}%")
                    ->orWhere('slug', 'like', "%{$keyword}%");
            })
            ->orderBy('tenKH')
            ->limit(10)
            ->get();

        return response()->json([
            'data' => $courses->map(function (Course $course) {
                return [
                    'id'    => $course->maKH,
                    'label' => $course->tenKH,
                    'slug'  => $course->slug,
                ];
            }),
        ]);
    }

    protected function decodeDesignJson(?string $json): ?array
    {
        if ($json === null || trim($json) === '') {
            return null;
        }

        $decoded = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw ValidationException::withMessages([
                'design_json' => 'Thiết lập JSON không hợp lệ: ' . json_last_error_msg(),
            ]);
        }

        return $decoded;
    }
}
