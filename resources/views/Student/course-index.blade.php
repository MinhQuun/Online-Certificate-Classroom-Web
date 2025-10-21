@extends('layouts.student')

@section('title', 'Trang chủ')

@push('styles')
    @php
        $pageStyle = 'css/Student/pages-courses.css';
        $heroBanners = [
            ['file' => 'Assets/Banner/banner1.png', 'alt' => 'Không gian học chứng chỉ trực tuyến hiện đại'],
            ['file' => 'Assets/Banner/banner2.png', 'alt' => 'Lộ trình học tập cá nhân hoá'],
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
                <h2>Khóa học đang mở</h2>
                <p>Lộ trình rõ ràng, tài nguyên phong phú và bài kiểm tra cuối kỳ giúp bạn tự tin đạt mục tiêu chứng chỉ.</p>
            </div>

            <div class="card-grid">
                @if ($courses->isEmpty())
                    <p>Chưa có khóa học.</p>
                @endif

                @foreach ($courses as $course)
                    @php
                        $startDate = $course->start_date_label;
                    @endphp
                    <article class="course-card">
                        <div class="course-card__media">
                            <a href="{{ route('student.courses.show', $course->slug) }}" class="course-card__thumb">
                                <img src="{{ $course->cover_image_url }}" alt="{{ $course->tenKH }}">
                            </a>

                            <div class="course-card__actions">
                                <a href="{{ route('student.courses.show', $course->slug) }}" class="btn-action btn-action--primary">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M2 2H3.5L4.5 4H14L12 10H5L3 2H1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <circle cx="6" cy="13" r="1" fill="currentColor"/>
                                        <circle cx="11" cy="13" r="1" fill="currentColor"/>
                                    </svg>
                                    Mua Ngay
                                </a>
                                <a href="{{ route('student.courses.show', $course->slug) }}" class="btn-action btn-action--secondary">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M8 3C4.5 3 2 8 2 8C2 8 4.5 13 8 13C11.5 13 14 8 14 8C14 8 11.5 3 8 3Z" stroke="currentColor" stroke-width="1.5"/>
                                        <circle cx="8" cy="8" r="2" stroke="currentColor" stroke-width="1.5"/>
                                    </svg>
                                    Chi tiết
                                </a>
                            </div>
                        </div>
                        <div class="course-card__body">
                            <h3><a href="{{ route('student.courses.show', $course->slug) }}">{{ $course->tenKH }}</a></h3>
                            <span class="course-card__price">{{ number_format((float) $course->hocPhi, 0, ',', '.') }} VNĐ</span>
                        </div>
                    </article>
                @endforeach
            </div>

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

