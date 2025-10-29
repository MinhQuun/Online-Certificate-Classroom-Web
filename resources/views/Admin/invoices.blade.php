@extends('layouts.admin')
@section('title', 'Quan ly hoa don')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Admin/admin-invoices.css') }}">
@endpush

@php
    $methodNames = $paymentMethods->pluck('tenPhuongThuc', 'maTT');
@endphp

@section('content')
    <section class="page-header">
        <span class="kicker">Admin</span>
        <h1 class="title">Quan ly hoa don</h1>
        <p class="muted">Theo doi cong no, loc giao dich va xem chi tiet hoa don.</p>
    </section>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="metric-card">
                <div class="metric-kicker">Tong hoa don</div>
                <div class="metric-value">{{ number_format($globalSummary['totalInvoices']) }}</div>
                <div class="metric-meta">He thong</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card">
                <div class="metric-kicker">Tong doanh thu</div>
                <div class="metric-value">{{ number_format($globalSummary['totalRevenue']) }}đ</div>
                <div class="metric-meta">Tat ca don hang</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card">
                <div class="metric-kicker">Doanh thu thang nay</div>
                <div class="metric-value">{{ number_format($globalSummary['monthlyRevenue']) }}đ</div>
                <div class="metric-meta">{{ \Illuminate\Support\Carbon::now()->format('m/Y') }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3 align-items-stretch mb-3">
        <div class="col-lg-9">
            <div class="card invoices-filter">
                <div class="card-body">
                    <form class="row g-3" method="get" action="{{ route('admin.invoices.index') }}">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label text-uppercase small text-muted mb-1">Tu khoa</label>
                            <input type="text" name="search" class="form-control"
                                value="{{ $filters['search'] }}"
                                placeholder="Ma HD, ten hoc vien, email...">
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label text-uppercase small text-muted mb-1">Tu ngay</label>
                            <input type="date" name="date_from" class="form-control"
                                value="{{ $filters['date_from'] }}">
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label text-uppercase small text-muted mb-1">Den ngay</label>
                            <input type="date" name="date_to" class="form-control"
                                value="{{ $filters['date_to'] }}">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label text-uppercase small text-muted mb-1">Phuong thuc</label>
                            <select name="payment_method" class="form-select">
                                <option value="">Tat ca</option>
                                @foreach ($paymentMethods as $method)
                                    <option value="{{ $method->maTT }}" {{ $filters['payment_method'] === $method->maTT ? 'selected' : '' }}>
                                        {{ $method->tenPhuongThuc }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label text-uppercase small text-muted mb-1">Tong tien tu</label>
                            <input type="number" name="amount_min" class="form-control"
                                step="0.01" min="0"
                                value="{{ $filters['amount_min'] }}">
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label text-uppercase small text-muted mb-1">Tong tien den</label>
                            <input type="number" name="amount_max" class="form-control"
                                step="0.01" min="0"
                                value="{{ $filters['amount_max'] }}">
                        </div>
                        <div class="col-lg-6 d-flex align-items-end justify-content-end gap-2">
                            <button class="btn btn-primary">
                                <i class="bi bi-funnel me-1"></i> Loc ket qua
                            </button>
                            <a class="btn btn-outline-secondary" href="{{ route('admin.invoices.index') }}">
                                Xoa loc
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card filter-summary h-100">
                <div class="card-body d-flex flex-column gap-3">
                    <div>
                        <div class="summary-label text-uppercase">So hoa don theo loc</div>
                        <div class="summary-value">{{ number_format($filteredCount) }}</div>
                    </div>
                    <div>
                        <div class="summary-label text-uppercase">Tong tien theo loc</div>
                        <div class="summary-value text-primary">{{ number_format($filteredAmount) }}đ</div>
                    </div>
                    <div>
                        <div class="summary-label text-uppercase">Top phuong thuc</div>
                        <div class="d-flex flex-wrap gap-2">
                            @forelse ($methodBreakdown as $entry)
                                @php
                                    $label = $methodNames[$entry['code']] ?? ($entry['code'] ?: 'Khac');
                                @endphp
                                <span class="method-chip">
                                    <span class="txt">{{ $label }}</span>
                                    <span class="amt">{{ number_format($entry['amount']) }}đ</span>
                                </span>
                            @empty
                                <span class="text-muted small">Khong co du lieu</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="m-0">Danh sach hoa don</h5>
            <span class="text-muted small">
                Hien thi {{ $invoices->firstItem() ?? 0 }} - {{ $invoices->lastItem() ?? 0 }} / {{ $invoices->total() }}
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-align-middle invoice-table mb-0">
                <thead>
                    <tr>
                        <th>Ma HD</th>
                        <th>Hoc vien</th>
                        <th>Email</th>
                        <th>Phuong thuc</th>
                        <th class="text-end">Tong tien</th>
                        <th class="text-center">So khoa</th>
                        <th>Ngay lap</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $invoice)
                        @php
                            $student = $invoice->student;
                            $user = $student?->user;
                            $studentName = $student?->hoTen ?? $user?->name ?? 'Khong ro';
                            $email = $user?->email ?? 'N/A';
                            $methodLabel = optional($invoice->paymentMethod)->tenPhuongThuc ?? ($invoice->maTT ?: 'N/A');
                            $issuedAt = $invoice->ngayLap
                                ? \Illuminate\Support\Carbon::parse($invoice->ngayLap)
                                : ($invoice->created_at ? \Illuminate\Support\Carbon::parse($invoice->created_at) : null);
                        @endphp
                        <tr class="invoice-row" data-href="{{ route('admin.invoices.show', $invoice) }}">
                            <td><strong>#{{ $invoice->maHD }}</strong></td>
                            <td>
                                <div class="fw-semibold text-truncate" title="{{ $studentName }}">{{ $studentName }}</div>
                                @if ($student)
                                    <div class="text-muted small">HV#{{ $student->maHV }}</div>
                                @endif
                            </td>
                            <td class="text-truncate" title="{{ $email }}">{{ $email }}</td>
                            <td><span class="badge rounded-pill text-bg-light">{{ $methodLabel }}</span></td>
                            <td class="text-end fw-semibold text-primary">{{ number_format($invoice->tongTien) }}đ</td>
                            <td class="text-center">
                                <span class="badge rounded-pill text-bg-light">
                                    {{ $invoice->items_count }}
                                </span>
                            </td>
                            <td>
                                @if ($issuedAt)
                                    <div>{{ $issuedAt->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ $issuedAt->format('H:i') }}</div>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.invoices.show', $invoice) }}">
                                    <i class="bi bi-eye me-1"></i> Chi tiet
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                Khong tim thay hoa don phu hop.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($invoices->hasPages())
            <div class="card-footer">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/Admin/admin-invoices.js') }}" defer></script>
@endpush
