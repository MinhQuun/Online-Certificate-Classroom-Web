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
    $pendingActivations = $successPayload['pending_activation_courses'] ?? [];
    $alreadyActiveCourses = $successPayload['already_active_courses'] ?? [];

    $methodLabels = [
        'qr'   => 'Quét mã QR',
        'bank' => 'Chuyển khoản ngân hàng',
        'visa' => 'Thẻ quốc tế / Ví điện tử',
    ];
    $methodDescriptions = [
        'qr'   => 'Thanh toán ngay bằng mã QR nội địa.',
        'bank' => 'Chuyển khoản qua Internet Banking hoặc tại quầy.',
        'visa' => 'Thanh toán bằng thẻ Visa, Mastercard hoặc ví điện tử.',
    ];
    $methodLabel = $methodLabels[$successMethod] ?? null;
    $defaultMethodKey = array_key_first($methodLabels);
    $defaultMethodLabel = $defaultMethodKey !== null ? $methodLabels[$defaultMethodKey] : (reset($methodLabels) ?: null);
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
                                    <p>Vui lòng kiểm tra lại combos và khóa học trước khi tiếp tục.</p>
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
                            <div class="checkout-card checkout-card--primary">
                                <header class="checkout-card__header">
                                    <h2>Phương thức thanh toán</h2>
                                    <p>Chọn phương thức phù hợp để hoàn tất đơn hàng.</p>
                                </header>

                                <div class="checkout-methods">
                                    @foreach($methodLabels as $methodKey => $label)
                                        <label class="checkout-method">
                                            <input
                                                type="radio"
                                                name="payment_method"
                                                value="{{ $methodKey }}"
                                                {{ $loop->first ? 'checked' : '' }}
                                                {{ $hasSuccessPayload ? 'disabled' : '' }}
                                            >
                                            <div class="checkout-method__content">
                                                <div>
                                                    <h3>{{ $label }}</h3>
                                                    <p>{{ $methodDescriptions[$methodKey] }}</p>
                                                </div>
                                                <span class="checkout-method__icon">
                                                    @switch($methodKey)
                                                        @case('qr')<i class="fa-solid fa-qrcode"></i>@break
                                                        @case('bank')<i class="fa-solid fa-building-columns"></i>@break
                                                        @case('visa')<i class="fa-solid fa-credit-card"></i>@break
                                                    @endswitch
                                                </span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <aside class="checkout-card checkout-card--sidebar">
                                <div class="checkout-sidebar__head">
                                    <h3>Tóm tắt đơn hàng</h3>
                                    <p>Kiểm tra lại trước khi thanh toán.</p>
                                </div>

                                <div class="checkout-summary checkout-summary--panel">
                                    <div class="checkout-summary__method">
                                        <span>Phương thức</span>
                                        <strong data-checkout-method-label>{{ $defaultMethodLabel }}</strong>
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

                                <div class="checkout-note">
                                    <i class="fa-solid fa-receipt"></i>
                                    <span>Hóa đơn và mã kích hoạt sẽ được gửi tới email {{ $currentUser?->email ?? 'của bạn' }}.</span>
                                </div>

                                <div class="checkout-actions checkout-actions--stacked">
                                    <button type="button" class="btn btn--ghost btn--lg" data-checkout-prev>
                                        <i class="fa-solid fa-arrow-left"></i> Quay lại
                                    </button>
                                    <button type="submit" class="btn btn--primary btn--lg">
                                        Thanh toán
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
                                    <h3>Đơn hàng</h3>
                                    <ul class="checkout-list checkout-list--small">
                                        @foreach($successPayload['combos'] ?? [] as $combo)
                                            <li class="checkout-item checkout-item--mini">
                                                <div class="checkout-item__media">
                                                    <img src="{{ $combo['cover_image_url'] ?? asset('Assets/logo.png') }}" alt="{{ $combo['tenGoi'] }}">
                                                </div>
                                                <div class="checkout-item__body">
                                                    <h4>{{ $combo['tenGoi'] }}</h4>
                                                    <span>Combo ưu đãi</span>
                                                </div>
                                                <strong class="checkout-item__price">{{ number_format((int) $combo['sale_price'], 0, ',', '.') }} VND</strong>
                                            </li>
                                        @endforeach
                                        @foreach($successPayload['courses'] ?? [] as $course)
                                            <li class="checkout-item checkout-item--mini">
                                                <div class="checkout-item__media">
                                                    <img src="{{ $course['cover_image_url'] ?? asset('Assets/logo.png') }}" alt="{{ $course['tenKH'] }}">
                                                </div>
                                                <div class="checkout-item__body">
                                                    <h4>{{ $course['tenKH'] }}</h4>
                                                    <span>Khóa học lẻ</span>
                                                </div>
                                                <strong class="checkout-item__price">{{ number_format((int) $course['hocPhi'], 0, ',', '.') }} VND</strong>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div class="checkout-summary">
                                        <div>
                                            <span>Tổng combo</span>
                                            <strong>{{ number_format((int) ($successPayload['combo_total'] ?? 0), 0, ',', '.') }} VND</strong>
                                        </div>
                                        <div>
                                            <span>Tổng khóa học</span>
                                            <strong>{{ number_format((int) ($successPayload['course_total'] ?? 0), 0, ',', '.') }} VND</strong>
                                        </div>
                                        <div class="checkout-summary__total">
                                            <span>Đã thanh toán</span>
                                            <strong>{{ number_format((int) ($successPayload['total'] ?? 0), 0, ',', '.') }} VND</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="checkout-success-block">
                                    <h3>Mã kích hoạt</h3>
                                    <p>Chúng tôi đã gửi mã kích hoạt đến email {{ $currentUser?->email ?? 'của bạn' }}. Bạn có thể kích hoạt khóa học trong mục “Mã kích hoạt”.</p>

                                    @if(!empty($pendingActivations))
                                        <div class="checkout-activation">
                                            <h4>Chờ kích hoạt ({{ count($pendingActivations) }})</h4>
                                            <ul>
                                                @foreach($pendingActivations as $item)
                                                    <li><i class="fa-solid fa-key"></i> {{ $item['tenKH'] ?? '' }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if(!empty($alreadyActiveCourses))
                                        <div class="checkout-activation checkout-activation--muted">
                                            <h4>Đã kích hoạt trước đó</h4>
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
