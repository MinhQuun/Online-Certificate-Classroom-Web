@extends('layouts.student')

@section('title', $course->tenKH)

@push('styles')
    @php
        $pageStyle = 'css/Student/pages-course-detail.css';
    @endphp
    <link rel="stylesheet" href="{{ asset($pageStyle) }}?v={{ student_asset_version($pageStyle) }}">
@endpush

@section('content')
@php
    $courseCover = $course->hinhanh ? asset('Assets/' . $course->hinhanh) : asset('Assets/logo.png');
    $startDate = $course->ngayBatDau ? \Carbon\Carbon::parse($course->ngayBatDau)->format('d/m/Y') : 'Dang cap nhat';
    $endDate = $course->ngayKetThuc ? \Carbon\Carbon::parse($course->ngayKetThuc)->format('d/m/Y') : 'Dang cap nhat';
@endphp

<section class="course-hero">
    <div class="oc-container course-hero__grid">
        <div class="course-hero__text">
            <span class="chip chip--soft">Lo trinh chung chi</span>
            <h1>{{ $course->tenKH }}</h1>
            <p>{{ $course->moTa }}</p>
            <ul class="course-hero__stats">
                <li><strong>{{ $course->thoiHanNgay }} ngay</strong><span>Thoi han hoc</span></li>
                <li><strong>{{ number_format((float) $course->hocPhi, 0, ',', '.') }}&#8363;</strong><span>Hoc phi</span></li>
                <li><strong>{{ $course->chapters->count() }}</strong><span>Chuong hoc</span></li>
            </ul>
        </div>
        <div class="course-hero__media">
            <img src="{{ $courseCover }}" alt="{{ $course->tenKH }}">
        </div>
    </div>
</section>

<section class="section">
    <div class="oc-container course-layout">
        <div class="course-layout__main">
            <div class="section__header">
                <h2>Noi dung khoa hoc</h2>
                <p>Khoa hoc duoc chia thanh cac chuong voi mini test di kem, giup danh gia tien trinh truoc khi chuyen sang noi dung moi.</p>
            </div>

            @foreach ($course->chapters as $chapter)
                @php
                    $chapterMiniTests = $chapter->miniTests;
                @endphp
                <div class="accordion" data-accordion>
                    <button class="accordion__header" type="button">
                        <div>
                            <span class="accordion__eyebrow">Chuong {{ $chapter->thuTu }}</span>
                            <span class="accordion__title">{{ $chapter->tenChuong }}</span>
                        </div>
                        <span class="accordion__chevron" aria-hidden="true"></span>
                    </button>
                    <div class="accordion__panel">
                        <ul class="lesson-list">
                            @foreach ($chapter->lessons as $lesson)
                                <li>
                                    <a href="{{ route('student.lessons.show', $lesson->maBH) }}">
                                        <span class="lesson-list__title">Bai {{ $lesson->thuTu }}: {{ $lesson->tieuDe }}</span>
                                        <span class="badge">{{ strtoupper($lesson->loai) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        @if ($chapterMiniTests->count())
                            <div class="mini-tests">
                                <h3>Mini test trong chuong</h3>
                                <div class="mini-tests__grid">
                                    @foreach ($chapterMiniTests as $miniTest)
                                        <article class="mini-test-card">
                                            <header>
                                                <span class="chip">Mini test</span>
                                                <h4>{{ $miniTest->title }}</h4>
                                            </header>
                                            <ul class="meta-list">
                                                <li>Thu tu: {{ $miniTest->thuTu }}</li>
                                                <li>Thoi gian: {{ $miniTest->time_limit_min }} phut</li>
                                                <li>Lan lam: {{ $miniTest->attempts_allowed }}</li>
                                            </ul>
                                            <footer>
                                                <span>Max score: {{ $miniTest->max_score }}</span>
                                                <span>Trong so: {{ $miniTest->trongSo }}</span>
                                            </footer>
                                            @if ($miniTest->materials->count())
                                                <div class="resource-list">
                                                    @foreach ($miniTest->materials as $resource)
                                                        <a href="{{ $resource->public_url }}" target="_blank" rel="noopener">
                                                            <span>{{ $resource->tenTL }}</span>
                                                            <span>{{ strtoupper($resource->loai) }}</span>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            @if ($course->finalTests->count())
                <div class="final-tests">
                    <div class="section__header">
                        <h2>Final test khoa hoc</h2>
                        <p>Bo de tong hop danh gia toan dien truoc khi buoc vao ky thi chung chi chinh thuc.</p>
                    </div>
                    <div class="final-tests__grid">
                        @foreach ($course->finalTests as $test)
                            <article class="final-test-card">
                                <header>
                                    <span class="chip chip--accent">Final test</span>
                                    <h3>{{ $test->title }}</h3>
                                </header>
                                <ul class="meta-list">
                                    @if ($test->dotTest)
                                        <li>Dot to chuc: {{ $test->dotTest }}</li>
                                    @endif
                                    <li>Thoi gian: {{ $test->time_limit_min }} phut</li>
                                    <li>Tong so cau hoi: {{ $test->total_questions }}</li>
                                </ul>
                                @if ($test->materials->count())
                                    <div class="resource-list">
                                        @foreach ($test->materials as $resource)
                                            <a href="{{ $resource->public_url }}" target="_blank" rel="noopener">
                                                <span>{{ $resource->tenTL }}</span>
                                                <span>{{ strtoupper($resource->loai) }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <aside class="course-sidebar">
            <div class="course-sidebar__card">
                <div class="course-sidebar__price">{{ number_format((float) $course->hocPhi, 0, ',', '.') }}&#8363;</div>
                <a class="btn btn--primary" href="#">Dang ky ngay</a>
                <ul class="course-sidebar__list">
                    <li>Tai lieu da dinh dang</li>
                    <li>Mini test theo tung chuong</li>
                    <li>Final test tong hop</li>
                </ul>
            </div>
            <div class="course-sidebar__card course-sidebar__card--muted">
                <h4>Thong tin lich hoc</h4>
                <ul>
                    <li>Ngay bat dau: {{ $startDate }}</li>
                    <li>Ngay ket thuc: {{ $endDate }}</li>
                    <li>Ho tro: support@occ.edu.vn</li>
                </ul>
            </div>
        </aside>
    </div>
</section>
@endsection

