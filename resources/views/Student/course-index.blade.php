@extends('layouts.student')

@section('title', 'Trang chủ')

@push('styles')
    @php
        $pageStyle = 'css/Student/pages-courses.css';
        $heroBanners = [
            ['file' => 'Assets/Banner/banner1.png', 'alt' => 'Không gian học chứng chỉ trực tuyến hiện đại'],
            ['file' => 'Assets/Banner/banner2.png', 'alt' => 'Lộ trình học tập cá nhân hóa'],
            ['file' => 'Assets/Banner/banner3.png', 'alt' => 'Cộng đồng mentor đồng hành'],
        ];
    @endphp
    <link rel="stylesheet" href="{{ asset($pageStyle) }}?v={{ student_asset_version($pageStyle) }}">
@endpush

@section('content')
    @php
        $firstCourse = $courses->first();
        $isCategoryView = isset($currentCategory) && $currentCategory;
        $headingTitle = $isCategoryView ? $currentCategory->tenDanhMuc : 'Khóa học nổi bật';
        $headingDescription = $isCategoryView
            ? 'Khám phá các khóa học nổi bật thuộc chuyên đề ' . $currentCategory->tenDanhMuc . '.'
            : 'Lộ trình rõ ràng, tài nguyên phong phú và bài kiểm tra cuối kỳ giúp bạn tự tin đạt mục tiêu chứng chỉ.';
        $groupedCourses = !$courses->isEmpty()
            ? $courses->getCollection()->groupBy(function ($c) {
                return optional($c->category)->tenDanhMuc ?? 'Chua co danh muc';
            })
            : collect();
        $preferredBandOrder = [
            'TOEIC Foundation (405-600)',
            'TOEIC Intermediate (605-780)',
            'TOEIC Advanced (785-990)',
        ];
        if ($groupedCourses->isNotEmpty()) {
            $groupedCourses = $groupedCourses->sortKeysUsing(function ($a, $b) use ($preferredBandOrder) {
                $indexA = array_search($a, $preferredBandOrder, true);
                $indexB = array_search($b, $preferredBandOrder, true);
                $scoreA = $indexA === false ? PHP_INT_MAX : $indexA;
                $scoreB = $indexB === false ? PHP_INT_MAX : $indexB;

                if ($scoreA === $scoreB) {
                    return strnatcasecmp($a, $b);
                }

                return $scoreA <=> $scoreB;
            });
        }
        $bandTotal = $groupedCourses->count();
    @endphp

    <section class="hero hero--courses" data-home-index>
        <div class="oc-container hero__grid">
            <div class="hero__text" data-reveal-from-left>
                <h1>Online Certificate Classroom</h1>
                <p>Chương trình được thiết kế bởi đội ngũ giảng viên có chuyên môn, tập trung vào kỹ năng thực hành và hệ thống đánh giá liên tục.</p>
                <div class="hero__meta">
                    <span>Thư viện tài liệu số</span>
                    <span>Review Exercises từng chương</span>
                    <span>Theo dõi tiến độ</span>
                </div>
            </div>
            <div class="hero__media hero-banner" data-hero-banner data-reveal-from-right>
                <div class="hero-banner__slides">
                    @foreach ($heroBanners as $banner)
                        <div class="hero-banner__slide {{ $loop->first ? 'is-active' : '' }}">
                            <img
                                src="{{ asset($banner['file']) }}"
                                alt="{{ $banner['alt'] }}"
                                loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                            >
                        </div>
                    @endforeach
                </div>
                <div class="hero-banner__dots" role="tablist" aria-label="Chuyển banner khóa học">
                    @foreach ($heroBanners as $banner)
                        <button
                            type="button"
                            class="hero-banner__dot {{ $loop->first ? 'is-active' : '' }}"
                            data-hero-banner-dot
                            aria-label="Xem banner {{ $loop->iteration }}"
                            aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                        ></button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="oc-container">
            <div
                class="courses-heading-bar"
                data-sticky-heading
                data-heading-default-title="{{ $headingTitle }}"
            >
                <div class="section__header">
                    <h2 data-heading-title>{{ $headingTitle }}</h2>
                    <p>{{ $headingDescription }}</p>
                </div>

                <div class="courses-heading-bar__status" data-heading-indicator>
                    <span class="courses-heading-bar__status-label">
                        <i class="fa-regular fa-compass" aria-hidden="true"></i>
                        Đang xem
                    </span>
                    <span class="courses-heading-bar__status-value" data-heading-indicator-text>
                        {{ $headingTitle }}
                    </span>
                </div>

                @if ($bandTotal > 1)
                    <div
                        class="course-band-nav"
                        data-course-band-nav
                        data-band-total="{{ $bandTotal }}"
                    >
                        <div class="course-band-nav__headline">
                            <span class="course-band-nav__label">
                                <i class="fa-solid fa-list-ul" aria-hidden="true"></i>
                                Mục lục danh mục
                            </span>
                            <span class="course-band-nav__progress" aria-live="polite">
                                <span data-course-band-progress-current>1</span>
                                <span aria-hidden="true">/</span>
                                <span data-course-band-progress-total>{{ $bandTotal }}</span>
                            </span>
                        </div>
                        <div class="course-band-nav__items" role="tablist" aria-label="Đi tới danh mục khóa học">
                            @foreach ($groupedCourses as $bandName => $groupCourses)
                                <button
                                    type="button"
                                    class="course-band-nav__item {{ $loop->first ? 'is-active' : '' }}"
                                    data-band-target="{{ $bandName }}"
                                    data-band-index="{{ $loop->iteration }}"
                                    @if($loop->first) aria-current="true" @endif
                                >
                                    <span class="course-band-nav__item-label">{{ $bandName }}</span>
                                    <span class="course-band-nav__count">{{ $groupCourses->count() }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            @if ($courses->isEmpty())
                <div class="empty-state">
                    <div class="empty-state__icon">Chưa có khóa học</div>
                    <p class="empty-state__description">Hiện tại chưa có khóa học nào trong danh mục này. Vui lòng quay lại sau.</p>
                </div>
            @else
                @foreach ($groupedCourses as $bandName => $groupCourses)
                    @php
                        $bandAnchor = \Illuminate\Support\Str::slug($bandName) ?: 'band-' . $loop->iteration;
                    @endphp
                    <section
                        id="band-{{ $bandAnchor }}"
                        class="course-band {{ $loop->first ? 'course-band--initial' : '' }}"
                        data-band="{{ $bandName }}"
                        data-band-title="{{ $bandName }}"
                        data-band-index="{{ $loop->iteration }}"
                        @unless($loop->first) data-reveal-on-scroll @endunless
                    >
                        <h3 class="course-band__title">
                            <span class="course-band__title-text">{{ $bandName }}</span>
                            <span class="course-band__count">({{ $groupCourses->count() }} khóa học)</span>
                        </h3>
                        <div class="card-grid">
                            @foreach ($groupCourses as $course)
                                @php
                                    $isFirstBand = optional($loop->parent)->first ?? false;
                                    $inCart = in_array($course->maKH, $cartIds ?? [], true);
                                    $isActive = in_array($course->maKH, $activeCourseIds ?? [], true);
                                    if ($isActive) {
                                        $inCart = false;
                                    }
                                    $ctaClass = $isActive ? 'course-card__cta--active' : ($inCart ? 'course-card__cta--in-cart' : '');
                                    $promotion = $course->active_promotion;
                                    $hasPromotion = $course->saving_amount > 0;
                                    $promotionLabel = $promotion?->tenKM;
                                    $promotionEnds = $promotion && $promotion->ngayKetThuc
                                        ? optional($promotion->ngayKetThuc)->format('d/m')
                                        : null;
                                @endphp

                                {{-- CARD KHÔNG CÓ <a> WRAPPER --}}
                                <article
                                    class="course-card {{ $hasPromotion ? 'course-card--has-promo' : '' }}"
                                    {{ $isFirstBand ? '' : 'data-reveal-scale' }}
                                    data-course-id="{{ $course->maKH }}"
                                    data-course-slug="{{ $course->slug }}"
                                >
                                    <div class="course-card__category">
                                        <span class="chip chip--category">{{ optional($course->category)->tenDanhMuc ?? 'Chương trình nổi bật' }}</span>
                                    </div>

                                    <div class="course-card__media">
                                        <div class="course-card__thumb">
                                            <img src="{{ $course->cover_image_url }}" alt="{{ $course->tenKH }}" loading="lazy">
                                        </div>
                                        <div class="course-card__media-meta">
                                            <span class="course-card__media-tag {{ $hasPromotion ? 'is-promo' : '' }}">
                                                <i class="fa-solid fa-gift" aria-hidden="true"></i>
                                                {{ $hasPromotion ? ($promotionLabel ?? 'Ưu đãi đang diễn ra') : 'Giá niêm yết ổn định' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="course-card__body">
                                        <h3>
                                            <a href="{{ route('student.courses.show', $course->slug) }}" class="course-card__title-link">
                                                {{ $course->tenKH }}
                                            </a>
                                        </h3>
                                        @php
                                            $ratingValue = $course->rating_avg ?? $course->average_rating ?? null;
                                            $ratingCount = (int) ($course->rating_count ?? $course->total_reviews ?? 0);
                                            $ratingDisplay = $ratingValue !== null
                                                ? number_format((float) $ratingValue, 1, ',', '.')
                                                : '--';
                                        @endphp
                                        <div class="course-card__rating" aria-label="Danh gia {{ $ratingDisplay }} tren 5 tu {{ $ratingCount }} luot">
                                            <i class="fa-solid fa-star course-card__rating-icon" aria-hidden="true"></i>
                                            <span class="course-card__rating-value">{{ $ratingDisplay }}</span>
                                            @if ($ratingCount > 0)
                                                @php
                                                    $ratingCountCompact = $ratingCount >= 1_000_000
                                                        ? rtrim(rtrim(number_format($ratingCount / 1_000_000, 1, ',', '.'), '0'), ',') . 'm'
                                                        : ($ratingCount >= 1_000
                                                            ? rtrim(rtrim(number_format($ratingCount / 1_000, 1, ',', '.'), '0'), ',') . 'k'
                                                            : number_format($ratingCount, 0, ',', '.'));
                                                @endphp

                                                <span class="course-card__rating-count">({{ $ratingCountCompact }} lượt đánh giá)</span>
                                            @else
                                                <span class="course-card__rating-count">Chưa có đánh giá</span>
                                            @endif
                                        </div>
                                        <p class="course-card__promo-note {{ $hasPromotion ? 'is-active' : '' }}">
                                            <i class="fa-regular {{ $hasPromotion ? 'fa-clock' : 'fa-circle-check' }}" aria-hidden="true"></i>
                                            <span>
                                                @if ($hasPromotion && $promotionEnds)
                                                    Ưu đãi đến {{ $promotionEnds }}
                                                @elseif ($hasPromotion)
                                                    Ưu đãi giới hạn số lượng
                                                @else
                                                    Giá niêm yết ổn định toàn khóa
                                                @endif
                                            </span>
                                        </p>

                                        <div class="course-card__footer">
                                            <div class="course-card__price-block {{ $hasPromotion ? 'course-card__price-block--promo' : '' }}">
                                                <div class="course-card__price-label">
                                                    <span>{{ $hasPromotion ? 'Chỉ còn' : 'Học phí' }}</span>

                                                </div>
                                                <div class="course-card__price-value">
                                                    {{ number_format($course->sale_price, 0, ',', '.') }} VND
                                                </div>
                                                <div class="course-card__price-meta">
                                                    @if ($hasPromotion)
                                                        <span class="course-card__origin">{{ number_format($course->original_price, 0, ',', '.') }} VND</span>
                                                        <span class="course-card__saving">
                                                            <i class="fa-solid fa-arrow-trend-down" aria-hidden="true"></i>
                                                            Tiết kiệm {{ number_format($course->saving_amount, 0, ',', '.') }} VND
                                                        </span>
                                                    @else
                                                        <span class="course-card__note">
                                                            Bao gồm tài liệu & mentor đồng hành
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            @php
                                                $ariaLabel = $isActive
                                                    ? 'Đã sở hữu'
                                                    : ($inCart
                                                        ? 'Đã trong giỏ hàng'
                                                        : 'Thêm ' . $course->tenKH . ' vào giỏ hàng'
                                                    );
                                            @endphp

                                            <button
                                                type="button"
                                                class="course-card__cta {{ $ctaClass }}"
                                                @if($isActive || $inCart) disabled @endif
                                                data-add-to-cart="{{ $course->maKH }}"
                                                data-cart-adding-label="Đang thêm..."
                                                data-cart-added-label="Đã trong giỏ hàng"
                                                aria-label="{{ $ariaLabel }}"
                                            >
                                                {{ $isActive ? 'Đã sở hữu' : ($inCart ? 'Đã trong giỏ hàng' : 'Thêm vào giỏ hàng') }}
                                            </button>
                                        </div>
                                    </div>
                                </article>

                                {{-- FORM ẨN: NẰM NGOÀI CARD --}}
                                @if (!$isActive && !$inCart)
                                    <form method="post" action="{{ route('student.cart.store') }}" class="cart-form d-none" data-course-id="{{ $course->maKH }}">
                                        @csrf
                                        <input type="hidden" name="course_id" value="{{ $course->maKH }}">
                                    </form>
                                @endif
                            @endforeach
                        </div>
                    </section>
                @endforeach
            @endif

            <div class="pagination">
                @include('components.pagination', [
                    'paginator' => $courses->withQueryString(),
                    'ariaLabel' => 'Điều hướng danh sách khóa học',
                    'containerClass' => '',
                ])
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/Student/hero-banner.js') }}" defer></script>
    <script src="{{ asset('js/Student/ajax-forms.js') }}" defer></script>
    <script src="{{ asset('js/Student/course-index.js') }}" defer></script>
    <script src="{{ asset('js/Student/home-index.js') }}" defer></script>
@endpush
