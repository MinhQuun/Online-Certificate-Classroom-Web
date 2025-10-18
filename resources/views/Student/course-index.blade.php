@extends('layouts.student')

@section('title', 'Khoa hoc')

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
            <h1>Khoi dong lo trinh chung chi truc tuyen</h1>
            <p>Chuong trinh duoc thiet ke boi doi ngu chuyen mon, tap trung vao ky nang thuc hanh va he thong danh gia lien tuc.</p>
            <div class="hero__meta">
                <span>Thu vien tai lieu so</span>
                <span>Mini test tung chuong</span>
                <span>Mentor theo sat</span>
            </div>
        </div>
        <div class="hero__media">
            <img src="{{ $firstCourse && $firstCourse->hinhanh ? asset('Assets/' . $firstCourse->hinhanh) : asset('Assets/logo.png') }}" alt="Khoa hoc noi bat">
        </div>
    </div>
</section>

<section class="section">
    <div class="oc-container">
        <div class="section__header">
            <h2>Khoa hoc dang mo</h2>
            <p>Lo trinh ro rang, tai nguyen phong phu va bai kiem tra cuoi ky giup ban tu tin dat muc tieu chung chi.</p>
        </div>

        <div class="card-grid">
            @if ($courses->isEmpty())
                <p>Chua co khoa hoc.</p>
            @endif

            @foreach ($courses as $course)
                @php
                    $courseImage = $course->hinhanh ? asset('Assets/' . $course->hinhanh) : asset('Assets/logo.png');
                    $startDate = $course->ngayBatDau
                        ? \Carbon\Carbon::parse($course->ngayBatDau)->format('d/m/Y')
                        : 'Dang cap nhat';
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
                            <span>Thoi han: {{ $course->thoiHanNgay }} ngay</span>
                            <span>Khai giang: {{ $startDate }}</span>
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
