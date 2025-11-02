@extends('layouts.student')
@section('title', 'Combo khóa học')

@push('styles')
    @php $comboStyle = 'css/Student/pages-combos.css'; @endphp
    <link rel="stylesheet" href="{{ asset($comboStyle) }}?v={{ student_asset_version($comboStyle) }}">
@endpush

@section('content')
    <section class="combo-hero">
        <div class="oc-container combo-hero__inner">
            <div class="combo-hero__text">
                <p class="combo-hero__kicker">Ưu đãi học tập</p>
                <h1>Combo khóa học theo lộ trình</h1>
                <p class="combo-hero__lead">
                    Chọn lộ trình TOEIC phù hợp, tiết kiệm đến 40% so với mua lẻ từng khóa học.
                    Mỗi combo đã được đội ngũ giảng viên OCC xây dựng theo mục tiêu điểm cụ thể.
                </p>
                <form action="{{ route('student.combos.index') }}" method="get" class="combo-search" role="search">
                    <label for="combo-search" class="sr-only">Tìm combo</label>
                    <input id="combo-search" type="text" name="q" value="{{ $search }}" placeholder="Bạn muốn luyện kỹ năng nào?">
                    <button type="submit">
                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                        <span>Tìm combo</span>
                    </button>
                </form>
            </div>
            <div class="combo-hero__visual" aria-hidden="true">
                <img src="{{ asset('Assets/Duy4.jpg') }}" alt="">
            </div>
        </div>
    </section>

    @if($spotlightCombos->isNotEmpty())
        <section class="combo-spotlight">
            <div class="oc-container">
                <div class="combo-section-head">
                    <h2>Combo nổi bật</h2>
                    <p>Những gói được học viên lựa chọn nhiều nhất tuần qua.</p>
                </div>
                <div class="combo-grid combo-grid--spotlight">
                    @foreach($spotlightCombos as $combo)
                        <article class="combo-card combo-card--spotlight">
                            <div class="combo-card__badge">
                                <i class="fa-solid fa-fire"></i> Được yêu thích
                            </div>
                            <div class="combo-card__image">
                                <img src="{{ $combo->cover_image_url }}" alt="">
                            </div>
                            <div class="combo-card__body">
                                <h3>{{ $combo->tenGoi }}</h3>
                                <p class="combo-card__description">{{ Str::limit($combo->moTa, 120) }}</p>
                                <ul class="combo-card__meta">
                                    <li><i class="fa-solid fa-book"></i> {{ $combo->courses_count }} khóa học</li>
                                    <li><i class="fa-solid fa-clock"></i>
                                        {{ $combo->ngayKetThuc ? 'Đến '.optional($combo->ngayKetThuc)->format('d/m/Y') : 'Không giới hạn' }}
                                    </li>
                                </ul>
                                <div class="combo-card__pricing">
                                    <strong>{{ number_format($combo->sale_price, 0, ',', '.') }} VND</strong>
                                    <span class="origin">{{ number_format($combo->original_price, 0, ',', '.') }} VND</span>
                                </div>
                                <div class="combo-card__actions">
                                    <a class="btn btn--ghost" href="{{ route('student.combos.show', $combo->slug) }}">Xem chi tiết</a>
                                    <form method="post" action="{{ route('student.cart.store-combo') }}">
                                        @csrf
                                        <input type="hidden" name="combo_id" value="{{ $combo->maGoi }}">
                                        <button type="submit" class="btn btn--primary">
                                            <i class="fa-solid fa-cart-plus"></i> Thêm vào giỏ
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="combo-listing">
        <div class="oc-container">
            <div class="combo-section-head">
                <h2>Combo đang mở bán</h2>
                <p>Ưu đãi được kích hoạt ngay sau khi thanh toán.</p>
            </div>

            @if($availableCombos->isEmpty())
                <div class="combo-empty">
                    <div class="combo-empty__icon"><i class="fa-solid fa-gift"></i></div>
                    <h3>Chưa có combo phù hợp</h3>
                    <p>Vui lòng thử lại với từ khóa khác hoặc quay lại sau, chúng tôi đang chuẩn bị thêm nhiều ưu đãi mới.</p>
                </div>
            @else
                <div class="combo-grid">
                    @foreach($availableCombos as $combo)
                        <article class="combo-card">
                            <div class="combo-card__image">
                                <img src="{{ $combo->cover_image_url }}" alt="">
                                @if($combo->saving_amount > 0)
                                    <span class="combo-card__discount">Tiết kiệm {{ $combo->saving_percent }}%</span>
                                @endif
                            </div>
                            <div class="combo-card__body">
                                <h3><a href="{{ route('student.combos.show', $combo->slug) }}">{{ $combo->tenGoi }}</a></h3>
                                <p class="combo-card__description">{{ Str::limit($combo->moTa, 110) }}</p>
                                <ul class="combo-card__meta">
                                    <li><i class="fa-solid fa-layer-group"></i> {{ $combo->courses_count }} khóa học</li>
                                    <li><i class="fa-solid fa-calendar-check"></i>
                                        {{ $combo->ngayBatDau ? 'Bắt đầu '.optional($combo->ngayBatDau)->format('d/m') : 'Kích hoạt ngay' }}
                                    </li>
                                </ul>
                                <div class="combo-card__pricing">
                                    <strong>{{ number_format($combo->sale_price, 0, ',', '.') }} VND</strong>
                                    <span class="origin">{{ number_format($combo->original_price, 0, ',', '.') }} VND</span>
                                </div>
                            </div>
                            <div class="combo-card__footer">
                                <a class="btn btn--ghost" href="{{ route('student.combos.show', $combo->slug) }}">Xem chi tiết</a>
                                <form method="post" action="{{ route('student.cart.store-combo') }}">
                                    @csrf
                                    <input type="hidden" name="combo_id" value="{{ $combo->maGoi }}">
                                    <button type="submit" class="btn btn--primary">
                                        Thêm vào giỏ
                                    </button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="combo-pagination">
                    {{ $availableCombos->links() }}
                </div>
            @endif
        </div>
    </section>

    @if($upcomingCombos->isNotEmpty())
        <section class="combo-upcoming">
            <div class="oc-container">
                <div class="combo-section-head">
                    <h2>Combo sắp ra mắt</h2>
                    <p>Đặt lịch nhắc nhở để không bỏ lỡ ưu đãi đặc biệt.</p>
                </div>
                <div class="combo-upcoming__list">
                    @foreach($upcomingCombos as $combo)
                        <article class="combo-upcoming__item">
                            <div class="combo-upcoming__date">
                                <strong>{{ optional($combo->ngayBatDau)->format('d') }}</strong>
                                <span>{{ optional($combo->ngayBatDau)->format('M') }}</span>
                            </div>
                            <div class="combo-upcoming__info">
                                <h3>{{ $combo->tenGoi }}</h3>
                                <p>{{ Str::limit($combo->moTa, 140) }}</p>
                            </div>
                            <a class="btn btn--ghost" href="{{ route('student.combos.show', $combo->slug) }}">
                                Chi tiết
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
