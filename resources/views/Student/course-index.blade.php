{{-- resources/views/student/course-index.blade.php --}}
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
    @endphp

    <section class="hero hero--courses" data-home-index>
        <div class="oc-container hero__grid">
            <div class="hero__text" data-reveal-from-left>
                <h1>Khởi động lộ trình chứng chỉ trực tuyến</h1>
                <p>Chương trình được thiết kế bởi đội ngũ chuyên môn, tập trung vào kỹ năng thực hành và hệ thống đánh giá liên tục.</p>
                <div class="hero__meta">
                    <span>Thư viện tài liệu số</span>
                    <span>Review Exercises từng chương</span>
                    <span>Mentor theo sát</span>
                </div>
            </div>
            <div class="hero__media hero-banner" data-hero-banner data-reveal-from-right>
                <div class="hero-banner__slides">
                    @foreach ($heroBanners as $banner)
                        <div class="hero-banner__slide {{ $loop->first ? 'is-active' : '' }}">
                            <img src="{{ asset($banner['file']) }}" alt="{{ $banner['alt'] }}">
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
            <div class="section__header">
                @if (isset($currentCategory) && $currentCategory)
                    <h2>{{ $currentCategory->tenDanhMuc }}</h2>
                    <p>Khám phá các khóa học nổi bật thuộc chuyên đề {{ $currentCategory->tenDanhMuc }}.</p>
                @else
                    <h2>Tất cả khóa học</h2>
                    <p>Lộ trình rõ ràng, tài nguyên phong phú và bài kiểm tra cuối kỳ giúp bạn tự tin đạt mục tiêu chứng chỉ.</p>
                @endif
            </div>

            @if ($courses->isEmpty())
                <div class="empty-state">
                    <div class="empty-state__icon">Sách</div>
                    <h3 class="empty-state__title">Chưa có khóa học</h3>
                    <p class="empty-state__description">Hiện tại chưa có khóa học nào trong danh mục này. Vui lòng quay lại sau.</p>
                </div>
            @else
                @php
                    $grouped = $courses->getCollection()->groupBy(function ($c) {
                        return optional($c->category)->tenDanhMuc ?? 'Chưa có danh mục';
                    });
                @endphp

                @foreach ($grouped as $bandName => $groupCourses)
                    <section class="course-band" data-band="{{ $bandName }}" data-reveal-on-scroll>
                        <h3 class="course-band__title">
                            <span class="course-band__title-text">{{ $bandName }}</span>
                            <span class="course-band__count">({{ $groupCourses->count() }} khóa học)</span>
                        </h3>
                        <div class="card-grid">
                            @foreach ($groupCourses as $course)
                                @php
                                    $inCart = in_array($course->maKH, $cartIds ?? [], true);
                                    $isActive = in_array($course->maKH, $activeCourseIds ?? [], true);
                                    $isPending = in_array($course->maKH, $pendingCourseIds ?? [], true);
                                    if ($isActive || $isPending) {
                                        $inCart = false;
                                    }
                                    $ctaClass = $isActive ? 'course-card__cta--active' : ($isPending ? 'course-card__cta--pending' : ($inCart ? 'course-card__cta--in-cart' : ''));
                                    $promotion = $course->active_promotion;
                                    $hasPromotion = $course->saving_amount > 0;
                                    $promotionLabel = $promotion?->tenKM;
                                    $promotionEnds = $promotion && $promotion->ngayKetThuc
                                        ? optional($promotion->ngayKetThuc)->format('d/m')
                                        : null;
                                @endphp

                                {{-- CARD KHÔNG CÓ <a> WRAPPER --}}
                                <article class="course-card {{ $hasPromotion ? 'course-card--has-promo' : '' }}" data-reveal-scale data-course-id="{{ $course->maKH }}" data-course-slug="{{ $course->slug }}">
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
                                                    <span class="course-card__price-pill {{ $hasPromotion ? 'is-promo' : '' }}">
                                                        {{ $hasPromotion ? 'Đã giảm ' . $course->saving_percent . '%' : 'Ổn định' }}
                                                    </span>
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

                                            {{-- NÚT CTA: RIÊNG BIỆT, KHÔNG NẰM TRONG <a> --}}
                                            <button
                                                type="button"
                                                class="course-card__cta {{ $ctaClass }}"
                                                @if($isActive || $isPending || $inCart) disabled @endif
                                                data-add-to-cart="{{ $course->maKH }}"
                                                aria-label="{{ $isActive ? 'Đã kích hoạt' : ($isPending ? 'Chờ kích hoạt' : ($inCart ? 'Đã trong giỏ hàng' : 'Thêm ' . $course->tenKH . ' vào giỏ hàng')) }}"
                                            >
                                                {{ $isActive ? 'Đã kích hoạt' : ($isPending ? 'Chờ kích hoạt' : ($inCart ? 'Đã trong giỏ hàng' : 'Thêm vào giỏ hàng')) }}
                                            </button>
                                        </div>
                                    </div>
                                </article>

                                {{-- FORM ẨN: NẰM NGOÀI CARD --}}
                                @if (!$isActive && !$isPending && !$inCart)
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
    <script src="{{ asset('js/Student/ajax-forms.js') }}"></script>
    <script src="{{ asset('js/Student/home-index.js') }}"></script>

    {{-- JS XỬ LÝ NÚT THÊM GIỎ HÀNG & CARD CLICK --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Xử lý button thêm giỏ hàng
            document.querySelectorAll('[data-add-to-cart]').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    if (this.disabled) return;

                    const courseId = this.getAttribute('data-add-to-cart');
                    const form = document.querySelector(`.cart-form[data-course-id="${courseId}"]`);
                    if (form) {
                        form.submit();
                    }
                });
            });

            // Xử lý click toàn card (ngoại trừ button & link)
            document.querySelectorAll('.course-card').forEach(card => {
                card.addEventListener('click', function(e) {
                    // Bỏ qua click trên button hoặc link
                    if (e.target.tagName === 'BUTTON' || e.target.tagName === 'A' || e.target.closest('a') || e.target.closest('button')) {
                        return;
                    }

                    const slug = this.getAttribute('data-course-slug');
                    if (slug) {
                        window.location.href = `/student/courses/${slug}`;
                    }
                });

                // Thêm cursor pointer
                card.style.cursor = 'pointer';
            });
        });
    </script>
@endpush