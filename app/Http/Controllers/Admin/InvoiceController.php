<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->extractFilters($request);
        $perPage = 10;

        $baseQuery = $this->baseQuery();
        $this->applyFilters($baseQuery, $filters);

        $paginatedQuery = clone $baseQuery;
        $sumQuery       = clone $baseQuery;
        $countQuery     = clone $baseQuery;
        $breakdownQuery = clone $baseQuery;

        // DANH SÁCH HÓA ĐƠN: Sắp xếp theo ngày lập và mã hóa đơn
        $invoices = $paginatedQuery
            ->orderByDesc('ngayLap')
            ->orderByDesc('maHD')
            ->paginate($perPage)
            ->withQueryString();

        $filteredAmount = (float) $sumQuery->sum('tongTien');
        $filteredCount  = (int) $countQuery->count();

        // THỐNG KÊ THEO PHƯƠNG THỨC THANH TOÁN
        $methodBreakdown = $breakdownQuery
            ->select('maTT')
            ->selectRaw('COUNT(*) as total_invoices')
            ->selectRaw('SUM(tongTien) as total_amount')
            ->groupBy('maTT')
            ->orderByDesc('total_amount')
            ->get()
            ->map(function ($row) {
                return [
                    'code'   => $row->maTT,
                    'count'  => (int) $row->total_invoices,
                    'amount' => (float) $row->total_amount,
                ];
            });

        $now = Carbon::now();

        $globalSummary = [
            'totalInvoices'  => Invoice::count(),
            'totalRevenue'   => (float) Invoice::sum('tongTien'),
            'monthlyRevenue' => (float) Invoice::whereBetween('ngayLap', [
                $now->copy()->startOfMonth(),
                $now->copy()->endOfMonth(),
            ])->sum('tongTien'),
        ];

        $paymentMethods = PaymentMethod::query()
            ->orderBy('tenPhuongThuc')
            ->get();

        return view('Admin.invoices', [
            'invoices'        => $invoices,
            'filters'         => $filters,
            'paymentMethods'  => $paymentMethods,
            'filteredAmount'  => $filteredAmount,
            'filteredCount'   => $filteredCount,
            'methodBreakdown' => $methodBreakdown,
            'globalSummary'   => $globalSummary,
            'perPage'         => $perPage,
        ]);
    }

        public function show($id)
    {
        $invoice = Invoice::with([
                'student.user',
                'paymentMethod',
                'items.course',
                'comboItems.combo.courses',
                'comboItems.promotion',
            ])
            ->findOrFail($id);

        $lineItems = $this->prepareInvoiceLineItems($invoice);

        $student = $invoice->student;
        $relatedInvoices = Invoice::where('maHV', $student?->maHV)
            ->where('maHD', '!=', $invoice->maHD)
            ->take(5)
            ->get();

        $issuedAt = null;
        if ($invoice->ngayLap) {
            try {
                $issuedAt = Carbon::parse($invoice->ngayLap);
            } catch (\Throwable $e) {
                $issuedAt = null;
            }
        }

        return response()->json([
            'invoice' => [
                'id' => $invoice->maHD,
                'total_amount_text' => number_format($invoice->tongTien) . ' VND',
                'payment_method' => $invoice->paymentMethod?->tenPhuongThuc ?? 'N/A',
                'processor' => $invoice->nguoiXuLy ?? 'Hệ thống',
                'items_total_quantity' => $lineItems['total_quantity'],
                'items_total_text' => number_format($lineItems['total_amount']) . ' VND',
                'product_breakdown' => [
                    'courses' => $lineItems['course_quantity'],
                    'combos' => $lineItems['combo_quantity'],
                ],
                'issued_at' => [
                    'full' => $issuedAt ? $issuedAt->format('d/m/Y H:i') : null,
                    'date' => $issuedAt ? $issuedAt->format('d/m/Y') : null,
                    'time' => $issuedAt ? $issuedAt->format('H:i') : null,
                ],
                'items' => $lineItems['items']->map(function (array $item) {
                    return [
                        'type' => $item['type'],
                        'type_label' => $item['type_label'],
                        'product_id' => $item['product_id'],
                        'product_name' => $item['product_name'],
                        'quantity' => $item['quantity'],
                        'unit_price_text' => $item['unit_price_text'],
                        'line_total_text' => $item['line_total_text'],
                        'courses' => $item['courses'],
                        'promotion_code' => $item['promotion_code'],
                        'promotion_name' => $item['promotion_name'],
                    ];
                })->values(),
                'note' => $invoice->ghiChu ?? 'Không có',
            ],
            'student' => [
                'student_id' => $student?->maHV,
                'name' => $student?->hoTen ?? 'N/A',
                'email' => $student?->user?->email ?? '',
                'phone' => $student?->soDT ?? '',
            ],
            'related_invoices' => $relatedInvoices->map(function ($r) {
                $rIssued = null;
                if ($r->ngayLap) {
                    try {
                        $rIssued = Carbon::parse($r->ngayLap);
                    } catch (\Throwable $e) {
                        $rIssued = null;
                    }
                }

                return [
                    'id' => $r->maHD,
                    'issued_at' => $rIssued ? $rIssued->format('d/m/Y') : null,
                    'method' => optional($r->paymentMethod)->tenPhuongThuc ?? 'N/A',
                    'amount_text' => number_format($r->tongTien) . ' VND',
                ];
            })->values(),
            'pdf_url' => route('admin.invoices.pdf', $invoice->maHD),
        ]);
    }


        public function exportPdf(Invoice $invoice)
    {
        $invoice->loadMissing([
            'student.user',
            'paymentMethod',
            'items.course',
            'comboItems.combo.courses',
            'comboItems.promotion',
        ]);

        $issuedAt = $invoice->ngayLap
            ? Carbon::parse($invoice->ngayLap)
            : ($invoice->created_at ? Carbon::parse($invoice->created_at) : null);

        $lineItems = $this->prepareInvoiceLineItems($invoice);

        $data = [
            'invoice'       => $invoice,
            'student'       => $invoice->student,
            'user'          => $invoice->student?->user,
            'issuedAt'      => $issuedAt,
            'items'         => $lineItems['items']->values()->all(),
            'totalQuantity' => $lineItems['total_quantity'],
            'totalAmount'   => $lineItems['total_amount'],
            'breakdown'     => [
                'courses' => $lineItems['course_quantity'],
                'combos'  => $lineItems['combo_quantity'],
            ],
        ];

        $pdf = Pdf::loadView('Admin.invoice-pdf', $data)->setPaper('a4');
        $fileName = 'hoa-don-' . $invoice->maHD . '.pdf';

        return $pdf->download($fileName);
    }

    public function exportExcel(Request $request)
    {
        $filters = $this->extractFilters($request);

        $query = $this->baseQuery();
        $this->applyFilters($query, $filters);

        $invoices = $query->get();

        if ($invoices->isEmpty()) {
            $queryString = array_filter($filters, function ($value) {
                return $value !== null && $value !== '';
            });

            return redirect()
                ->route('admin.invoices.index', $queryString)
                ->with('warning', 'Không có hóa đơn nào để xuất Excel.');
        }

        $export   = new InvoiceExcelExport($invoices);
        $fileName = 'hoa-don-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download($export, $fileName);
    }

    /**
     * Query cơ bản: chỉ load quan hệ + đếm items
     * KHÔNG thêm orderBy vì nó sẽ dính lỗi GROUP BY
    */
        private function baseQuery(): Builder
    {
        return Invoice::query()
            ->with([
                'student.user',
                'paymentMethod',
                'items.course',
                'comboItems',
                'comboItems.combo',
            ])
            ->withCount('items')
            ->withCount(['comboItems as combo_items_count']);
    }

    private function extractFilters(Request $request): array
    {
        return [
            'search'         => trim((string) $request->query('search', '')),
            'date_from'      => $request->query('date_from'),
            'date_to'        => $request->query('date_to'),
            'payment_method' => $request->query('payment_method'),
            'amount_min'     => $request->query('amount_min'),
            'amount_max'     => $request->query('amount_max'),
        ];
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        if ($filters['search'] !== '') {
            $search = $filters['search'];

            $query->where(function (Builder $builder) use ($search) {
                if (ctype_digit($search)) {
                    $builder->orWhere('maHD', (int) $search)
                        ->orWhere('maHV', (int) $search);
                }

                $builder
                    ->orWhere('ghiChu', 'like', "%{$search}%")
                    ->orWhereHas('student', function (Builder $studentQuery) use ($search) {
                        $studentQuery->where('hoTen', 'like', "%{$search}%");
                    })
                    ->orWhereHas('student.user', function (Builder $userQuery) use ($search) {
                        $userQuery->where('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($filters['payment_method']) {
            $query->where('maTT', $filters['payment_method']);
        }

        if ($filters['date_from']) {
            try {
                $from = Carbon::parse($filters['date_from'])->startOfDay();
                $query->where('ngayLap', '>=', $from);
            } catch (\Throwable $e) {
                // ignore invalid input
            }
        }

        if ($filters['date_to']) {
            try {
                $to = Carbon::parse($filters['date_to'])->endOfDay();
                $query->where('ngayLap', '<=', $to);
            } catch (\Throwable $e) {
                // ignore invalid input
            }
        }

        if (($min = $this->normalizeCurrency($filters['amount_min'])) !== null) {
            $query->where('tongTien', '>=', $min);
        }

        if (($max = $this->normalizeCurrency($filters['amount_max'])) !== null) {
            $query->where('tongTien', '<=', $max);
        }
    }

    private function prepareInvoiceLineItems(Invoice $invoice): array
    {
        $invoice->loadMissing([
            'items.course',
            'comboItems.combo.courses',
            'comboItems.promotion',
        ]);

        $items = collect();
        $courseQuantity = 0;
        $comboQuantity = 0;

        foreach ($invoice->items as $item) {
            $quantity = (int) ($item->soLuong ?? 0);
            $unitPrice = (int) round((float) $item->donGia);
            $lineTotal = $quantity * $unitPrice;

            $items->push([
                'type' => 'course',
                'type_label' => 'Khóa học',
                'product_id' => $item->maKH,
                'product_name' => $item->course?->tenKH ?? ('Khóa học #' . $item->maKH),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'unit_price_text' => number_format($unitPrice) . ' VND',
                'line_total' => $lineTotal,
                'line_total_text' => number_format($lineTotal) . ' VND',
                'courses' => [],
                'promotion_code' => null,
                'promotion_name' => null,
            ]);

            $courseQuantity += $quantity;
        }

        foreach ($invoice->comboItems as $comboItem) {
            $quantity = (int) ($comboItem->soLuong ?? 0);
            $unitPrice = (int) round((float) $comboItem->donGia);
            $lineTotal = $quantity * $unitPrice;

            $courses = $comboItem->combo?->courses
                ? $comboItem->combo->courses->map(function ($course) {
                    return [
                        'id' => $course->maKH,
                        'name' => $course->tenKH,
                    ];
                })->values()->all()
                : [];

            $items->push([
                'type' => 'combo',
                'type_label' => 'Combo',
                'product_id' => $comboItem->maGoi,
                'product_name' => $comboItem->combo?->tenGoi ?? ('Combo #' . $comboItem->maGoi),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'unit_price_text' => number_format($unitPrice) . ' VND',
                'line_total' => $lineTotal,
                'line_total_text' => number_format($lineTotal) . ' VND',
                'courses' => $courses,
                'promotion_code' => $comboItem->maKM,
                'promotion_name' => $comboItem->promotion?->tenKM ?? null,
            ]);

            $comboQuantity += $quantity;
        }

        return [
            'items' => $items->values(),
            'total_quantity' => $courseQuantity + $comboQuantity,
            'total_amount' => (int) $items->sum('line_total'),
            'course_quantity' => $courseQuantity,
            'combo_quantity' => $comboQuantity,
        ];
    }

    private function normalizeCurrency(?string $value): ?float
    {
        if ($value === null) {
            return null;
        }

        $normalized = preg_replace('/[^\d.,-]/', '', $value);
        if ($normalized === null || $normalized === '') {
            return null;
        }

        $normalized = str_replace([',', ' '], '', $normalized);

        if (substr_count($normalized, '.') > 1) {
            $normalized = str_replace('.', '', $normalized);
        }

        if (!is_numeric($normalized)) {
            return null;
        }

        return (float) $normalized;
    }
}

/**
 * Export Excel: Hóa đơn theo bộ lọc
 */
final class InvoiceExcelExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    private Collection $invoices;

    public function __construct(Collection $invoices)
    {
        $this->invoices = $invoices;
    }

    public function collection(): Collection
    {
        return $this->invoices;
    }

            public function headings(): array
    {
        return [
            'Mã HD',
            'Mã học viên',
            'Tên học viên',
            'Email',
            'Phương thức thanh toán',
            'Tổng tiền',
            'Số sản phẩm',
            'Ngày lập',
        ];
    }

        public function map($invoice): array
    {
        $invoice->loadMissing(['student.user', 'items', 'comboItems']);

        $student = $invoice->student;
        $user    = $student?->user;
        $issuedAt = $invoice->ngayLap
            ? Carbon::parse($invoice->ngayLap)
            : ($invoice->created_at ? Carbon::parse($invoice->created_at) : null);

        $courseQuantity = (int) $invoice->items->sum('soLuong');
        $comboQuantity = (int) $invoice->comboItems->sum('soLuong');
        $totalProducts = $courseQuantity + $comboQuantity;

        return [
            $invoice->maHD,
            $invoice->maHV,
            $student?->hoTen ?? $user?->name ?? 'Chưa xác định',
            $user?->email ?? 'N/A',
            $invoice->paymentMethod->tenPhuongThuc ?? ($invoice->maTT ?: 'N/A'),
            (float) $invoice->tongTien,
            $totalProducts,
            $issuedAt ? $issuedAt->format('d/m/Y H:i') : 'N/A',
        ];
    }



}
