@extends('layouts.student')

@section('title', 'Thanh toán đơn hàng')

@push('styles')
    @php
        $pageStyle = 'css/Student/pages-checkout.css';
    @endphp
    <link rel="stylesheet" href="{{ asset($pageStyle) }}?v={{ student_asset_version($pageStyle) }}">
@endpush

@section('content')
    @php
        $courseCount = $courses->count();
        $currentUser = auth()->user();
        $successMethod = $successPayload['payment_method'] ?? null;

        $methodLabels = [
            'qr' => 'Quét mã QR',
            'bank' => 'Chuyển khoản ngân hàng',
            'visa' => 'Thẻ quốc tế / Ví điện tử',
        ];
        $methodLabel = $methodLabels[$successMethod] ?? null;
    @endphp

    <section class="page-hero page-hero--soft">
        <div class="oc-container">
            <p class="page-hero__breadcrumb">
                <a href="{{ route('student.cart.index') }}">Giỏ hàng</a>
                <span aria-hidden="true">›</span>
                <span>Thanh toán đơn hàng</span>
            </p>
            <h1>Thanh toán đơn hàng</h1>
            <p>Hoàn tất thông tin và xác nhận thanh toán trong 3 bước đơn giản.</p>
        </div>
    </section>

    <section class="checkout-section">
        <div class="oc-container">
            <div
                class="checkout-shell"
                data-checkout
                data-current-stage="{{ $stage }}"
                data-locked="{{ $hasSuccessPayload ? 'true' : 'false' }}"
            >
                <div class="checkout-stepper">
                    <div class="checkout-step" data-checkout-step="1">
                        <span class="checkout-step__index">1</span>
                        <span class="checkout-step__label">Xác nhận thông tin đơn hàng</span>
                    </div>
                    <div class="checkout-step" data-checkout-step="2">
                        <span class="checkout-step__index">2</span>
                        <span class="checkout-step__label">Xác nhận thanh toán</span>
                    </div>
                    <div class="checkout-step" data-checkout-step="3">
                        <span class="checkout-step__index">3</span>
                        <span class="checkout-step__label">Hoàn tất thanh toán</span>
                    </div>
                </div>

                <div class="checkout-stages">
                    <div class="checkout-stage {{ $stage === 1 ? 'is-active' : '' }}" data-stage="1">
                        <div class="checkout-card">
                            <header>
                                <h2>Khóa học ({{ $courseCount }})</h2>
                                <p>Kiểm tra lại thông tin trước khi chuyển sang bước thanh toán</p>
                            </header>
                            <ul class="checkout-course-list">
                                @forelse($courses as $course)
                                    @php
                                        $courseIsArray = is_array($course);
                                        $courseId = $courseIsArray ? $course['maKH'] : $course->maKH;
                                        $courseTitle = $courseIsArray ? $course['tenKH'] : $course->tenKH;
                                        $courseCover = $courseIsArray ? $course['cover_image_url'] : $course->cover_image_url;
                                        $coursePrice = $courseIsArray ? $course['hocPhi'] : $course->hocPhi;
                                        $courseEnd = $courseIsArray ? ($course['end_date_label'] ?? 'Đang cập nhật') : ($course->end_date_label ?? 'Đang cập nhật');
                                    @endphp
                                    <li class="checkout-course">
                                        <div class="checkout-course__media">
                                            <img src="{{ $courseCover }}" alt="{{ $courseTitle }}" loading="lazy">
                                        </div>
                                        <div class="checkout-course__body">
                                            <h3>{{ $courseTitle }}</h3>
                                            <span>Hạn truy cập: {{ $courseEnd }}</span>
                                        </div>
                                        <strong class="checkout-course__price">{{ number_format((int) $coursePrice, 0, ',', '.') }} VNĐ</strong>
                                    </li>
                                @empty
                                    <li class="checkout-course checkout-course--empty">
                                        Giỏ hàng của bạn đang trống.
                                    </li>
                                @endforelse
                            </ul>


                            <div class="checkout-total">
                                <span>Tổng thanh toán</span>
                                <strong>{{ number_format($total, 0, ',', '.') }} VNĐ</strong>
                            </div>

                            <button
                                type="button"
                                class="checkout-btn checkout-btn--primary"
                                data-checkout-next
                                @if($hasSuccessPayload) disabled aria-disabled="true" @endif
                            >
                                Xác nhận thông tin
                            </button>
                        </div>
                    </div>

                    <div class="checkout-stage {{ $stage === 2 ? 'is-active' : '' }}" data-stage="2">
                        <form method="post" action="{{ route('student.checkout.complete') }}" class="checkout-card checkout-card--payment">
                            @csrf
                            <header>
                                <h2>Xác nhận thanh toán</h2>
                                <p>Chọn hình thức thanh toán phù hợp. OCC sẽ gửi hướng dẫn chi tiết đến email của bạn.</p>
                            </header>

                            <div class="payment-options">
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="qr" checked>
                                    <div class="payment-option__body">
                                        <span class="payment-option__title">Quét mã QR</span>
                                        <span class="payment-option__desc">Thanh toán ngay với VietQR / Mobile Banking</span>
                                    </div>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="bank">
                                    <div class="payment-option__body">
                                        <span class="payment-option__title">Chuyển khoản ngân hàng</span>
                                        <span class="payment-option__desc">Nhận thông tin tài khoản OCC và chuyển khoản thủ công</span>
                                    </div>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="visa">
                                    <div class="payment-option__body">
                                        <span class="payment-option__title">Thẻ quốc tế | Ví điện tử</span>
                                        <span class="payment-option__desc">Hỗ trợ Visa, MasterCard, Momo, ZaloPay</span>
                                    </div>
                                </label>
                            </div>

                            <div class="checkout-total checkout-total--compact">
                                <span>Tổng cần thanh toán</span>
                                <strong>{{ number_format($total, 0, ',', '.') }} VNĐ</strong>
                            </div>

                            <button type="submit" class="checkout-btn checkout-btn--primary">
                                Xác nhận thanh toán
                            </button>
                            <p class="checkout-note">Hệ thống sẽ yêu cầu bạn xác thực lại giao dịch và gửi biên nhận về email <strong>{{ $currentUser->email ?? 'của bạn' }}</strong>.</p>
                        </form>
                    </div>

                    <div class="checkout-stage {{ $stage === 3 ? 'is-active' : '' }}" data-stage="3">
                        <div class="checkout-card checkout-card--success">
                            <div class="checkout-success">
                                <div class="checkout-success__media" aria-hidden="true">
                                    <img src="{{ asset('Assets/Banner/banner2.png') }}" alt="Hoàn tất thanh toán">
                                </div>
                                <div class="checkout-success__text">
                                    <p class="checkout-success__eyebrow">Hoàn tất thanh toán</p>
                                    <h2>Thật tuyệt vời, {{ $currentUser->hoTen ?? $currentUser->name ?? 'bạn' }}!</h2>
                                    <p>Bạn đã hoàn tất đơn hàng {{ $courseCount }} khóa học với tổng giá trị <strong>{{ number_format($total, 0, ',', '.') }} VNĐ</strong>. @if($methodLabel)Phương thức: <strong>{{ $methodLabel }}</strong>.@endif</p>
                                    <p>OCC sẽ gửi hướng dẫn kích hoạt vào email của bạn trong vòng vài phút. Đội ngũ mentor đã sẵn sàng đồng hành!</p>
                                    <div class="checkout-success__actions">
                                        <a href="{{ route('student.courses.index') }}" class="checkout-btn checkout-btn--ghost">Tiếp tục học tập</a>
                                        <a href="{{ route('student.cart.index') }}" class="checkout-btn checkout-btn--outline">Quản lý đơn hàng</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/Student/checkout.js') }}" defer></script>
@endpush
