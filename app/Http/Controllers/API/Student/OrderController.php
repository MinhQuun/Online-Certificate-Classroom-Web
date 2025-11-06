<?php

namespace App\Http\Controllers\API\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user()->loadMissing('student');

        if (! $user->student) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tài khoản chưa có hồ sơ học viên.',
            ], 403);
        }

        $perPage = (int) $request->query('per_page', 10);
        $perPage = max(1, min($perPage, 25));

        $invoices = Invoice::query()
            ->with([
                'items.course' => function ($query) {
                    $query->select('maKH', 'tenKH', 'slug', 'hocPhi');
                },
                'paymentMethod',
            ])
            ->where('maHV', $user->student->maHV)
            ->orderByDesc('ngayLap')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $items = $invoices->getCollection()->map(function (Invoice $invoice) use ($user) {
            $courses = $invoice->items->map(function ($item) use ($user) {
                $enrollment = null;

                if ($item->course) {
                    $enrollment = Enrollment::query()
                        ->select('maHV', 'maKH', 'trangThai', 'activated_at', 'expires_at', 'progress_percent')
                        ->where('maHV', $user->student->maHV)
                        ->where('maKH', $item->course->maKH)
                        ->first();
                }

                $quantity = (int) ($item->soLuong ?? 1);
                $unit = (int) $item->donGia;

                return [
                    'course_id'    => $item->course?->maKH,
                    'course_title' => $item->course?->tenKH,
                    'quantity'     => $quantity,
                    'unit_price'   => $unit,
                    'total_price'  => $unit * $quantity,
                    'enrollment'   => $enrollment ? [
                        'status'           => $enrollment->trangThai,
                        'activated_at'     => optional($enrollment->activated_at)->toIso8601String(),
                        'expires_at'       => optional($enrollment->expires_at)->toIso8601String(),
                        'progress_percent' => (int) ($enrollment->progress_percent ?? 0),
                    ] : null,
                ];
            })->values();

            return [
                'id'            => $invoice->maHD,
                'status'        => $invoice->trangThai,
                'type'          => $invoice->loai,
                'total_amount'  => (int) $invoice->tongTien,
                'note'          => $invoice->ghiChu,
                'issued_at'     => optional($invoice->ngayLap ?? $invoice->created_at)->toIso8601String(),
                'payment_method'=> $invoice->paymentMethod ? [
                    'id'   => $invoice->paymentMethod->maTT,
                    'name' => $invoice->paymentMethod->tenTT ?? null,
                ] : null,
                'items'         => $courses,
            ];
        })->values();

        $summary = [
            'total_orders'           => Invoice::where('maHV', $user->student->maHV)->count(),
            'total_activated_amount' => $this->calculateActivatedAmount($user->student->maHV),
        ];

        return response()->json([
            'status'  => 'success',
            'message' => 'Lấy lịch sử đơn hàng thành công.',
            'data'    => [
                'items' => $items,
                'pagination' => [
                    'current_page' => $invoices->currentPage(),
                    'per_page'     => $invoices->perPage(),
                    'total'        => $invoices->total(),
                    'last_page'    => $invoices->lastPage(),
                ],
                'summary' => $summary,
            ],
        ]);
    }

    protected function calculateActivatedAmount(int $studentId): int
    {
        $enrollments = Enrollment::query()
            ->select('maKH')
            ->where('maHV', $studentId)
            ->where('trangThai', 'ACTIVE')
            ->get();

        if ($enrollments->isEmpty()) {
            return 0;
        }

        $courseIds = $enrollments->pluck('maKH');

        return (int) Invoice::query()
            ->join('CTHD', 'CTHD.maHD', '=', 'HOADON.maHD')
            ->where('HOADON.maHV', $studentId)
            ->whereIn('CTHD.maKH', $courseIds)
            ->sum(DB::raw('CTHD.donGia * IFNULL(CTHD.soLuong,1)'));
    }
}
