@extends('layouts.student')

@section('title', 'Thanh toán đơn hàng')

@push('styles')
    @php
        $pageStyle = 'css/Student/pages-checkout.css';
    @endphp
    <link rel="stylesheet" href="{{ asset($pageStyle) }}?v={{ student_asset_version($pageStyle) }}">
@endpush

@php
    $courseCount = $courses instanceof \Illuminate\Support\Collection ? $courses->count() : collect($courses)->count();
    $comboCount = $combos instanceof \Illuminate\Support\Collection ? $combos->count() : collect($combos)->count();
    $currentUser = auth()->user();
    $successMethod = $successPayload['payment_method'] ?? null;
    $alreadyActiveCourses = $successPayload['already_active_courses'] ?? [];

            $paymentMethods = [
        'vnpay' => [
            'label' => 'Cổng thanh toán VNPAY',
            'icon' => 'fa-wallet',
        ],
        'qr' => [
            'label' => 'Quét mã VietQR',
            'icon' => 'fa-qrcode',
        ],
        'bank' => [
            'label' => 'Chuyển khoản ngân hàng',
            'icon' => 'fa-building-columns',
        ],
        'visa' => [
            'label' => 'Thẻ quốc tế / Ví điện tử',
            'icon' => 'fa-credit-card',
        ],
    ];

    $methodLabels = collect($paymentMethods)->mapWithKeys(fn ($method, $key) => [$key => $method['label']])->all();
    $methodLabel = $methodLabels[$successMethod] ?? null;
    $defaultMethodKey = array_key_first($methodLabels);
    $activeMethodKey = $successMethod && isset($methodLabels[$successMethod]) ? $successMethod : $defaultMethodKey;
    $activeMethodLabel = $methodLabels[$activeMethodKey] ?? (reset($methodLabels) ?: null);
@endphp

