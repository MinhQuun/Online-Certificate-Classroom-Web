<?php

namespace App\Http\Controllers\API\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user()->loadMissing('student');
        if (!$user->student) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Không tìm thấy hồ sơ học viên.',
            ], 403);
        }

        $filters = [
            'status' => strtoupper((string) $request->query('status', '')),
            'search' => trim((string) $request->query('q', '')),
        ];

        $perPage = (int) $request->query('per_page', 10);
        $perPage = max(5, min(30, $perPage));

        $query = Certificate::query()
            ->with(['course'])
            ->where('maHV', $user->student->maHV);

        if (in_array($filters['status'], [
            Certificate::STATUS_PENDING,
            Certificate::STATUS_ISSUED,
            Certificate::STATUS_REVOKED,
        ], true)) {
            $query->where('trangThai', $filters['status']);
        }

        if ($filters['search'] !== '') {
            $keyword = '%' . $filters['search'] . '%';
            $query->where(function ($builder) use ($keyword) {
                $builder->where('code', 'like', $keyword)
                    ->orWhere('tenCC', 'like', $keyword)
                    ->orWhereHas('course', fn ($sub) => $sub->where('tenKH', 'like', $keyword));
            });
        }

        $paginator = $query
            ->orderByDesc('issued_at')
            ->orderByDesc('maCC')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'status'  => 'success',
            'message' => 'Lấy danh sách chứng chỉ thành công.',
            'data'    => [
                'certificates' => array_map([$this, 'transformCertificate'], $paginator->items()),
                'pagination'   => [
                    'total'        => $paginator->total(),
                    'per_page'     => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'last_page'    => $paginator->lastPage(),
                ],
            ],
        ]);
    }

    public function show(Request $request, Certificate $certificate): JsonResponse
    {
        $user = $request->user()->loadMissing('student');
        if (!$user->student || $certificate->maHV !== $user->student->maHV) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Không tìm thấy chứng chỉ phù hợp.',
            ], 404);
        }

        $certificate->loadMissing(['course']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Lấy chứng chỉ thành công.',
            'data'    => $this->transformCertificate($certificate, true),
        ]);
    }

    public function download(Request $request, Certificate $certificate): JsonResponse
    {
        $user = $request->user()->loadMissing('student');
        if (!$user->student || $certificate->maHV !== $user->student->maHV) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Không tìm thấy chứng chỉ phù hợp.',
            ], 404);
        }

        if ($certificate->trangThai !== Certificate::STATUS_ISSUED || !$certificate->pdf_url) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Chứng chỉ chưa sẵn sàng để tải xuống.',
            ], 422);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Lấy đường dẫn tải chứng chỉ thành công.',
            'data'    => [
                'download_url' => $certificate->pdf_url,
                'code'         => $certificate->code,
            ],
        ]);
    }

    protected function transformCertificate(Certificate $certificate, bool $withMeta = false): array
    {
        $payload = [
            'id'            => $certificate->maCC,
            'code'          => $certificate->code,
            'title'         => $certificate->tenCC,
            'type'          => $certificate->loaiCC,
            'status'        => $certificate->trangThai,
            'issued_at'     => optional($certificate->issued_at)->toIso8601String(),
            'revoked_at'    => optional($certificate->revoked_at)->toIso8601String(),
            'description'   => $certificate->moTa,
            'course'        => $certificate->course
                ? [
                    'id'   => $certificate->course->maKH,
                    'name' => $certificate->course->tenKH,
                    'slug' => $certificate->course->slug,
                ]
                : null,
            'can_download'  => $certificate->trangThai === Certificate::STATUS_ISSUED && (bool) $certificate->pdf_url,
            'download_url'  => $certificate->trangThai === Certificate::STATUS_ISSUED ? $certificate->pdf_url : null,
        ];

        if ($withMeta) {
            $payload['issue_mode'] = $certificate->issue_mode;
            $payload['revoked_reason'] = $certificate->revoked_reason;
        }

        return $payload;
    }
}
