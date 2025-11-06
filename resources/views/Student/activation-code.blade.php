@extends('layouts.student')

@section('title', 'Kích hoạt khóa học')

@push('styles')
    @php
        $pageStyle = 'css/Student/pages-activation.css';
    @endphp
    <link rel="stylesheet" href="{{ asset($pageStyle) }}?v={{ student_asset_version($pageStyle) }}">
@endpush

@section('content')
    <section class="activation-hero">
        <div class="activation-hero__content">
            <h1>Kích hoạt khóa học</h1>
            <p>Nhập mã kích hoạt mà OCC đã gửi tới email của bạn để mở khóa nội dung học tập. Phần lịch sử bên dưới sẽ giúp bạn theo dõi trạng thái của các mã đã được gửi và lịch sử sử dụng.</p>
        </div>
    </section>

    <section class="activation-grid">
        <div class="activation-column activation-column--primary">
            <div class="activation-card">
                <h2>Nhập mã kích hoạt</h2>
                <p class="description">Mỗi mã chỉ sử dụng được một lần cho đúng khóa học. Vui lòng dán mã bạn nhận được từ email vào ô bên dưới.</p>
                <form class="activation-form" method="post" action="{{ route('student.activations.redeem') }}">
                    @csrf
                    <label for="code">Mã kích hoạt</label>
                    <input id="code" name="code" type="text" placeholder="OCC-XXXX-XXXX" value="{{ old('code') }}" autocomplete="off">
                    <p class="input-note">Mã gồm chữ và số, phân tách bằng dấu gạch ngang. Không phân biệt chữ hoa thường.</p>
                    @error('code')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                    <button type="submit" class="activation-submit">
                        Kích hoạt ngay
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>
            </div>

            <div class="activation-card">
                <h2>Khóa học chờ kích hoạt</h2>
                @if($pendingEnrollments->isEmpty())
                    <div class="activation-empty">
                        Bạn chưa có khóa học nào ở trạng thái chờ kích hoạt. Hãy kiểm tra lịch sử đơn hàng hoặc tiếp tục khám phá các khóa học của OCC.
                    </div>
                @else
                    <ul class="pending-course-list">
                        @foreach($pendingEnrollments as $enrollment)
                            @php
                                $course = $enrollment->course;
                            @endphp
                            <li class="pending-course">
                                <div class="pending-course__info">
                                    <h3>{{ $course->tenKH ?? 'Khóa #' . $enrollment->maKH }}</h3>
                                    <p>Mã khóa học: {{ $enrollment->maKH }} 
                                        @if($course?->teacher)
                                            · Giảng viên: {{ $course->teacher->hoTen }}
                                        @endif
                                    </p>
                                </div>
                                <span class="pending-course__status">
                                    <i class="fa-solid fa-hourglass-half"></i>
                                    Chờ kích hoạt
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="activation-column activation-column--secondary">
            <div class="activation-card activation-card--history">
                @php
                    $statusLabels = [
                        'CREATED' => 'Đã tạo',
                        'SENT' => 'Đã gửi',
                        'USED' => 'Đã sử dụng',
                        'EXPIRED' => 'Hết hạn',
                    ];
                @endphp
                <h2>Lịch sử mã kích hoạt</h2>

                @if($comboActivationCodes->isNotEmpty())
                    <div class="activation-history-group">
                        <h3>Mã combo</h3>
                        <ul class="activation-history activation-history--combo">
                            @foreach($comboActivationCodes as $comboCode)
                                @php
                                    $status = strtoupper($comboCode->trangThai);
                                    $statusClass = strtolower($status);
                                    $comboCourses = $comboCode->combo?->courses ?? collect();
                                @endphp
                                <li class="activation-history__item activation-history__item--combo">
                                    <div class="history-headline">
                                        <span class="history-course">
                                            {{ $comboCode->combo->tenGoi ?? 'Combo #' . $comboCode->maGoi }}
                                        </span>
                                        <span class="history-status history-status--{{ $statusClass }}">
                                            {{ $statusLabels[$status] ?? $status }}
                                        </span>
                                    </div>
                                    <ul class="history-meta">
                                        <li><i class="fa-regular fa-calendar-plus"></i> Phát hành: {{ optional($comboCode->generated_at)->format('d/m/Y H:i') ?? '-' }}</li>
                                        <li><i class="fa-regular fa-paper-plane"></i> Gửi: {{ optional($comboCode->sent_at)->format('d/m/Y H:i') ?? 'Chưa gửi' }}</li>
                                        <li><i class="fa-regular fa-circle-check"></i> Sử dụng: {{ optional($comboCode->used_at)->format('d/m/Y H:i') ?? 'Chưa sử dụng' }}</li>
                                    </ul>
                                    @if($comboCourses->isNotEmpty())
                                        <div class="history-combo-courses">
                                            <span>Khóa học bao gồm:</span>
                                            <ul>
                                                @foreach($comboCourses as $course)
                                                    <li>{{ $course->tenKH ?? ('Khóa #' . $course->maKH) }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($activationCodes->isNotEmpty())
                    <div class="activation-history-group">
                        <h3>Mã khóa học</h3>
                        <ul class="activation-history">
                            @foreach($activationCodes as $code)
                                @php
                                    $status = strtoupper($code->trangThai);
                                    $statusClass = strtolower($status);
                                @endphp
                                <li class="activation-history__item">
                                    <div class="history-headline">
                                        <span class="history-course">{{ $code->course->tenKH ?? 'Khóa #' . $code->maKH }}</span>
                                        <span class="history-status history-status--{{ $statusClass }}">
                                            {{ $statusLabels[$status] ?? $status }}
                                        </span>
                                    </div>

                                    <ul class="history-meta">
                                        <li><i class="fa-regular fa-calendar-plus"></i> Phát hành: {{ optional($code->generated_at)->format('d/m/Y H:i') ?? '-' }}</li>
                                        <li><i class="fa-regular fa-paper-plane"></i> Gửi: {{ optional($code->sent_at)->format('d/m/Y H:i') ?? 'Chưa gửi' }}</li>
                                        <li><i class="fa-regular fa-circle-check"></i> Sử dụng: {{ optional($code->used_at)->format('d/m/Y H:i') ?? 'Chưa sử dụng' }}</li>
                                    </ul>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($activationCodes->isEmpty() && $comboActivationCodes->isEmpty())
                    <div class="activation-empty">
                        Chúng tôi sẽ hiển thị lịch sử mã kích hoạt của bạn tại đây ngay khi có đơn hàng mới.
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/Student/activation-code.js') }}" defer></script>
@endpush
