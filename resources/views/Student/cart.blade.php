@extends('layouts.student')

@section('title', 'Giỏ hàng của bạn')

@push('styles')
    @php
        $pageStyle = 'css/Student/pages-cart.css';
    @endphp
    <link rel="stylesheet" href="{{ asset($pageStyle) }}?v={{ student_asset_version($pageStyle) }}">
@endpush

@section('content')
    <section class="page-hero page-hero--soft">
        <div class="oc-container">
            <p class="page-hero__breadcrumb">
                <a href="{{ route('student.courses.index') }}">Trang chủ</a>
                <span aria-hidden="true">›</span>
                <span>Giỏ hàng</span>
            </p>
            <h1>Giỏ hàng ({{ $courses->count() }})</h1>
            <p>Chọn những khóa học bạn muốn thanh toán. Hệ thống sẽ giữ nguyên trạng thái giỏ hàng và nhắc đăng nhập ở bước tiếp theo.</p>
        </div>
    </section>

    <section class="cart-section">
        <div class="oc-container">
            @if($courses->isEmpty())
                <div class="cart-empty">
                    <div class="cart-empty__icon" aria-hidden="true">🛒</div>
                    <h2>Giỏ hàng đang trống</h2>
                    <p>Bạn chưa thêm khóa học nào. Khám phá thư viện khóa học để bắt đầu hành trình học tập nhé!</p>
                    <a class="btn btn--primary" href="{{ route('student.courses.index') }}">Khám phá khóa học</a>
                </div>
            @else
                <form method="post" action="{{ route('student.checkout.start') }}" id="cart-form" hidden>
                    @csrf
                </form>
                <div class="cart-layout" data-cart-form-scope>
                    <div class="cart-board">
                            <div class="cart-board__header">
                                <label class="cart-checkbox">
                                    <input type="checkbox" data-cart-select-all>
                                    <span>Chọn tất cả ({{ $courses->count() }})</span>
                                </label>
                                <span class="cart-board__meta">Đang có {{ $courses->count() }} khóa học</span>
                            </div>

                            @error('items')
                                <p class="cart-error" role="alert">{{ $message }}</p>
                            @enderror

                            <ul class="cart-list">
                                @foreach($courses as $course)
                                    @php
                                        $price = (int) ($course->hocPhi ?? 0);
                                        $teacherName = optional($course->teacher)->hoTen ?? 'Đội ngũ OCC';
                                        $endDate = $course->end_date_label ?? 'Đang cập nhật';
                                    @endphp
                                    <li class="cart-item" data-cart-item data-price="{{ $price }}">
                                        <label class="cart-checkbox cart-checkbox--item">
                                            <input type="checkbox" name="items[]" value="{{ $course->maKH }}" data-cart-item-checkbox form="cart-form">
                                            <span class="sr-only">Chọn {{ $course->tenKH }}</span>
                                        </label>
                                        <div class="cart-item__media">
                                            <img src="{{ $course->cover_image_url }}" alt="{{ $course->tenKH }}" loading="lazy">
                                        </div>
                                        <div class="cart-item__body">
                                            <div class="cart-item__top">
                                                <div>
                                                    <h3>{{ $course->tenKH }}</h3>
                                                    <div class="cart-item__meta">
                                                        <span>Giảng viên: {{ $teacherName }}</span>
                                                        <span>Kết thúc: {{ $endDate }}</span>
                                                    </div>
                                                </div>
                                                <span class="cart-item__price">{{ number_format($price, 0, ',', '.') }} VNĐ</span>
                                            </div>
                                            <div class="cart-item__foot">
                                                <div class="cart-item__rating" aria-label="Đánh giá 5 sao">
                                                    @for($i = 0; $i < 5; $i++)
                                                        <i class="fa-solid fa-star" aria-hidden="true"></i>
                                                    @endfor
                                                    <span>5.0</span>
                                                </div>
                                                <div class="cart-item__actions">
                                                    <a href="{{ route('student.courses.show', $course->slug) }}">Xem chi tiết</a>
                                                    <form method="post" action="{{ route('student.cart.destroy', $course->maKH) }}">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="cart-item__remove">Xóa</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                    </div>

                    <aside class="cart-summary">
                        <div class="summary-card">
                            <div class="summary-card__head">
                                <h2>Thông tin đơn hàng</h2>
                                <p>Tổng hợp nhanh khóa học bạn đã chọn</p>
                            </div>
                            <div class="summary-row">
                                <span>Đã chọn</span>
                                <strong data-cart-selected-count>0 khóa học</strong>
                            </div>
                            <div class="summary-row">
                                <span>Tạm tính</span>
                                <strong data-cart-subtotal>0 VNĐ</strong>
                            </div>
                            <div class="summary-total">
                                <span>Tổng thanh toán</span>
                                <strong data-cart-total>0 VNĐ</strong>
                            </div>
                            <button
                                type="submit"
                                form="cart-form"
                                class="summary-btn" form="cart-form"
                                data-cart-submit
                                disabled
                                aria-disabled="true"
                            >
                                Xác nhận thanh toán
                            </button>
                            <p class="summary-note">
                                Bạn sẽ được yêu cầu đăng nhập/đăng ký trước khi sang bước thanh toán. Mọi thông tin đơn hàng sẽ được lưu lại.
                            </p>
                        </div>
                    </aside>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/Student/cart.js') }}" defer></script>
@endpush

