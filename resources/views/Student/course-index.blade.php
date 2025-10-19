@extends('layouts.student')

@section('title', 'Khóa học')

@push('styles')
    @php
        $pageStyle = 'css/Student/pages-courses.css';
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
            {{-- <div class="hero__media">
                <img src="{{ $firstCourse && $firstCourse->hinhanh ? asset('Assets/' . $firstCourse->hinhanh) : asset('Assets/logo.png') }}" alt="Khóa học nổi bật">
            </div> --}}
            <div class="hero__media">
                <img src="{{ asset('Assets/Duy4.jpg') }}" alt="Khoá học nổi bật">
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
                        $courseImage = $course->hinhanh ? asset('Assets/' . $course->hinhanh) : asset('Assets/logo.png');
                        $startDate = $course->ngayBatDau
                            ? \Carbon\Carbon::parse($course->ngayBatDau)->format('d/m/Y')
                            : 'Đang cập nhật';
                    @endphp
                    <article class="course-card">
                        <a href="{{ route('student.courses.show', $course->slug) }}" class="course-card__thumb">
                            <img src="{{ $courseImage }}" alt="{{ $course->tenKH }}">
                            <span class="course-card__price">{{ number_format((float) $course->hocPhi, 0, ',', '.') }}&#8363;</span>
                        </a>
                        <div class="course-card__body">
                            <h3><a href="{{ route('student.courses.show', $course->slug) }}">{{ $course->tenKH }}</a></h3>
                            <p>{{ $course->moTa }}</p>
                            <div class="course-card__meta">
                                <span>Thời hạn: {{ $course->thoiHanNgay }} ngày</span>
                                <span>Khai giảng: {{ $startDate }}</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="pagination">
                {{ $courses->withQueryString()->links() }}
            </div>
        </div>
    </section>
@endsection