@extends('layouts.admin')
@section('title', 'Chi tiet hoa don #' . $invoice->maHD)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Admin/admin-invoices.css') }}">
@endpush

@php
    $student = $invoice->student;
    $user = $student?->user;
    $issuedAt = $invoice->ngayLap
        ? \Illuminate\Support\Carbon::parse($invoice->ngayLap)
        : ($invoice->created_at ? \Illuminate\Support\Carbon::parse($invoice->created_at) : null);
    $items = $invoice->items;
    $totalQuantity = $items->sum('soLuong');
    $totalAmount = $items->sum(function ($item) {
        return (float) $item->soLuong * (float) $item->donGia;
    });
@endphp

@section('content')
    <section class="page-header">
        <span class="kicker">Admin</span>
        <h1 class="title">Hoa don #{{ $invoice->maHD }}</h1>
        <p class="muted">Chi tiet giao dich va cac khoa hoc da mua.</p>
    </section>

    <div class="d-flex justify-content-between align-items-center mb-3 gap-2 flex-wrap">
        <a class="btn btn-outline-secondary" href="{{ url()->previous() !== url()->current() ? url()->previous() : route('admin.invoices.index') }}">
            <i class="bi bi-arrow-left me-1"></i> Quay lai danh sach
        </a>
        <div class="d-flex gap-2">
            <span class="badge text-bg-light">Trang thai: Hoan tat</span>
            @if ($invoice->paymentMethod)
                <span class="badge text-bg-primary">{{ $invoice->paymentMethod->tenPhuongThuc }}</span>
            @endif
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-xl-8">
            <div class="card invoice-summary h-100">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-3">
                        <div>
                            <div class="summary-label text-uppercase">Ma hoa don</div>
                            <div class="summary-value">#{{ $invoice->maHD }}</div>
                        </div>
                        <div>
                            <div class="summary-label text-uppercase">Ngay lap</div>
                            <div class="summary-value">
                                {{ $issuedAt ? $issuedAt->format('d/m/Y H:i') : 'N/A' }}
                            </div>
                        </div>
                        <div>
                            <div class="summary-label text-uppercase">Tong thanh toan</div>
                            <div class="summary-value text-primary">{{ number_format($invoice->tongTien) }}đ</div>
                        </div>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="summary-label text-uppercase">So khoa hoc</div>
                            <div class="summary-value">{{ $totalQuantity }}</div>
                        </div>
                        <div class="col-md-4">
            <div class="summary-label text-uppercase">Nhan vien xu ly</div>
                            <div class="summary-value">{{ $invoice->maND ? 'User #' . $invoice->maND : 'Tu dong' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="summary-label text-uppercase">Ghi chu</div>
                            <div class="summary-value">{{ $invoice->ghiChu ?: 'Khong co' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card student-card h-100">
                <div class="card-header">
                    <h5 class="m-0">Thong tin hoc vien</h5>
                </div>
                <div class="card-body">
                    <div class="student-name">{{ $student?->hoTen ?? $user?->name ?? 'Chua xac dinh' }}</div>
                    <ul class="list-unstyled student-meta">
                        <li><span>Email:</span> {{ $user?->email ?? 'N/A' }}</li>
                        <li><span>So dien thoai:</span> {{ $user?->sdt ?? 'N/A' }}</li>
                        <li><span>Ma hoc vien:</span> {{ $student?->maHV ?? 'N/A' }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header bg-body-secondary">
            <h5 class="m-0">Danh sach khoa hoc</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Khoa hoc</th>
                        <th class="text-center">So luong</th>
                        <th class="text-end">Don gia</th>
                        <th class="text-end">Thanh tien</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $index => $item)
                    @php
                        $course = $item->course;
                        $lineTotal = (float) $item->soLuong * (float) $item->donGia;
                    @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-semibold">{{ $course->tenKH ?? 'Khoa hoc #' . $item->maKH }}</div>
                                <div class="text-muted small">Ma KH: {{ $item->maKH }}</div>
                            </td>
                            <td class="text-center">{{ $item->soLuong }}</td>
                            <td class="text-end">{{ number_format($item->donGia) }}đ</td>
                            <td class="text-end">{{ number_format($lineTotal) }}đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <div class="total-box">
                <div>Cong: <span>{{ number_format($totalAmount) }}đ</span></div>
            </div>
        </div>
    </div>

    @if ($relatedInvoices->isNotEmpty())
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0">Hoa don lien quan</h5>
                <span class="text-muted small">Cung hoc vien</span>
            </div>
            <div class="list-group list-group-flush">
                @foreach ($relatedInvoices as $related)
                    @php
                        $relatedAt = $related->ngayLap
                            ? \Illuminate\Support\Carbon::parse($related->ngayLap)
                            : ($related->created_at ? \Illuminate\Support\Carbon::parse($related->created_at) : null);
                    @endphp
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                        href="{{ route('admin.invoices.show', $related) }}">
                        <div>
                            <div class="fw-semibold">#{{ $related->maHD }} · {{ $relatedAt ? $relatedAt->format('d/m/Y') : 'N/A' }}</div>
                            <div class="text-muted small">
                                {{ $related->paymentMethod->tenPhuongThuc ?? $related->maTT ?? 'N/A' }}
                            </div>
                        </div>
                        <span class="text-primary fw-semibold">{{ number_format($related->tongTien) }}đ</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
@endsection
