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
        $courseCover = $course->cover_image_url;
        $startDate = $course->start_date_label;
        $endDate = $course->end_date_label;
    @endphp

    <!-- Hero Section -->
    <section class="course-hero">
        <div class="oc-container course-hero__grid">
            <div class="course-hero__text">
                <span class="chip chip--soft">🎓 Lộ trình chứng chỉ</span>
                <h1>{{ $course->tenKH }}</h1>
                <p>{{ $course->moTa }}</p>
                <ul class="course-hero__stats">
                    <li>
                        <strong>{{ $course->thoiHanNgay }}</strong>
                        <span>Ngày học</span>
                    </li>
                    <li>
                        <strong>{{ number_format((float) $course->hocPhi, 0, ',', '.') }}₫</strong>
                        <span>Học phí</span>
                    </li>
                    <li>
                        <strong>{{ $course->chapters->count() }}</strong>
                        <span>Chương học</span>
                    </li>
                </ul>
            </div>
            <div class="course-hero__media">
                <img src="{{ $courseCover }}" alt="{{ $course->tenKH }}" loading="lazy">
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="section">
        <div class="oc-container course-layout">
            <!-- Course Content -->
            <div class="course-layout__main">
                <div class="section__header">
                    <h2>📚 Nội dung khóa học</h2>
                    <p>Khóa học được chia thành các chương kèm theo bài kiểm tra nhỏ (mini test), giúp bạn đánh giá tiến độ trước khi chuyển sang nội dung mới.</p>
                </div>

                <!-- Chapters -->
                @foreach ($course->chapters as $index => $chapter)
                    @php
                        $chapterMiniTests = $chapter->miniTests;
                    @endphp
                    <article class="module" data-accordion>
                        <header class="module__header">
                            <button class="module__toggle" type="button" aria-expanded="false">
                                <div class="module__info">
                                    <span class="module__eyebrow">Chương {{ $chapter->thuTu }}</span>
                                    <span class="module__title">{{ $chapter->tenChuong }}</span>
                                </div>
                                <span class="module__chevron" aria-hidden="true"></span>
                            </button>
                        </header>
                        <div class="module__panel">
                            <div class="module__body">
                                <!-- Lessons -->
                                @if ($chapter->lessons->count())
                                    <ul class="lesson-list lesson-list--module">
                                        @foreach ($chapter->lessons as $lesson)
                                            @php
                                                $lessonTypeKey = preg_replace('/[^a-z0-9]+/', '-', strtolower($lesson->loai)) ?: 'default';
                                            @endphp
                                            <li>
                                                <a href="{{ route('student.lessons.show', $lesson->maBH) }}">
                                                    <div class="lesson-list__meta">
                                                        <span class="lesson-list__eyebrow">Bài {{ $lesson->thuTu }}</span>
                                                        <span class="lesson-list__title">{{ $lesson->tieuDe }}</span>
                                                    </div>
                                                    <span class="badge badge--{{ $lessonTypeKey }}">{{ strtoupper($lesson->loai) }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                <!-- Mini Tests -->
                                @if ($chapterMiniTests->count())
                                    <div class="mini-tests">
                                        <div class="mini-tests__head">
                                            <h3>📝 Bài kiểm tra nhỏ</h3>
                                            <span>{{ $chapterMiniTests->count() }} bài</span>
                                        </div>
                                        <div class="mini-tests__grid">
                                            @foreach ($chapterMiniTests as $miniTest)
                                                <article class="mini-test-card">
                                                    <header>
                                                        <span class="chip">Mini test</span>
                                                        <h4>{{ $miniTest->title }}</h4>
                                                    </header>
                                                    <ul class="meta-list meta-list--inline">
                                                        <li>Thứ tự {{ $miniTest->thuTu }}</li>
                                                        <li>{{ $miniTest->time_limit_min }} phút</li>
                                                        <li>{{ $miniTest->attempts_allowed }} lần làm</li>
                                                    </ul>
                                                    <footer>
                                                        <span>Điểm: {{ $miniTest->max_score }}</span>
                                                        <span>Trọng số: {{ $miniTest->trongSo }}</span>
                                                    </footer>
                                                    @if ($miniTest->materials->count())
                                                        <div class="resource-list resource-list--compact">
                                                            @foreach ($miniTest->materials as $resource)
                                                                @php
                                                                    $resTypeKey = preg_replace('/[^a-z0-9]+/', '-', strtolower($resource->loai)) ?: 'default';
                                                                @endphp
                                                                <a href="{{ $resource->public_url }}" target="_blank" rel="noopener noreferrer">
                                                                    <span>{{ $resource->tenTL }}</span>
                                                                    <span class="badge badge--{{ $resTypeKey }}">{{ strtoupper($resource->loai) }}</span>
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
                    </article>
                @endforeach

                <!-- Final Tests -->
                @if ($course->finalTests->count())
                    <article class="module" data-accordion>
                        <header class="module__header">
                            <button class="module__toggle" type="button" aria-expanded="false">
                                <div class="module__info">
                                    <span class="module__eyebrow">Tổng kết</span>
                                    <span class="module__title">🎯 Bài kiểm tra cuối khóa</span>
                                </div>
                                <span class="module__chevron" aria-hidden="true"></span>
                            </button>
                        </header>
                        <div class="module__panel">
                            <div class="module__body">
                                <p class="muted">Bộ đề tổng hợp giúp đánh giá toàn diện trước khi bước vào kỳ thi chứng chỉ chính thức.</p>
                                <div class="final-tests__grid">
                                    @foreach ($course->finalTests as $test)
                                        <article class="final-test-card">
                                            <header>
                                                <span class="chip chip--accent">Final test</span>
                                                <h3>{{ $test->title }}</h3>
                                            </header>
                                            <ul class="meta-list meta-list--inline">
                                                @if ($test->dotTest)
                                                    <li>Đợt {{ $test->dotTest }}</li>
                                                @endif
                                                <li>{{ $test->time_limit_min }} phút</li>
                                                <li>{{ $test->total_questions }} câu hỏi</li>
                                            </ul>
                                            @if ($test->materials->count())
                                                <div class="resource-list">
                                                    @foreach ($test->materials as $resource)
                                                        @php
                                                            $resTypeKey = preg_replace('/[^a-z0-9]+/', '-', strtolower($resource->loai)) ?: 'default';
                                                        @endphp
                                                        <a href="{{ $resource->public_url }}" target="_blank" rel="noopener noreferrer">
                                                            <span>{{ $resource->tenTL }}</span>
                                                            <span class="badge badge--{{ $resTypeKey }}">{{ strtoupper($resource->loai) }}</span>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </article>
                @endif
            </div>

            <!-- Sidebar -->
            <aside class="course-sidebar">
                <!-- Pricing Card -->
                <div class="course-sidebar__card">
                    <div class="course-sidebar__price">{{ number_format((float) $course->hocPhi, 0, ',', '.') }}₫</div>
                    <form method="post" action="{{ route('student.cart.store') }}" class="course-sidebar__cta">
                        @csrf
                        <input type="hidden" name="course_id" value="{{ $course->maKH }}">
                        <button
                            type="submit"
                            class="btn btn--primary"
                            style="text-align: center; padding: 16px 24px; font-weight: 700; font-size: 16px; border-radius: 12px;"
                            @if($isInCart) disabled aria-disabled="true" @endif
                        >
                            {{ $isInCart ? 'Đã trong giỏ hàng' : 'Thêm vào giỏ hàng' }}
                        </button>
                    </form>
                    @if($isInCart)
                        <a class="course-sidebar__link" href="{{ route('student.cart.index') }}">Đến giỏ hàng</a>
                    @endif
                    <ul class="course-sidebar__list">
                        <li>Tài liệu định dạng sẵn</li>
                        <li>Mini test từng chương</li>
                        <li>Final test tổng hợp</li>
                        <li>Chứng chỉ hoàn thành</li>
                    </ul>
                </div>

                <!-- Info Card -->
                <div class="course-sidebar__card course-sidebar__card--muted">
                    <h4>📅 Thông tin lịch học</h4>
                    <ul>
                        <li>Bắt đầu: {{ $startDate }}</li>
                        <li>Kết thúc: {{ $endDate }}</li>
                        <li>Hỗ trợ 24/7</li>
                        <li>support@occ.edu.vn</li>
                    </ul>
                </div>
            </aside>
        </div>
    </section>
@endsection

