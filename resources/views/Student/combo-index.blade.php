@extends('layouts.student')
@section('title', 'Combo khóa học')

@push('styles')
    @php $comboStyle = 'css/Student/pages-combos.css'; @endphp
    <link rel="stylesheet" href="{{ asset($comboStyle) }}?v={{ student_asset_version($comboStyle) }}">
@endpush

@php
    $availableCollection = method_exists($availableCombos, 'getCollection')
        ? $availableCombos->getCollection()
        : collect($availableCombos);
    $totalAvailable = method_exists($availableCombos, 'total')
        ? $availableCombos->total()
        : $availableCollection->count();
    $averageSaving = (int) round($availableCollection->avg('saving_percent') ?? 0);
    $totalCoursesInCombos = (int) $availableCollection->sum('courses_count');
    $heroImage = asset('Assets/Combos/combo_khoahoc.png');
    $comboCartIds = $comboCartIds ?? [];
@endphp

@section('content')
    <section class="combo-hero">
        <div class="oc-container combo-hero__inner">
            <div class="combo-hero__text">
                <span class="combo-hero__badge">Ưu đãi dành riêng cho học viên OCC</span>
                <h1>Combo khóa học TOEIC chuẩn lộ trình 4 kỹ năng</h1>
                <p class="combo-hero__lead">
                    Chọn combo phù hợp với mục tiêu điểm của bạn và nhận giáo trình, mentor cùng lịch học trọn gói.
                    Mỗi lộ trình được giảng viên OCC thiết kế bám sát đề thi thật, giúp bạn tối ưu thời gian và chi phí.
                </p>

                <div class="combo-hero__metrics" role="list">
                    <div role="listitem">
                        <span>{{ $totalAvailable }}+</span>
                        <small>Combo đang mở bán</small>
                    </div>
                    <div role="listitem">
                        <span>{{ $totalCoursesInCombos }}</span>
                        <small>Khóa học trong combo</small>
                    </div>
                    <div role="listitem">
                        <span>{{ max($averageSaving, 15) }}%</span>
                        <small>Tiết kiệm tối đa</small>
                    </div>
                </div>

                <form action="{{ route('student.combos.index') }}" method="get" class="combo-search" role="search">
                    <label for="combo-search" class="sr-only">Tìm combo</label>
                    <input
                        id="combo-search"
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Bạn muốn tăng điểm TOEIC ở kỹ năng nào?"
                        autocomplete="off"
                    >
                    <button type="submit">
                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                        <span>Tìm combo</span>
                    </button>
                </form>

                <div class="combo-hero__tags" aria-label="Lợi ích nổi bật">
                    <span><i class="fa-solid fa-headphones"></i> Listening + Reading</span>
                    <span><i class="fa-solid fa-microphone"></i> Speaking + Writing</span>
                    <span><i class="fa-solid fa-user-graduate"></i> Mentor theo sát</span>
                </div>
            </div>

            <div class="combo-hero__visual" aria-hidden="true">
                <div class="combo-hero__visual-card">
                    <img src="{{ $heroImage }}" alt="Combo nổi bật" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <section class="combo-highlights">
        <div class="oc-container">
            <ul class="combo-highlights__grid">
                <li>
                    <i class="fa-solid fa-layer-group" aria-hidden="true"></i>
                    <span>Lộ trình từng bước</span>
                    <p>Mỗi combo gồm chuỗi khóa học được sắp xếp theo thứ tự tối ưu giúp bạn tăng điểm nhanh.</p>
                </li>
                <li>
                    <i class="fa-solid fa-chalkboard-teacher" aria-hidden="true"></i>
                    <span>Giảng viên OCC đồng hành</span>
                    <p>Mentor theo sát tiến độ, giải đáp thắc mắc và điều chỉnh kế hoạch học tập kịp thời.</p>
                </li>
                <li>
                    <i class="fa-solid fa-certificate" aria-hidden="true"></i>
                    <span>Cam kết đầu ra</span>
                    <p>Nhận chứng chỉ OCC và lộ trình dự phòng nếu bạn chưa đạt được mục tiêu sau combo.</p>
                </li>
            </ul>
        </div>
    </section>

    @if($spotlightCombos->isNotEmpty())
        <section class="combo-spotlight">
            <div class="oc-container">
                <div class="section__header">
                    <h2>Combo nổi bật tuần này</h2>
                    <p>Lộ trình được đăng ký nhiều nhất trong 7 ngày qua, sẵn sàng giúp bạn bứt tốc điểm số.</p>
                </div>
                <div class="combo-grid combo-grid--spotlight">
                    @foreach($spotlightCombos as $combo)
                        @php
                            $comboInCart = in_array($combo->maGoi, $comboCartIds ?? [], true);
                            $isActive = in_array($combo->maGoi, $activeComboIds ?? [], true);
                            $isPending = in_array($combo->maGoi, $pendingComboIds ?? [], true);
                        @endphp
                        <article class="combo-card combo-card--spotlight" data-combo-id="{{ $combo->maGoi }}">
                            <a class="combo-card__link" href="{{ route('student.combos.show', $combo->slug) }}">
                                <div class="combo-card__image">
                                    <img src="{{ $combo->cover_image_url }}" alt="{{ $combo->tenGoi }}" loading="lazy">
                                    <div class="combo-card__badge">
                                        <i class="fa-solid fa-fire" aria-hidden="true"></i> Được yêu thích
                                    </div>
                                </div>
                                <div class="combo-card__body">
                                    <h3>{{ $combo->tenGoi }}</h3>
                                    <ul class="combo-card__meta">
                                        <li><i class="fa-solid fa-book" aria-hidden="true"></i> {{ $combo->courses_count }} khóa học</li>
                                    </ul>
                                </div>
                            </a>
                            <div class="combo-card__footer">
                                <div class="combo-card__price-block">
                                    <small>Chỉ còn</small>
                                    <strong>{{ number_format($combo->sale_price, 0, ',', '.') }} VND</strong>
                                    @if($combo->saving_amount > 0)
                                        <span class="combo-card__origin">{{ number_format($combo->original_price, 0, ',', '.') }} VND</span>
                                        <span class="combo-card__saving">Tiết kiệm {{ number_format($combo->saving_amount, 0, ',', '.') }} VND</span>
                                    @endif
                                </div>
                                @if($isActive)
                                    <a href="{{ route('student.my-courses') }}" class="combo-card__cta combo-card__cta--activated">
                                        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                                        <span>Đã kích hoạt</span>
                                    </a>
                                @elseif($isPending)
                                    <button type="button" class="combo-card__cta combo-card__cta--pending" disabled aria-disabled="true">
                                        <i class="fa-solid fa-clock" aria-hidden="true"></i>
                                        <span>Chờ kích hoạt</span>
                                    </button>
                                @else
                                    <form method="post" action="{{ route('student.cart.store-combo') }}" class="combo-card__action" data-combo-add-form data-combo-id="{{ $combo->maGoi }}">
                                        @csrf
                                        <input type="hidden" name="combo_id" value="{{ $combo->maGoi }}">
                                        <button
                                            type="submit"
                                            class="combo-card__cta {{ $comboInCart ? 'combo-card__cta--in-cart' : '' }}" data-combo-add-btn data-combo-id="{{ $combo->maGoi }}" data-label-default="Thêm vào giỏ hàng" data-label-added="Đã trong giỏ hàng" data-label-adding="Đang thêm..." data-icon-default="fa-cart-plus" data-icon-added="fa-check" data-icon-adding="fa-spinner fa-spin"
                                            @if($comboInCart) disabled aria-disabled="true" @endif
                                        >
                                            <i class="fa-solid {{ $comboInCart ? 'fa-check' : 'fa-cart-plus' }}" aria-hidden="true" data-combo-add-icon></i>
                                            <span data-combo-add-text>{{ $comboInCart ? 'Đã trong giỏ hàng' : 'Thêm vào giỏ hàng' }}</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="combo-listing">
        <div class="oc-container">
            <div class="section__header">
                <h2>Combo đang mở bán</h2>
                <p>Truy cập ngay sau thanh toán, học linh hoạt trên mọi thiết bị.</p>
            </div>

            @if($availableCombos->isEmpty())
                <div class="combo-empty">
                    <div class="combo-empty__icon"><i class="fa-solid fa-gift"></i></div>
                    <h3>Hiện chưa có combo phù hợp</h3>
                    <p>Thử lại với từ khóa khác hoặc quay lại sau. Chúng tôi sẽ sớm bổ sung ưu đãi mới.</p>
                </div>
            @else
                <div class="combo-grid">
                    @foreach($availableCombos as $combo)
                        @php
                            $comboInCart = in_array($combo->maGoi, $comboCartIds ?? [], true);
                            $isActive = in_array($combo->maGoi, $activeComboIds ?? [], true);
                            $isPending = in_array($combo->maGoi, $pendingComboIds ?? [], true);
                        @endphp
                        <article class="combo-card" data-combo-id="{{ $combo->maGoi }}">
                            <a class="combo-card__link" href="{{ route('student.combos.show', $combo->slug) }}">
                                <div class="combo-card__image">
                                    <img src="{{ $combo->cover_image_url }}" alt="{{ $combo->tenGoi }}" loading="lazy">
                                </div>
                                <div class="combo-card__body">
                                    <h3>{{ $combo->tenGoi }}</h3>
                                    <ul class="combo-card__meta">
                                        <li><i class="fa-solid fa-layer-group" aria-hidden="true"></i> {{ $combo->courses_count }} khóa học</li>
                                    </ul>
                                </div>
                            </a>
                            <div class="combo-card__footer">
                                <div class="combo-card__price-block">
                                    <small>Chỉ còn</small>
                                    <strong>{{ number_format($combo->sale_price, 0, ',', '.') }} VND</strong>
                                    @if($combo->saving_amount > 0)
                                        <span class="combo-card__origin">{{ number_format($combo->original_price, 0, ',', '.') }} VND</span>
                                        <span class="combo-card__saving">Tiết kiệm {{ number_format($combo->saving_amount, 0, ',', '.') }} VND</span>
                                    @endif
                                </div>
                                @if($isActive)
                                    <a href="{{ route('student.my-courses') }}" class="combo-card__cta combo-card__cta--activated">
                                        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                                        <span>Đã kích hoạt</span>
                                    </a>
                                @elseif($isPending)
                                    <button type="button" class="combo-card__cta combo-card__cta--pending" disabled aria-disabled="true">
                                        <i class="fa-solid fa-clock" aria-hidden="true"></i>
                                        <span>Chờ kích hoạt</span>
                                    </button>
                                @else
                                    <form method="post" action="{{ route('student.cart.store-combo') }}" class="combo-card__action" data-combo-add-form data-combo-id="{{ $combo->maGoi }}">
                                        @csrf
                                        <input type="hidden" name="combo_id" value="{{ $combo->maGoi }}">
                                        <button
                                            type="submit"
                                            class="combo-card__cta {{ $comboInCart ? 'combo-card__cta--in-cart' : '' }}" data-combo-add-btn data-combo-id="{{ $combo->maGoi }}" data-label-default="Thêm vào giỏ hàng" data-label-added="Đã trong giỏ hàng" data-label-adding="Đang thêm..." data-icon-default="fa-cart-plus" data-icon-added="fa-check" data-icon-adding="fa-spinner fa-spin"
                                            @if($comboInCart) disabled aria-disabled="true" @endif
                                        >
                                            <i class="fa-solid {{ $comboInCart ? 'fa-check' : 'fa-cart-plus' }}" aria-hidden="true" data-combo-add-icon></i>
                                            <span data-combo-add-text>{{ $comboInCart ? 'Đã trong giỏ hàng' : 'Thêm vào giỏ hàng' }}</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>

                @if(method_exists($availableCombos, 'links'))
                    <div class="combo-pagination">
                        {{ $availableCombos->links() }}
                    </div>
                @endif
            @endif
        </div>
    </section>

    @if($upcomingCombos->isNotEmpty())
        <section class="combo-upcoming">
            <div class="oc-container">
                <div class="combo-section-head">
                    <h2>Combo sắp ra mắt</h2>
                    <p>Đặt lời nhắc để không bỏ lỡ ưu đãi giới hạn.</p>
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
                                <p>{{ \Illuminate\Support\Str::limit($combo->moTa, 160) }}</p>
                            </div>
                            <a class="btn btn--ghost" href="{{ route('student.combos.show', $combo->slug) }}">
                                Xem chi tiết
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection

@push('scripts')
    <script src="{{ asset('js/Student/ajax-forms.js') }}"></script>
    <script src="{{ asset('js/Student/combo-index.js') }}" defer></script>
@endpush
