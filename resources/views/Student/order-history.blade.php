@extends('layouts.student')

@section('title', 'Lịch sử đơn hàng')

@push('styles')
    @php
        $pageStyle = 'css/Student/pages-order-history.css';
    @endphp
    <link rel="stylesheet" href="{{ asset($pageStyle) }}?v={{ student_asset_version($pageStyle) }}">
@endpush

@section('content')
<main class="order-history-page">
    <div class="order-history-container">
        {{-- Header --}}
        <div class="order-history-header">
            <div class="header-content">
                <h1>
                    <i class="fa-solid fa-cart-shopping"></i>
                    Lịch sử đơn hàng
                </h1>
                <p class="subtitle">Quản lý và theo dõi các đơn hàng bạn đã thực hiện</p>
            </div>
        </div>

        {{-- Statistics --}}
        <div class="order-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Tổng đơn hàng</span>
                    <span class="stat-value">{{ $totalOrders }}</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Tổng chi tiêu (Đã kích hoạt)</span>
                    <span class="stat-value">{{ number_format($totalAmount, 0, ',', '.') }}₫</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-book"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Khóa học đã kích hoạt</span>
                    <span class="stat-value">{{ $activatedCoursesCount }}</span>
                </div>
            </div>
        </div>

        {{-- Orders List --}}
        @if($invoices->count() > 0)
            <div class="orders-list">
                @foreach($invoices as $invoice)
                    <article class="order-card" data-invoice-id="{{ $invoice->maHD }}">
                        {{-- Order Header --}}
                        <div class="order-card__header">
                            <div class="order-info">
                                <h3 class="order-number">
                                    <i class="fa-solid fa-file-invoice"></i>
                                    Đơn hàng #{{ $invoice->maHD }}
                                </h3>
                                <div class="order-meta">
                                    <span class="order-date">
                                        <i class="fa-regular fa-calendar"></i>
                                        {{ $invoice->ngayLap ? \Carbon\Carbon::parse($invoice->ngayLap)->format('d/m/Y H:i') : 'N/A' }}
                                    </span>
                                    @if($invoice->paymentMethod)
                                        <span class="payment-method">
                                            <i class="fa-solid fa-credit-card"></i>
                                            {{ $invoice->paymentMethod->tenTT }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="order-total">
                                <span class="total-label">Tổng tiền</span>
                                <span class="total-amount">{{ number_format($invoice->tongTien, 0, ',', '.') }}₫</span>
                            </div>
                        </div>

                        {{-- Order Items --}}
                        <div class="order-card__body">
                            @if($invoice->items && $invoice->items->count() > 0)
                                <div class="order-items">
                                    @foreach($invoice->items as $item)
                                        @if($item->course)
                                            <div class="order-item">
                                                <div class="item-image">
                                                    <a href="{{ route('student.courses.show', $item->course->slug) }}">
                                                        <img src="{{ $item->course->cover_image_url }}"
                                                             alt="{{ $item->course->tenKH }}"
                                                             loading="lazy">
                                                    </a>
                                                </div>
                                                <div class="item-details">
                                                    <h4 class="item-title">
                                                        <a href="{{ route('student.courses.show', $item->course->slug) }}">
                                                            {{ $item->course->tenKH }}
                                                        </a>
                                                        @if(isset($item->enrollment))
                                                            @if($item->enrollment->trangThai === 'ACTIVE')
                                                                <span class="status-badge status-active">
                                                                    <i class="fa-solid fa-circle-check"></i>
                                                                    Đã kích hoạt
                                                                </span>
                                                            @elseif($item->enrollment->trangThai === 'EXPIRED')
                                                                <span class="status-badge status-expired">
                                                                    <i class="fa-solid fa-circle-xmark"></i>
                                                                    Đã hết hạn
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span class="status-badge status-not-enrolled">
                                                                <i class="fa-solid fa-exclamation-circle"></i>
                                                                Chưa đăng ký
                                                            </span>
                                                        @endif
                                                    </h4>
                                                    @if($item->course->category)
                                                        <p class="item-category">
                                                            <i class="fa-solid fa-tag"></i>
                                                            {{ $item->course->category->tenDanhMuc }}
                                                        </p>
                                                    @endif
                                                    @if($item->course->teacher)
                                                        <p class="item-teacher">
                                                            <i class="fa-solid fa-chalkboard-user"></i>
                                                            {{ $item->course->teacher->hoTen ?? $item->course->teacher->name }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="item-price">
                                                    <span class="price-label">Đơn giá</span>
                                                    <span class="price-value">{{ number_format($item->donGia, 0, ',', '.') }}₫</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="order-empty">
                                    <i class="fa-solid fa-box-open"></i>
                                    <p>Không có thông tin chi tiết đơn hàng</p>
                                </div>
                            @endif
                        </div>

                        {{-- Order Footer --}}
                        @if($invoice->ghiChu)
                            <div class="order-card__footer">
                                <div class="order-note">
                                    <i class="fa-solid fa-note-sticky"></i>
                                    <span class="note-label">Ghi chú:</span>
                                    <span class="note-content">{{ $invoice->ghiChu }}</span>
                                </div>
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($invoices->hasPages())
                <div class="pagination-wrapper">
                    {{ $invoices->links() }}
                </div>
            @endif
        @else
            {{-- Empty State --}}
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <h3>Chưa có đơn hàng nào</h3>
                <p>Bạn chưa thực hiện đơn hàng nào. Khám phá và đăng ký khóa học ngay hôm nay!</p>
                <a href="{{ route('student.courses.index') }}" class="btn btn-primary">
                    <i class="fa-solid fa-compass"></i>
                    Khám phá khóa học
                </a>
            </div>
        @endif
    </div>
</main>
@endsection

@push('scripts')
    @php
        $pageScript = 'js/Student/order-history.js';
    @endphp
    <script src="{{ asset($pageScript) }}?v={{ student_asset_version($pageScript) }}"></script>
@endpush
