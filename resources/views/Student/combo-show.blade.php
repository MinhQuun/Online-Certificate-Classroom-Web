@extends('layouts.student')
@section('title', $combo->tenGoi . ' - Combo khóa học')

@push('styles')
    @php $comboStyle = 'css/Student/pages-combos.css'; @endphp
    <link rel="stylesheet" href="{{ asset($comboStyle) }}?v={{ student_asset_version($comboStyle) }}">
@endpush

@section('content')
    <section class="combo-detail-hero">
        <div class="oc-container combo-detail-hero__inner">
            <div class="combo-detail-hero__text">
                <p class="combo-detail-hero__kicker">Combo khóa học</p>
                <h1>{{ $combo->tenGoi }}</h1>
                <p class="combo-detail-hero__lead">{{ $combo->moTa }}</p>

                <ul class="combo-detail-meta">
                    <li><i class="fa-solid fa-layer-group"></i> {{ $combo->courses->count() }} khóa học</li>
                    <li><i class="fa-solid fa-clock"></i>
                        @if($combo->ngayKetThuc)
                            Có hạn đến {{ optional($combo->ngayKetThuc)->format('d/m/Y') }}
                        @else
                            Không giới hạn thời gian học
                        @endif
                    </li>
                    @if($combo->active_promotion)
                        <li><i class="fa-solid fa-gift"></i> {{ $combo->active_promotion->tenKM }}</li>
                    @endif
                </ul>

                <div class="combo-detail-pricing">
                    <div>
                        <span>Giá combo</span>
                        <strong>{{ number_format($combo->sale_price, 0, ',', '.') }} VND</strong>
                    </div>
                    <div>
                        <span>Giá học lẻ</span>
                        <strong>{{ number_format($combo->original_price, 0, ',', '.') }} VND</strong>
                    </div>
                    <div>
                        <span>Tiết kiệm</span>
                        <strong>{{ number_format($combo->saving_amount, 0, ',', '.') }} VND</strong>
                    </div>
                </div>

                <div class="combo-detail-actions">
                    <form method="post" action="{{ route('student.cart.store-combo') }}">
                        @csrf
                        <input type="hidden" name="combo_id" value="{{ $combo->maGoi }}">
                        <button type="submit" class="btn btn--primary btn--lg" {{ $isAvailable ? '' : 'disabled' }}>
                            <i class="fa-solid fa-cart-plus"></i>
                            {{ $isAvailable ? 'Thêm vào giỏ hàng' : 'Combo sắp mở bán' }}
                        </button>
                    </form>
                    <a class="btn btn--ghost btn--lg" href="{{ route('student.cart.index') }}">
                        Xem giỏ hàng
                    </a>
                </div>

                @if(!$isAvailable)
                    <p class="combo-availability-note">
                        <i class="fa-solid fa-circle-info"></i>
                        Combo này sẽ mở bán vào {{ optional($combo->ngayBatDau)->format('d/m/Y') }}.
                        Bạn có thể đặt lịch nhắc nhở trong mục "Combo sắp ra mắt".
                    </p>
                @endif
            </div>

            <div class="combo-detail-hero__visual" aria-hidden="true">
                <div class="combo-detail-cover">
                    <img src="{{ $combo->cover_image_url }}" alt="">
                </div>
                <div class="combo-detail-savings">
                    <span>Tiết kiệm</span>
                    <strong>{{ $combo->saving_percent }}%</strong>
                </div>
            </div>
        </div>
    </section>

    <section class="combo-detail-content">
        <div class="oc-container combo-detail-layout">
            <div class="combo-detail-main">
                <h2>Combo bao gồm</h2>
                <p class="text-muted">Các khóa học được sắp xếp theo lộ trình. Học viên được kích hoạt toàn bộ khóa học sau khi combo được thanh toán.</p>

                <div class="combo-course-timeline">
                    @foreach($combo->courses as $index => $course)
                        <article class="combo-course-card">
                            <div class="combo-course-card__order">
                                <span>{{ $index + 1 }}</span>
                            </div>
                            <div class="combo-course-card__body">
                                <h3>
                                    <a href="{{ route('student.courses.show', $course->slug) }}" target="_blank" rel="noopener">
                                        {{ $course->tenKH }}
                                    </a>
                                </h3>
                                <ul class="combo-course-card__meta">
                                    <li><i class="fa-solid fa-chalkboard-teacher"></i> {{ $course->teacher->hoTen ?? $course->teacher->name ?? 'Giảng viên OCC' }}</li>
                                    <li><i class="fa-solid fa-coins"></i> {{ number_format($course->hocPhi, 0, ',', '.') }} VND</li>
                                </ul>
                                <p>{{ Str::limit($course->moTa, 180) }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <aside class="combo-detail-sidebar">
                <div class="combo-summary-card">
                    <h3>Tóm tắt combo</h3>
                    <ul class="combo-summary-list">
                        <li>
                            <span>Trạng thái</span>
                            <strong>{{ $isAvailable ? 'Đang mở bán' : 'Sắp diễn ra' }}</strong>
                        </li>
                        <li>
                            <span>Ngày bắt đầu</span>
                            <strong>{{ $combo->ngayBatDau ? optional($combo->ngayBatDau)->format('d/m/Y') : 'Kích hoạt ngay' }}</strong>
                        </li>
                        <li>
                            <span>Ngày kết thúc</span>
                            <strong>{{ $combo->ngayKetThuc ? optional($combo->ngayKetThuc)->format('d/m/Y') : 'Không giới hạn' }}</strong>
                        </li>
                        <li>
                            <span>Tổng học phí lẻ</span>
                            <strong>{{ number_format($combo->original_price, 0, ',', '.') }} VND</strong>
                        </li>
                        <li>
                            <span>Combo ưu đãi</span>
                            <strong>{{ number_format($combo->sale_price, 0, ',', '.') }} VND</strong>
                        </li>
                    </ul>

                    <form method="post" action="{{ route('student.cart.store-combo') }}" class="combo-summary-action">
                        @csrf
                        <input type="hidden" name="combo_id" value="{{ $combo->maGoi }}">
                        <button type="submit" class="btn btn--primary btn--block" {{ $isAvailable ? '' : 'disabled' }}>
                            <i class="fa-solid fa-cart-shopping"></i>
                            {{ $isAvailable ? 'Thêm combo vào giỏ' : 'Combo sắp mở bán' }}
                        </button>
                    </form>
                </div>

                @if($relatedCombos->isNotEmpty())
                    <div class="combo-related">
                        <h3>Combo liên quan</h3>
                        <ul>
                            @foreach($relatedCombos as $related)
                                <li>
                                    <a href="{{ route('student.combos.show', $related->slug) }}">
                                        <span>{{ $related->tenGoi }}</span>
                                        <small>{{ number_format($related->sale_price, 0, ',', '.') }} VND</small>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </aside>
        </div>
    </section>
@endsection