@section('content')
    <section class="page-hero page-hero--soft">
        <div class="oc-container">
            <p class="page-hero__breadcrumb">
                <a href="{{ route('student.cart.index') }}">Giỏ hàng</a>
                <span aria-hidden="true">></span>
                <span>Thanh toán</span>
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
                        <span class="checkout-step__label">Xác nhận đơn hàng</span>
                    </div>
                    <div class="checkout-step" data-checkout-step="2">
                        <span class="checkout-step__index">2</span>
                        <span class="checkout-step__label">Chọn phương thức thanh toán</span>
                    </div>
                    <div class="checkout-step" data-checkout-step="3">
                        <span class="checkout-step__index">3</span>
                        <span class="checkout-step__label">Hoàn tất</span>
                    </div>
                </div>

                <div class="checkout-stages">
                    {{-- Stage 1 --}}
                    <div class="checkout-stage {{ $stage === 1 ? 'is-active' : '' }}" data-stage="1">
                        <div class="checkout-stage__grid">
                            <div class="checkout-card checkout-card--primary">
                                <header class="checkout-card__header">
                                    <h2>Đơn hàng của bạn</h2>
                                    <p>Vui lòng kiểm tra lại combo và khóa học trước khi tiếp tục.</p>
                                </header>

                                @if($comboCount > 0)
                                    <div class="checkout-block">
                                        <h3>Combo ({{ $comboCount }})</h3>
                                        <ul class="checkout-list">
                                            @foreach($combos as $combo)
                                                @php
                                                    $comboArray   = is_array($combo);
                                                    $comboTitle   = $comboArray ? $combo['tenGoi'] : $combo->tenGoi;
                                                    $comboCover   = $comboArray ? ($combo['cover_image_url'] ?? asset('Assets/logo.png')) : $combo->cover_image_url;
                                                    $comboPrice   = $comboArray ? $combo['sale_price'] : $combo->sale_price;
                                                    $comboOriginal= $comboArray ? $combo['original_price'] : $combo->original_price;
                                                    $comboCourses = $comboArray ? ($combo['courses'] ?? []) : $combo->courses;
                                                @endphp
                                                <li class="checkout-item checkout-item--combo">
                                                    <div class="checkout-item__media">
                                                        <img src="{{ $comboCover }}" alt="{{ $comboTitle }}" loading="lazy">
                                                    </div>
                                                    <div class="checkout-item__body">
                                                        <h4>{{ $comboTitle }}</h4>
                                                        <p>Gồm {{ is_countable($comboCourses) ? count($comboCourses) : $comboCourses->count() }} khóa học. Tiết kiệm {{ number_format(max(0, $comboOriginal - $comboPrice), 0, ',', '.') }} VND.</p>
                                                        <ul class="checkout-sublist">
                                                            @foreach($comboCourses as $course)
                                                                @php
                                                                    $courseTitle = is_array($course) ? ($course['tenKH'] ?? '') : $course->tenKH;
                                                                @endphp
                                                                <li><i class="fa-solid fa-check"></i> {{ $courseTitle }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                    <strong class="checkout-item__price">{{ number_format((int) $comboPrice, 0, ',', '.') }} VND</strong>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if($courseCount > 0)
                                    <div class="checkout-block">
                                        <h3>Khóa học lẻ ({{ $courseCount }})</h3>
                                        <ul class="checkout-list">
                                            @foreach($courses as $course)
                                                @php
                                                    $courseArray = is_array($course);
                                                    $courseTitle = $courseArray ? $course['tenKH'] : $course->tenKH;
                                                    $courseCover = $courseArray ? $course['cover_image_url'] : $course->cover_image_url;
                                                    $coursePrice = $courseArray ? $course['hocPhi'] : $course->hocPhi;
                                                    $courseEnd   = $courseArray ? ($course['end_date_label'] ?? 'Đang cập nhật') : ($course->end_date_label ?? 'Đang cập nhật');
                                                @endphp
                                                <li class="checkout-item">
                                                    <div class="checkout-item__media">
                                                        <img src="{{ $courseCover }}" alt="{{ $courseTitle }}" loading="lazy">
                                                    </div>
                                                    <div class="checkout-item__body">
                                                        <h4>{{ $courseTitle }}</h4>
                                                        <p>Hạn truy cập: {{ $courseEnd }}</p>
                                                    </div>
                                                    <strong class="checkout-item__price">{{ number_format((int) $coursePrice, 0, ',', '.') }} VND</strong>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>

                            <aside class="checkout-card checkout-card--sidebar">
                                <div class="checkout-sidebar__head">
                                    <h3>Tổng thanh toán</h3>
                                    <p>Giá đã bao gồm ưu đãi hiện tại.</p>
                                </div>

                                <div class="checkout-summary checkout-summary--panel">
                                    <div>
                                        <span>Combo</span>
                                        <strong>{{ number_format($comboTotal, 0, ',', '.') }} VND</strong>
                                    </div>
                                    <div>
                                        <span>Khóa học lẻ</span>
                                        <strong>{{ number_format($courseTotal, 0, ',', '.') }} VND</strong>
                                    </div>
                                    <div class="checkout-summary__total">
                                        <span>Tổng thanh toán</span>
                                        <strong>{{ number_format($total, 0, ',', '.') }} VND</strong>
                                    </div>
                                </div>

                                <ul class="checkout-perks">
                                    <li><i class="fa-solid fa-shield-heart"></i> Bảo mật thanh toán chuẩn VNPAY</li>
                                    <li><i class="fa-solid fa-bolt"></i> Kích hoạt khóa học ngay sau khi thanh toán</li>
                                    <li><i class="fa-solid fa-headset"></i> Mentor OCC hỗ trợ xuyên suốt</li>
                                </ul>

                                <div class="checkout-actions checkout-actions--stacked">
                                    <button type="button" class="btn btn--primary btn--lg" data-checkout-next>
                                        Tiếp tục <i class="fa-solid fa-arrow-right"></i>
                                    </button>
                                    <a class="btn btn--ghost btn--lg" href="{{ route('student.cart.index') }}">
                                        Quay lại giỏ hàng
                                    </a>
                                </div>
                            </aside>
                        </div>
                    </div>

                    {{-- Stage 2 --}}
                    <div class="checkout-stage {{ $stage === 2 ? 'is-active' : '' }}" data-stage="2">
                        <form method="post" action="{{ route('student.checkout.complete') }}" class="checkout-stage__grid checkout-payment">
                            @csrf
                            <div class="checkout-card checkout-card--primary checkout-card--payment">
                                <header class="checkout-card__header checkout-card__header--inline">
                                    <div>
                                        <p class="checkout-eyebrow">Bước 2 · Phương thức thanh toán</p>
                                        <h2>Phương thức thanh toán</h2>
                                        <p>Chọn kênh phù hợp, OCC tự lưu phiên thanh toán và giữ trạng thái đơn.</p>
                                    </div>
                                </header>

                                <div class="payment-hero">
                                    <div>
                                        <p class="payment-hero__eyebrow">Chỉ còn một bước</p>
                                        <h3>Hoàn tất thanh toán để kích hoạt ngay</h3>
                                        <p>OCC đồng bộ email, hóa đơn và trạng thái khóa học ngay sau khi cổng thanh toán phản hồi.</p>
                                    </div>
                                </div>

                                <div class="payment-methods">
                                    <div class="payment-options">
                                        @foreach(collect($paymentMethods)->only(['vnpay', 'qr']) as $methodKey => $method)
                                            @php $isActive = $activeMethodKey === $methodKey; @endphp
                                            <label class="payment-option {{ $isActive ? 'is-active' : '' }}" data-checkout-method-card>
                                                <input
                                                    type="radio"
                                                    name="payment_method"
                                                    value="{{ $methodKey }}"
                                                    data-method-label="{{ $method['label'] }}"
                                                    {{ $isActive ? 'checked' : '' }}
                                                    {{ $hasSuccessPayload ? 'disabled' : '' }}
                                                >
                                                <div class="payment-option__header">
                                                    <div class="payment-option__title">
                                                        @if(!empty($method['badge']))
                                                            <span class="payment-option__badge">{{ $method['badge'] }}</span>
                                                        @endif
                                                        <h3 data-payment-title>{{ $method['label'] }}</h3>
                                                    </div>
                                                    <span class="payment-option__icon">
                                                        @switch($methodKey)
                                                            @case('qr')<i class="fa-solid fa-qrcode"></i>@break
                                                            @case('bank')<i class="fa-solid fa-building-columns"></i>@break
                                                            @case('visa')<i class="fa-solid fa-credit-card"></i>@break
                                                            @case('vnpay')<i class="fa-solid fa-wallet"></i>@break
                                                            @default<i class="fa-solid fa-shield"></i>
                                                        @endswitch
                                                    </span>
                                                </div>
                                                <p class="payment-option__desc"></p>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <aside class="checkout-card checkout-card--sidebar">
                                <div class="checkout-sidebar__head checkout-sidebar__head--pill">
                                    <div>
                                        <p class="checkout-eyebrow">Tóm tắt</p>
                                        <h3>Đơn hàng của bạn</h3>
                                    </div>
                                </div>

                                <div class="checkout-summary checkout-summary--panel">
                                    <div class="checkout-summary__method">
                                        <span>Phương thức</span>
                                        <strong data-checkout-method-label>{{ $activeMethodLabel }}</strong>
                                    </div>
                                    <div>
                                        <span>Tổng combo</span>
                                        <strong>{{ number_format($comboTotal, 0, ',', '.') }} VND</strong>
                                    </div>
                                    <div>
                                        <span>Tổng khóa học</span>
                                        <strong>{{ number_format($courseTotal, 0, ',', '.') }} VND</strong>
                                    </div>
                                    <div class="checkout-summary__total">
                                        <span>Cần thanh toán</span>
                                        <strong>{{ number_format($total, 0, ',', '.') }} VND</strong>
                                    </div>
                                </div>

                                <div class="checkout-actions checkout-actions--stacked">
                                    <button type="button" class="btn btn--ghost btn--lg" data-checkout-prev>
                                        <i class="fa-solid fa-arrow-left"></i> Quay lại
                                    </button>
                                    <button type="submit" class="btn btn--primary btn--lg">
                                        Xác nhận thanh toán
                                    </button>
                                </div>
                            </aside>
                        </form>
                    </div>

                    {{-- Stage 3 --}}
                    <div class="checkout-stage {{ $stage === 3 ? 'is-active' : '' }}" data-stage="3">
                        <div class="checkout-card checkout-card--success">
                            <header>
                                <div class="checkout-success-icon">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <h2>Thanh toán thành công!</h2>
                                <p>Đơn hàng đã được ghi nhận{{ $methodLabel ? " bằng phương thức {$methodLabel}" : '' }}.</p>
                            </header>

                            <div class="checkout-grid">
                                <div class="checkout-success-block">
                                    <h3>Truy cập khóa học</h3>
                                    <p>Khóa học/combo bạn vừa thanh toán đã được mở ngay lập tức. Hãy vào <strong>Khóa học của tôi</strong> để bắt đầu học hoặc xem chi tiết khóa học bạn vừa mua.</p>

                                    @if(!empty($alreadyActiveCourses))
                                        <div class="checkout-status-block checkout-status-block--muted">
                                            <h4>Khóa học đã sở hữu</h4>
                                            <ul>
                                                @foreach($alreadyActiveCourses as $item)
                                                    <li><i class="fa-solid fa-circle-check"></i> {{ $item['tenKH'] ?? '' }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <div class="checkout-actions">
                                        <a class="btn btn--primary btn--lg" href="{{ route('student.my-courses') }}">Vào lớp học</a>
                                        <a class="btn btn--ghost btn--lg" href="{{ route('student.courses.index') }}">Khám phá khóa học khác</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- /Stage 3 --}}
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/Student/checkout.js') }}" defer></script>
@endpush
