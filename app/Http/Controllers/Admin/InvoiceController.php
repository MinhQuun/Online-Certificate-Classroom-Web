<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'search'         => trim((string) $request->query('search', '')),
            'date_from'      => $request->query('date_from'),
            'date_to'        => $request->query('date_to'),
            'payment_method' => $request->query('payment_method'),
            'amount_min'     => $request->query('amount_min'),
            'amount_max'     => $request->query('amount_max'),
        ];

        $query = Invoice::query()
            ->with([
                'student.user',
                'paymentMethod',
            ])
            ->withCount('items')
            ->orderByDesc('ngayLap')
            ->orderByDesc('maHD');

        $this->applyFilters($query, $filters);

        $paginationQuery = clone $query;
        $amountQuery     = clone $query;
        $countQuery      = clone $query;
        $invoices       = $paginationQuery->paginate(12)->withQueryString();
        $filteredAmount = (float) $amountQuery->sum('tongTien');
        $filteredCount  = (int) $countQuery->count();

        $breakdownQuery = Invoice::query();
        $this->applyFilters($breakdownQuery, $filters);

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

        $globalSummary = [
            'totalInvoices'  => Invoice::count(),
            'totalRevenue'   => (float) Invoice::sum('tongTien'),
            'monthlyRevenue' => (float) Invoice::whereBetween('ngayLap', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
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
        ]);
    }

    public function show(Invoice $invoice)
    {
        $invoice->loadMissing([
            'student.user',
            'paymentMethod',
            'items.course',
        ]);

        $relatedInvoices = Invoice::query()
            ->with(['paymentMethod'])
            ->where('maHV', $invoice->maHV)
            ->where('maHD', '!=', $invoice->maHD)
            ->orderByDesc('ngayLap')
            ->limit(5)
            ->get();

        return view('Admin.invoice-detail', [
            'invoice'         => $invoice,
            'relatedInvoices' => $relatedInvoices,
        ]);
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
