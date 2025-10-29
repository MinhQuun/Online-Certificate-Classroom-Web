@extends('layouts.admin')
@section('title', 'Quản lý hóa đơn')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Admin/admin-invoices.css') }}">
@endpush

@php
    $methodNames = $paymentMethods->pluck('tenPhuongThuc', 'maTT');
    $exportQuery = array_filter($filters, fn ($value) => $value !== null && $value !== '');
    $detailUrlTemplate = route('admin.invoices.show', ['invoice' => '__INVOICE__']);
    $pdfUrlTemplate = route('admin.invoices.pdf', ['invoice' => '__INVOICE__']);
@endphp

@section('content')
    <section class="page-header">
        <span class="kicker">Admin</span>
        <h1 class="title">Quản lý hóa đơn</h1>
        <p class="muted">Theo dõi công nợ, lọc giao dịch và xem chi tiết hóa đơn.</p>
    </section>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="metric-card">
                <div class="metric-kicker">Tổng hóa đơn</div>
                <div class="metric-value">{{ number_format($globalSummary['totalInvoices']) }}</div>
                <div class="metric-meta">Hệ thống</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card">
                <div class="metric-kicker">Tổng doanh thu</div>
                <div class="metric-value">{{ number_format($globalSummary['totalRevenue']) }} VND</div>
                <div class="metric-meta">Tất cả đơn hàng</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card">
                <div class="metric-kicker">Doanh thu tháng này</div>
                <div class="metric-value">{{ number_format($globalSummary['monthlyRevenue']) }} VND</div>
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
                            <label class="form-label text-uppercase small text-muted mb-1">Từ khóa</label>
                            <input type="text" name="search" class="form-control"
                                   value="{{ $filters['search'] }}"
                                   placeholder="Mã HĐ, tên học viên, email...">
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label text-uppercase small text-muted mb-1">Từ ngày</label>
                            <input type="date" name="date_from" class="form-control"
                                   value="{{ $filters['date_from'] }}">
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label text-uppercase small text-muted mb-1">Đến ngày</label>
                            <input type="date" name="date_to" class="form-control"
                                   value="{{ $filters['date_to'] }}">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label text-uppercase small text-muted mb-1">Phương thức</label>
                            <select name="payment_method" class="form-select">
                                <option value="">Tất cả</option>
                                @foreach ($paymentMethods as $method)
                                    <option value="{{ $method->maTT }}" {{ $filters['payment_method'] === $method->maTT ? 'selected' : '' }}>
                                        {{ $method->tenPhuongThuc }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label text-uppercase small text-muted mb-1">Tổng tiền từ</label>
                            <input type="number" name="amount_min" class="form-control"
                                   step="0.01" min="0"
                                   value="{{ $filters['amount_min'] }}">
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label text-uppercase small text-muted mb-1">Tổng tiền đến</label>
                            <input type="number" name="amount_max" class="form-control"
                                   step="0.01" min="0"
                                   value="{{ $filters['amount_max'] }}">
                        </div>
                        <div class="col-lg-6 d-flex align-items-end justify-content-end gap-2">
                            <button class="btn btn-primary">
                                <i class="bi bi-funnel me-1"></i> Lọc kết quả
                            </button>
                            <a class="btn btn-outline-secondary" href="{{ route('admin.invoices.index') }}">
                                Xóa lọc
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
                        <div class="summary-label text-uppercase">Số hóa đơn theo lọc</div>
                        <div class="summary-value">{{ number_format($filteredCount) }}</div>
                    </div>
                    <div>
                        <div class="summary-label text-uppercase">Tổng tiền theo lọc</div>
                        <div class="summary-value text-primary">{{ number_format($filteredAmount) }} VND</div>
                    </div>
                    <div>
                        <div class="summary-label text-uppercase">Top phương thức</div>
                        <div class="d-flex flex-wrap gap-2" data-method-breakdown>
                            @forelse ($methodBreakdown as $entry)
                                @php
                                    $label = $methodNames[$entry['code']] ?? ($entry['code'] ?: 'Khác');
                                @endphp
                                <span class="method-chip">
                                    <span class="txt">{{ $label }}</span>
                                    <span class="amt">{{ number_format($entry['amount']) }} VND</span>
                                </span>
                            @empty
                                <span class="text-muted small">Không có dữ liệu</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card invoice-list-card">
        <div class="card-header flex-wrap gap-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="m-0">Danh sách hóa đơn</h5>
                <span class="text-muted small">
                    Hiển thị {{ $invoices->firstItem() ?? 0 }} - {{ $invoices->lastItem() ?? 0 }} / {{ $invoices->total() }}
                </span>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a class="btn btn-outline-success"
                   href="{{ route('admin.invoices.export', $exportQuery) }}">
                    <i class="bi bi-file-earmark-excel me-1"></i> Xuất Excel
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table
                class="table table-hover table-align-middle invoice-table mb-0"
                data-detail-url="{{ $detailUrlTemplate }}"
                data-pdf-url="{{ $pdfUrlTemplate }}"
            >
                <thead>
                    <tr>
                        <th>Mã HĐ</th>
                        <th>Học viên</th>
                        <th>Email</th>
                        <th>Phương thức</th>
                        <th class="text-end">Tổng tiền</th>
                        <th class="text-center">Số khóa</th>
                        <th>Ngày lập</th>
                        <th class="text-end"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $invoice)
                        @php
                            $student = $invoice->student;
                            $user = $student?->user;
                            $studentName = $student?->hoTen ?? $user?->name ?? 'Không rõ';
                            $email = $user?->email ?? 'N/A';
                            $methodLabel = optional($invoice->paymentMethod)->tenPhuongThuc ?? ($invoice->maTT ?: 'N/A');
                            $issuedAt = $invoice->ngayLap
                                ? \Illuminate\Support\Carbon::parse($invoice->ngayLap)
                                : ($invoice->created_at ? \Illuminate\Support\Carbon::parse($invoice->created_at) : null);
                        @endphp
                        <tr class="invoice-row" data-invoice-id="{{ $invoice->maHD }}">
                            <td><strong>#{{ $invoice->maHD }}</strong></td>
                            <td>
                                <div class="fw-semibold text-truncate" title="{{ $studentName }}">{{ $studentName }}</div>
                                @if ($student)
                                    <div class="text-muted small">HV#{{ $student->maHV }}</div>
                                @endif
                            </td>
                            <td class="text-truncate" title="{{ $email }}">{{ $email }}</td>
                            <td><span class="badge rounded-pill text-bg-light">{{ $methodLabel }}</span></td>
                            <td class="text-end fw-semibold text-primary">{{ number_format($invoice->tongTien) }} VND</td>
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
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary js-invoice-detail"
                                        data-invoice-id="{{ $invoice->maHD }}">
                                    <i class="bi bi-eye me-1"></i> Chi tiết
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                Không tìm thấy hóa đơn phù hợp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($invoices->total() > $perPage)
            <div class="card-footer">
                @include('components.pagination', [
                    'paginator' => $invoices,
                    'ariaLabel' => 'Điều hướng hóa đơn',
                    'containerClass' => 'mt-2',
                    'alignClass' => 'justify-content-center justify-content-lg-end',
                ])
            </div>
        @endif
    </div>

    <div class="modal fade" id="invoiceDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Hóa đơn #<span data-invoice-number>---</span></h5>
                        <p class="text-muted modal-subtitle mb-0" data-invoice-issued-full></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="invoice-modal-loader" data-modal-loader>
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                        <p class="text-muted mt-3 mb-0">Đang tải dữ liệu hóa đơn...</p>
                    </div>

                    <div class="alert alert-danger d-none" role="alert" data-modal-error>
                        Không thể tải dữ liệu hóa đơn. Vui lòng thử lại.
                    </div>

                    <div class="invoice-modal-content d-none" data-modal-content>
                        <div class="row g-3 mb-3 invoice-detail-grid">
                            <div class="col-md-4">
                                <div class="detail-tile">
                                    <span class="label">Tổng thanh toán</span>
                                    <strong class="value text-primary" data-total-amount>---</strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-tile">
                                    <span class="label">Ngày lập</span>
                                    <strong class="value" data-issued-date>---</strong>
                                    <span class="sub" data-issued-time></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="detail-tile">
                                    <span class="label">Phương thức</span>
                                    <strong class="value" data-payment-method>---</strong>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-lg-6">
                                <div class="detail-card">
                                    <h6 class="detail-card__title">Học viên</h6>
                                    <ul class="detail-card__list">
                                        <li><span>Họ tên</span><strong data-student-name>---</strong></li>
                                        <li><span>Email</span><strong data-student-email>---</strong></li>
                                        <li><span>Số điện thoại</span><strong data-student-phone>---</strong></li>
                                        <li><span>Mã học viên</span><strong data-student-id>---</strong></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="detail-card">
                                    <h6 class="detail-card__title">Thông tin hóa đơn</h6>
                                    <ul class="detail-card__list">
                                        <li><span>Mã hóa đơn</span><strong data-invoice-number-summary>---</strong></li>
                                        <li><span>Người xử lý</span><strong data-processor>---</strong></li>
                                        <li><span>Số khóa học</span><strong data-item-count>0</strong></li>
                                        <li><span>Ghi chú</span><strong data-invoice-note>---</strong></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <h6 class="section-title">Danh sách khóa học</h6>
                        <div class="table-responsive mb-3">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Khóa học</th>
                                        <th class="text-center">Số lượng</th>
                                        <th class="text-end">Đơn giá</th>
                                        <th class="text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody data-items-body></tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mb-4">
                            <div class="invoice-total-box">
                                <div>Tổng cộng: <span data-items-total>0 VND</span></div>
                            </div>
                        </div>

                        <div class="related-invoices" data-related-wrapper>
                            <h6 class="section-title d-flex justify-content-between align-items-center">
                                <span>Hóa đơn liên quan</span>
                                <small class="text-muted">Cùng học viên</small>
                            </h6>
                            <div class="list-group list-group-flush" data-related-list></div>
                            <div class="text-muted small d-none" data-related-empty>Không có hóa đơn nào khác.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-outline-secondary" href="#" target="_blank" rel="noopener" data-pdf-button>
                        <i class="bi bi-file-earmark-pdf me-1"></i> Xuất PDF
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.invoiceDetailConfig = {
            notFound: 'Không tìm thấy thông tin hóa đơn.',
            loader: 'Đang tải dữ liệu hóa đơn...'
        };
    </script>
    <script src="{{ asset('js/Admin/admin-invoices.js') }}" defer></script>
@endpush
