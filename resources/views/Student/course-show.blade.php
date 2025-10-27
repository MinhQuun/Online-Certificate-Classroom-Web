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
        $isAuthenticated = isset($isAuthenticated) ? (bool)$isAuthenticated : (bool)Auth::check();
        $isEnrolled = isset($isEnrolled) ? (bool)$isEnrolled : false;

        // Identify the first chapter, first lesson, and first mini test for free preview
        $firstChapter = $course->chapters->sortBy('thuTu')->first();
        $firstLesson = optional($firstChapter)->lessons->sortBy('thuTu')->first();
        $firstMiniTest = optional($firstChapter)->miniTests->sortBy('thuTu')->first();
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

    <!-- Access Flags for client gating -->
    <div id="courseAccessFlags" data-authenticated="{{ $isAuthenticated ? '1' : '0' }}" data-enrolled="{{ $isEnrolled ? '1' : '0' }}" hidden></div>

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
                                                $isFreeLesson = $firstLesson && $lesson->maBH === $firstLesson->maBH;
                                                $labelClass = $isFreeLesson ? 'label--free' : 'label--paid';
                                                $labelText = $isFreeLesson ? 'Free' : 'Paid';
                                            @endphp
                                            <li class="lesson-item">
                                                <span class="label {{ $labelClass }}">{{ $labelText }}</span>
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
                                                @php
                                                    // Kiểm tra xem có phải là mini test đầu tiên của chương 1 không
                                                    $isFreeMiniTest = $firstMiniTest && 
                                                                     $miniTest->id === $firstMiniTest->id && 
                                                                     $chapter->thuTu === 1 &&
                                                                     $miniTest->thuTu === 1;
                                                    $labelClass = $isFreeMiniTest ? 'label--free' : 'label--paid';
                                                    $labelText = $isFreeMiniTest ? 'Free' : 'Paid';
                                                @endphp
                                                <article class="mini-test-card">
                                                    <span class="label {{ $labelClass }}">{{ $labelText }}</span>
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
                                        @php
                                            $labelClass = 'label--paid';
                                            $labelText = 'Paid';
                                        @endphp
                                        <article class="final-test-card">
                                            <span class="label {{ $labelClass }}">{{ $labelText }}</span>
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

    <!-- Related Courses Section -->
    @if ($relatedCourses->count())
        <section class="section">
            <div class="oc-container">
                <div class="section__header">
                    <h2>Khóa học gợi ý</h2>
                    <p>Các khóa học khác cùng band "{{ optional($course->category)->tenDanhMuc ?? 'Chưa có danh mục' }}" có thể phù hợp với bạn.</p>
                </div>

                <div class="card-grid">
                    @foreach ($relatedCourses as $related)
                        @php
                            $categoryName = optional($related->category)->tenDanhMuc ?? 'Chương trình nổi bật';
                            $inCart = in_array($related->maKH, $cartIds ?? [], true);
                        @endphp
                        <article class="course-card">
                            <div class="course-card__category">
                                <span class="chip chip--category">{{ $categoryName }}</span>
                            </div>
                            <a href="{{ route('student.courses.show', $related->slug) }}" class="course-card__thumb">
                                <img src="{{ $related->cover_image_url }}" alt="{{ $related->tenKH }}" loading="lazy">
                            </a>
                            <div class="course-card__body">
                                <h3><a href="{{ route('student.courses.show', $related->slug) }}">{{ $related->tenKH }}</a></h3>
                                <div class="course-card__footer">
                                    <div class="course-card__price-block">
                                        <strong>{{ number_format((float) $related->hocPhi, 0, ',', '.') }} VNĐ</strong>
                                    </div>
                                    <form method="post" action="{{ route('student.cart.store') }}">
                                        @csrf
                                        <input type="hidden" name="course_id" value="{{ $related->maKH }}">
                                        <button
                                            type="submit"
                                            class="course-card__cta"
                                            @if($inCart) disabled aria-disabled="true" @endif
                                        >
                                            {{ $inCart ? 'Đã trong giỏ hàng' : 'Thêm vào giỏ hàng' }}
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
@endsection

@push('scripts')
    <script src="{{ asset('js/Student/course-show.js') }}" defer></script>
@endpush

<!-- Enroll Prompt Modal -->
<div class="modal fade" id="enrollPromptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 800px;">  <!-- Tăng max-width lên 800px -->
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Đăng ký để mở khóa nội dung</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Course Image & Basic Info -->
                <div class="d-flex gap-4 mb-4">
                    <div class="course-image-wrapper" style="min-width: 320px; padding: 0;">  
                        <!-- Thêm background color và border-radius cho wrapper -->
                        <img 
                            src="{{ $course->cover_image_url }}" 
                            alt="{{ $course->tenKH }}" 
                            style="width: 100%; height: 200px; border-radius: 20px; object-fit: contain;"
                            
                        >
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="fs-5 fw-bold mb-3">{{ $course->tenKH }}</h6>
                        <p class="text-muted mb-4" style="font-size: 0.95rem; line-height: 1.6;">
                            {{ Str::limit($course->moTa, 200) }}  <!-- Tăng limit của mô tả -->
                        </p>
                        
                        <div class="d-flex gap-3 mb-4">
                            <div class="px-3 py-2 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">Thời hạn</small>
                                <strong>{{ $course->thoiHanNgay }} ngày</strong>
                            </div>
                            <div class="px-3 py-2 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">Chương học</small>
                                <strong>{{ $course->chapters->count() }} chương</strong>
                            </div>
                        </div>

                        <div class="mb-2">
                            <strong class="fs-3 text-primary">{{ number_format((float) $course->hocPhi, 0, ',', '.') }} VNĐ</strong>
                        </div>
                    </div>
                </div>

                <!-- Course Benefits - Styled better -->
                <div class="border rounded-4 p-4 bg-light">
                    <h6 class="fw-bold mb-3">Quyền lợi khi đăng ký khóa học:</h6>
                    <ul class="list-unstyled mb-0 row row-cols-2">  <!-- Chia 2 cột -->
                        <li class="d-flex align-items-center gap-2 mb-3 col">
                            <i class="fas fa-check-circle text-success"></i>
                            <span>Tài liệu định dạng sẵn</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-3 col">
                            <i class="fas fa-check-circle text-success"></i>
                            <span>Mini test từng chương</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 mb-3 col">
                            <i class="fas fa-check-circle text-success"></i>
                            <span>Final test tổng hợp</span>
                        </li>
                        <li class="d-flex align-items-center gap-2 col">
                            <i class="fas fa-check-circle text-success"></i>
                            <span>Chứng chỉ hoàn thành</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                <form method="post" action="{{ route('student.cart.store') }}">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->maKH }}">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Thêm vào giỏ hàng
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>