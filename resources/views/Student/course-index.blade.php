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

    <section class="hero hero--courses">
        <div class="oc-container hero__grid">
            <div class="hero__text">
                <h1>Khởi động lộ trình chứng chỉ trực tuyến</h1>
                <p>Chương trình được thiết kế bởi đội ngũ chuyên môn, tập trung vào kỹ năng thực hành và hệ thống đánh giá liên tục.</p>
                <div class="hero__meta">
                    <span>Thư viện tài liệu số</span>
                    <span>Mini test từng chương</span>
                    <span>Mentor theo sát</span>
                </div>
            </div>
            <div class="hero__media hero-banner" data-hero-banner>
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
                    <div class="empty-state__icon">📚</div>
                    <h3 class="empty-state__title">Chưa có khóa học</h3>
                    <p class="empty-state__description">Hiện tại chưa có khóa học nào trong danh mục này. Vui lòng quay lại sau.</p>
                </div>
            @else
                @php
                    // Nhóm các khóa học theo tên danh mục (thường chứa thông tin band)
                    $grouped = $courses->getCollection()->groupBy(function ($c) {
                        return optional($c->category)->tenDanhMuc ?? 'Chưa có danh mục';
                    });
                @endphp

                @foreach ($grouped as $bandName => $groupCourses)
                    <section class="course-band" data-band="{{ $bandName }}">
                        <h3 class="course-band__title">
                            <span class="course-band__title-text">{{ $bandName }}</span>
                            <span class="course-band__count">({{ $groupCourses->count() }} khóa học)</span>
                        </h3>
                        <div class="card-grid">
                            @foreach ($groupCourses as $course)
                                @php
                                    $categoryName = optional($course->category)->tenDanhMuc ?? 'Chương trình nổi bật';
                                    $inCart = in_array($course->maKH, $cartIds ?? [], true);
                                    $isActive = in_array($course->maKH, $activeCourseIds ?? [], true);
                                    $isPending = in_array($course->maKH, $pendingCourseIds ?? [], true);
                                    if ($isActive || $isPending) {
                                        $inCart = false;
                                    }
                                    $statusLabel = null;
                                    $statusClass = null;
                                    if ($isActive) {
                                        $statusLabel = 'Đã kích hoạt';
                                        $statusClass = 'active';
                                    } elseif ($isPending) {
                                        $statusLabel = 'Chờ kích hoạt';
                                        $statusClass = 'pending';
                                    }
                                    $ctaClass = $isActive ? 'course-card__cta--owned' : ($isPending ? 'course-card__cta--pending' : '');
                                @endphp
                                <article class="course-card">
                                    <div class="course-card__category">
                                        <span class="chip chip--category">{{ $categoryName }}</span>
                                        @if($statusLabel)
                                            <span class="chip chip--status chip--status-{{ $statusClass }}">{{ $statusLabel }}</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('student.courses.show', $course->slug) }}" class="course-card__thumb">
                                        <img src="{{ $course->cover_image_url }}" alt="{{ $course->tenKH }}" loading="lazy">
                                    </a>
                                    <div class="course-card__body">
                                        <h3><a href="{{ route('student.courses.show', $course->slug) }}">{{ $course->tenKH }}</a></h3>
                                        <div class="course-card__footer">
                                            <div class="course-card__price-block">
                                                <strong>{{ number_format((float) $course->hocPhi, 0, ',', '.') }} VNĐ</strong>
                                            </div>
                                            <form method="post" action="{{ route('student.cart.store') }}">
                                                @csrf
                                                <input type="hidden" name="course_id" value="{{ $course->maKH }}">
                                                <button
                                                    type="submit"
                                                    class="course-card__cta {{ $ctaClass }}"
                                                    @if($isActive || $isPending || $inCart) disabled aria-disabled="true" @endif
                                                >
                                                    {{ $isActive ? 'Đã kích hoạt' : ($isPending ? 'Chờ kích hoạt' : ($inCart ? 'Đã trong giỏ hàng' : 'Thêm vào giỏ hàng')) }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </article>
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
@endpush
