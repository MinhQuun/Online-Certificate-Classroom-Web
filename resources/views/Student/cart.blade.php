@extends('layouts.student')

@section('title', 'Giỏ hàng combo & khóa học')

@push('styles')
    @php
        $pageStyle = 'css/Student/pages-cart.css';
    @endphp
    <link rel="stylesheet" href="{{ asset($pageStyle) }}?v={{ student_asset_version($pageStyle) }}">
@endpush

@php
    $courseCount = $courses->count();
    $comboCount = $combos->count();
    $isEmpty = $courseCount === 0 && $comboCount === 0;
@endphp

@section('content')
    <section class="page-hero page-hero--soft">
        <div class="oc-container">
            <p class="page-hero__breadcrumb">
                <a href="{{ route('student.courses.index') }}">Trang chủ</a>
                <span aria-hidden="true">></span>
                <span>Giỏ hàng</span>
            </p>
            <h1>Giỏ hàng của bạn (<span data-cart-count-total>{{ $courseCount + $comboCount }}</span>)</h1>
            <p>Chọn combo hoặc khóa học để thanh toán. Hệ thống sẽ giữ nguyên trạng thái giỏ hàng khi bạn đăng nhập.</p>
        </div>
    </section>

    <section class="cart-section">
        <div class="oc-container">
            <div class="cart-empty" data-cart-empty-state @if(!$isEmpty) hidden @endif>
                <div class="cart-empty__icon" aria-hidden="true">🛒</div>
                <h2>Giỏ hàng đang trống</h2>
                    <p>Khám phá các combo ưu đãi hoặc khóa học để bắt đầu hành trình học tập ngay hôm nay.</p>
                <div class="cart-empty__actions">
                        <a class="btn btn--primary" href="{{ route('student.combos.index') }}">Combo ưu đãi</a>
                        <a class="btn btn--ghost" href="{{ route('student.courses.index') }}">Thư viện khóa học</a>
                    </div>
                </div>
            </div>

            <form
                method="post"
                action="{{ route('student.checkout.start') }}"
                id="cart-form"
                data-cart-ajax="off"
                hidden
            >
                @csrf
            </form>

            <div class="cart-layout" data-cart-form-scope @if($isEmpty) hidden @endif>
                    <div class="cart-board">
                        <div class="cart-board__header">
                            <div class="cart-board__header-main">
                                <label class="cart-checkbox">
                                    <input type="checkbox" data-cart-select-all>
                                    <span data-cart-total-count>Chọn tất cả ({{ $courseCount + $comboCount }})</span>
                                </label>
                                <div class="cart-board__chips">
                                    <span class="cart-board__meta" data-cart-meta>
                                        {{ $comboCount }} combo · {{ $courseCount }} khóa học
                                    </span>
                                    <span class="cart-board__selection is-empty" data-cart-selected-count>Chưa chọn mục nào</span>
                                </div>
                            </div>
                            <div class="cart-board__actions">
                                <form
                                    method="post"
                                    action="{{ route('student.cart.destroy-selected') }}"
                                    class="cart-board__remove-form"
                                    data-cart-remove-form
                                    data-cart-action="remove-selected"
                                    data-confirm="Bạn chắc chắn muốn xoá các mục đã chọn?"
                                >
                                    @csrf
                                    @method('delete')
                                    <div data-cart-remove-inputs hidden></div>
                                    <button
                                        type="submit"
                                        class="cart-board__remove"
                                        data-cart-remove-selected
                                        disabled
                                        aria-disabled="true"
                                    >
                                        <i class="fa-solid fa-minus-circle" aria-hidden="true"></i>
                                        <span data-cart-remove-label>Xoá đã chọn</span>
                                    </button>
                                </form>
                                <form
                                    method="post"
                                    action="{{ route('student.cart.destroy-all') }}"
                                    class="cart-board__clear-form"
                                    data-cart-clear-form
                                    data-cart-action="clear-all"
                                    data-confirm="Bạn có chắc chắn muốn xoá toàn bộ giỏ hàng?"
                                >
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="cart-board__clear">
                                        <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                        <span>Xoá toàn bộ</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <ul class="cart-list">
                            @foreach($combos as $combo)
                                <li class="cart-item cart-item--combo" data-cart-item data-price="{{ $combo->sale_price }}">
                                    <label class="cart-item__select">
                                        <input type="checkbox" name="items[]" value="combo:{{ $combo->maGoi }}" form="cart-form" data-cart-item-checkbox>
                                        <span class="cart-item__indicator"></span>
                                    </label>
                                    <div class="cart-item__body">
                                        <div class="cart-item__thumb">
                                            <img src="{{ $combo->cover_image_url }}" alt="">
                                        </div>
                                        <div class="cart-item__info">
                                            <div class="cart-item__info-head">
                                                <h3>{{ $combo->tenGoi }}</h3>
                                                <span class="badge badge--combo">Combo</span>
                                            </div>
                                            <p class="cart-item__description">{{ Str::limit($combo->moTa, 140) }}</p>
                                            <ul class="cart-item__meta">
                                                <li><i class="fa-solid fa-layer-group"></i> {{ $combo->courses->count() }} khóa học</li>
                                                <li><i class="fa-solid fa-calendar-check"></i>
                                                    {{ $combo->ngayBatDau ? 'Bắt đầu ' . optional($combo->ngayBatDau)->format('d/m/Y') : 'Kích hoạt ngay' }}
                                                </li>
                                            </ul>
                                            <div class="cart-item__pricing">
                                                <strong>{{ number_format($combo->sale_price, 0, ',', '.') }} VND</strong>
                                                <span>{{ number_format($combo->original_price, 0, ',', '.') }} VND</span>
                                            </div>
                                        </div>
                                        <div class="cart-item__actions">
                                            <form method="post" action="{{ route('student.cart.destroy-combo', $combo->maGoi) }}" data-cart-item-remove>
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="cart-item__remove">Xoá combo</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="cart-item__combo-courses">
                                        <p class="title">Bao gồm:</p>
                                        <ul>
                                            @foreach($combo->courses as $course)
                                                <li>
                                                    <i class="fa-solid fa-check"></i>
                                                    {{ $course->tenKH }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </li>
                            @endforeach

                            @foreach($courses as $course)
                                <li class="cart-item" data-cart-item data-price="{{ $course->hocPhi }}">
                                    <label class="cart-item__select">
                                        <input type="checkbox" name="items[]" value="course:{{ $course->maKH }}" form="cart-form" data-cart-item-checkbox>
                                        <span class="cart-item__indicator"></span>
                                    </label>
                                    <div class="cart-item__body">
                                        <div class="cart-item__thumb">
                                            <img src="{{ $course->cover_image_url }}" alt="">
                                        </div>
                                        <div class="cart-item__info">
                                            <div class="cart-item__info-head">
                                                <h3>{{ $course->tenKH }}</h3>
                                            </div>
                                            <ul class="cart-item__meta">
                                                <li><i class="fa-solid fa-user-tie"></i> {{ $course->teacher->hoTen ?? $course->teacher->name ?? 'Giảng viên OCC' }}</li>
                                                <li><i class="fa-solid fa-clock"></i> {{ $course->thoiHanNgay ?? 90 }} ngày học</li>
                                            </ul>
                                            <div class="cart-item__pricing">
                                                <strong>{{ number_format($course->hocPhi, 0, ',', '.') }} VND</strong>
                                            </div>
                                        </div>
                                        <div class="cart-item__actions">
                                            <form method="post" action="{{ route('student.cart.destroy', $course->maKH) }}" data-cart-item-remove>
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="cart-item__remove">Xoá</button>
                                            </form>
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
                                <p>Tổng hợp combo và khóa học đã chọn</p>
                            </div>

                            <div class="summary-row">
                                <span>Combo</span>
                                <strong>{{ number_format($comboTotal, 0, ',', '.') }} VND</strong>
                            </div>
                            <div class="summary-row">
                                <span>Khóa học lẻ</span>
                                <strong>{{ number_format($courseTotal, 0, ',', '.') }} VND</strong>
                            </div>

                            <div class="summary-total">
                                <span>Tổng thanh toán</span>
                                <strong data-cart-total>{{ number_format($total, 0, ',', '.') }} VND</strong>
                            </div>

                            <button
                                type="submit"
                                form="cart-form"
                                class="summary-btn"
                                data-cart-submit
                                disabled
                                aria-disabled="true"
                            >
                                Xác nhận thanh toán
                            </button>
                            <p class="summary-note">
                                Bạn sẽ được yêu cầu đăng nhập trước khi thanh toán.
                                Giỏ hàng được đồng bộ với tài khoản của bạn.
                            </p>
                        </div>
                    </aside>
                </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/Student/cart.js') }}" defer></script>
    <script src="{{ asset('js/Student/ajax-forms.js') }}"></script>
@endpush
