@extends('layouts.student')

@section('title', $course->tenKH)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Student/pages-course-detail.css') }}?v={{ student_asset_version('css/Student/pages-course-detail.css') }}">
@endpush

@section('content')
    @php
        $courseCover = $course->cover_image_url;
        $startDate = $course->start_date_label ?? 'Chưa xác định';
        $endDate = $course->end_date_label ?? 'Chưa xác định';

        $firstChapter = $course->chapters->sortBy('thuTu')->first();
        $firstLesson = optional($firstChapter)->lessons->sortBy('thuTu')->first();
        $firstMiniTest = optional($firstChapter)->miniTests->sortBy('thuTu')->first();

        $freeLessonId = $firstLesson?->maBH;
        $freeMiniTestId = null;

        $teacher = $course->teacher;
        $teacherName = trim($teacher->hoTen ?? '') !== '' ? $teacher->hoTen : '(Chưa gán)';
        $teacherSpeciality = trim($teacher->chuyenMon ?? '') !== '' ? $teacher->chuyenMon : 'Đang cập nhật';
        $teacherInitial = strtoupper(substr($teacherName, 0, 1) ?: 'G');

        $averageRating = $ratingSummary['average'] ?? null;
        $totalReviews = (int) ($ratingSummary['total'] ?? 0);

        $studentRatingValue = old('diemSo', $studentReview->diemSo ?? null);
        $studentReviewContent = old('nhanxet', $studentReview->nhanxet ?? '');

        $activePromotion = $course->active_promotion;
        $courseHasPromotion = $course->saving_amount > 0;
        $activePromotionLabel = $activePromotion?->tenKM;
        $activePromotionEnds = $activePromotion && $activePromotion->ngayKetThuc
            ? optional($activePromotion->ngayKetThuc)->format('d/m')
            : null;
        $coursePriceEyebrow = $courseHasPromotion ? 'Chỉ còn' : 'Học phí';
        $coursePricePill = $courseHasPromotion ? 'Đã giảm ' . $course->saving_percent . '%' : 'Ổn định';
        $coursePriceNote = $courseHasPromotion
            ? 'Tiết kiệm ' . number_format($course->saving_amount, 0, ',', '.') . ' VND'
            : 'Bao gồm tài liệu & mentor đồng hành';
        $courseSalePrice = number_format($course->sale_price, 0, ',', '.');
        $courseOriginalPrice = number_format($course->original_price, 0, ',', '.');
    @endphp

    <!-- Hero Section -->
    <section class="course-hero" data-course-detail>
        <div class="oc-container course-hero__grid">
            <div class="course-hero__text" data-reveal-from-left>
                <span class="chip chip--soft">Lộ trình chứng chỉ</span>
                <h1>{{ $course->tenKH }}</h1>
                <p>{{ $course->moTa }}</p>
                <ul class="course-hero__stats">
                    {{-- <li class="course-hero__stat">
                        <strong>{{ $course->thoiHanNgay }}</strong> <span>Ngày học</span>
                    </li> --}}
                    <li
                        class="course-hero__stat course-hero__stat--link"
                        data-course-tab-target="content"
                        role="button"
                        tabindex="0"
                        aria-label="Xem chi tiết {{ $course->chapters->count() }} chương học"
                        aria-controls="course-content"
                    >
                        <strong>{{ $course->chapters->count() }}</strong> <span>Chương học</span>
                    </li>
                    <li
                        class="course-hero__stat course-hero__stat--link"
                        data-course-tab-target="reviews"
                        role="button"
                        tabindex="0"
                        aria-label="Đi tới đánh giá khóa học ({{ $totalReviews }} đánh giá)"
                        aria-controls="course-reviews"
                    >
                        <strong>{{ $averageRating !== null ? number_format((float) $averageRating, 1, ",", ".") : "--" }}
                            <div class="course-rating-summary__stars">
                                @for ($star = 1; $star <= 5; $star++)
                                    @php
                                        $starFill = $averageRating !== null ? max(min($averageRating - ($star - 1), 1), 0) : 0;
                                    @endphp
                                    <i class="@if($starFill >= 1) fas fa-star @elseif($starFill >= 0.5) fas fa-star-half-alt @else far fa-star @endif"></i>
                                @endfor
                            </div>
                        </strong>
                        <span>Đánh giá ({{ $totalReviews }})</span>
                    </li>
                </ul>
            </div>

            <div class="course-hero__actions" data-reveal-from-right>
                <div class="course-price-card {{ $courseHasPromotion ? 'course-price-card--promo' : '' }}">
                    <div class="course-price-card__header">
                        <span class="course-price-card__eyebrow">{{ $coursePriceEyebrow }}</span>
                        <span class="course-price-card__pill {{ $courseHasPromotion ? 'is-promo' : '' }}">{{ $coursePricePill }}</span>
                    </div>
                    <div class="course-price-card__value">{{ $courseSalePrice }} VND</div>
                    <div class="course-price-card__meta">
                        @if ($courseHasPromotion)
                            <span class="course-price-card__origin">{{ $courseOriginalPrice }} VND</span>
                            <span class="course-price-card__saving">
                                <i class="fa-solid fa-arrow-trend-down" aria-hidden="true"></i>
                                {{ $coursePriceNote }}
                            </span>
                            @if ($activePromotionEnds)
                                <span class="course-price-card__expiry">
                                    <i class="fa-regular fa-clock" aria-hidden="true"></i>
                                    Ưu đãi đến {{ $activePromotionEnds }}
                                </span>
                            @endif
                        @else
                            <span class="course-price-card__note">
                                <i class="fa-regular fa-file-lines" aria-hidden="true"></i>
                                {{ $coursePriceNote }}
                            </span>
                        @endif
                    </div>
                </div>

                @php
                    $primaryCtaClass = $isEnrolled
                        ? 'course-card__cta--active'
                        : ($isInCart ? 'course-card__cta--in-cart' : '');
                    $primaryCtaText = $isEnrolled
                        ? 'Đã sở hữu'
                        : ($isInCart ? 'Đã trong giỏ hàng' : 'Thêm vào giỏ hàng');
                    $primaryCtaAria = $isEnrolled
                        ? 'Bạn đã sở hữu khóa học này'
                        : ($isInCart
                            ? 'Khóa học đã trong giỏ hàng'
                            : 'Thêm ' . $course->tenKH . ' vào giỏ hàng');
                @endphp

                <div class="course-hero__cta">
                    <button
                        type="button"
                        class="course-card__cta course-card__cta--hero {{ $primaryCtaClass }}"
                        data-add-to-cart="{{ $course->maKH }}"
                        data-cart-adding-label="Đang thêm..."
                        data-cart-added-label="Đã trong giỏ hàng"
                        aria-label="{{ $primaryCtaAria }}"
                        @if($isEnrolled || $isInCart) disabled aria-disabled="true" @endif
                    >
                        {{ $primaryCtaText }}
                    </button>

                    @if($isEnrolled)
                        <p class="course-hero__cta-note">
                            Bạn đã sở hữu khóa học này. Tất cả tài nguyên đã được mở khóa.
                        </p>
                    @elseif($isInCart)
                        <a class="course-hero__cta-link" href="{{ route('student.cart.index') }}">
                            Đến giỏ hàng để thanh toán
                        </a>
                    @else
                        <p class="course-hero__cta-note">
                            OCC hoàn tiền 100% nếu bạn không hài lòng trong 7 ngày đầu.
                        </p>
                    @endif
                </div>

                <ul class="course-hero__assurance">
                    <li>
                        <i class="fa-solid fa-book-open" aria-hidden="true"></i>
                        Tài liệu được cập nhật hằng tuần
                    </li>
                    <li>
                        <i class="fa-solid fa-headset" aria-hidden="true"></i>
                        Mentor OCC đồng hành trong suốt lộ trình
                    </li>
                    <li>
                        <i class="fa-solid fa-certificate" aria-hidden="true"></i>
                        Nhận chứng chỉ hoàn thành được OCC xác thực
                    </li>
                </ul>
            </div>

            @if(!$isEnrolled && !$isInCart)
                <form method="post" action="{{ route('student.cart.store') }}" class="cart-form d-none" data-course-id="{{ $course->maKH }}">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->maKH }}">
                </form>
            @endif
        </div>
    </section>

    <!-- Access Flags -->
    <div id="courseAccessFlags"
         data-authenticated="{{ $isAuthenticated ? '1' : '0' }}"
         data-enrolled="{{ $isEnrolled ? '1' : '0' }}"
         data-free-lesson="{{ $freeLessonId ?? '' }}"
         data-free-minitest="{{ $freeMiniTestId ?? '' }}"
         hidden></div>

    <!-- Tab Switcher -->
    <section class="section section--course-tabs" aria-label="Điều hướng nội dung khóa học">
        <div class="oc-container">
            <div class="course-tab-switcher" data-course-tab-wrapper>
                <button
                    type="button"
                    class="course-tab-switcher__btn is-active"
                    data-course-tab-trigger="content"
                    aria-controls="course-content"
                    aria-selected="true"
                >
                    Chương học
                </button>
                <button
                    type="button"
                    class="course-tab-switcher__btn"
                    data-course-tab-trigger="reviews"
                    aria-controls="course-reviews"
                    aria-selected="false"
                >
                    Đánh giá
                </button>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section id="course-content" class="section course-tab-panel is-active" data-course-tab-panel="content">
        <div class="oc-container course-layout">
            @if (!$isEnrolled)
                <div id="lockedNotice" class="course-locked-notice" role="alert" hidden>
                    <div class="course-locked-notice__content">
                        <strong>Khóa học chưa được mở.</strong>
                        <span>Bạn cần đăng ký khóa học để học toàn bộ nội dung.</span>
                    </div>
                    <button type="button" class="course-locked-notice__close" aria-label="Đóng thông báo">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Course Content -->
            <div class="course-layout__main">
                <div class="section__header" data-reveal-on-scroll>
                    <h2>Nội dung khóa học</h2>
                    <p>Khóa học được chia thành các chương kèm theo bài ôn luyện (review exercises), giúp bạn đánh giá tiến độ trước khi chuyển sang nội dung mới.</p>
                </div>

                <!-- Chapters -->
                @foreach ($course->chapters as $chapter)
                    <article class="module" data-accordion data-accordion-autopen="false" data-reveal-on-scroll>
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
                                                $isFreeLesson = $freeLessonId && $lesson->maBH == $freeLessonId;

                                                if ($isEnrolled) {
                                                    $labelClass = 'label--unlocked';
                                                    $labelText = 'Unlocked';
                                                } else {
                                                    $labelClass = $isFreeLesson ? 'label--free' : 'label--paid';
                                                    $labelText = $isFreeLesson ? 'Free' : 'Paid';
                                                }
                                            @endphp
                                            <li class="lesson-item">
                                                <span class="label {{ $labelClass }}">{{ $labelText }}</span>
                                                <a href="{{ route('student.lessons.show', $lesson->maBH) }}" data-lesson-id="{{ $lesson->maBH }}">
                                                    <div class="lesson-list__meta">
                                                        <span class="lesson-list__eyebrow">Bài {{ $lesson->thuTu }}</span>
                                                        <span class="lesson-list__title">{{ $lesson->tieuDe }}</span>
                                                    </div>
                                                    <span class="badge badge--{{ strtolower($lesson->loai) }}">{{ strtoupper($lesson->loai) }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                <!-- Mini Tests -->
                                @if ($chapter->miniTests->count())
                                    <article class="module module--nested" data-accordion data-accordion-autopen="false" style="margin-top: 24px;">
                                        <header class="module__header">
                                            <button class="module__toggle" type="button" aria-expanded="false">
                                                <div class="module__info">
                                                    <span class="module__title" style="font-size: 18px; font-weight: 700;">Review Exercises</span>
                                                </div>
                                                <span style="margin-left: auto; margin-right: 16px; background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">
                                                    {{ $chapter->miniTests->count() }} bài
                                                </span>
                                                <span class="module__chevron" aria-hidden="true"></span>
                                            </button>
                                        </header>
                                        <div class="module__panel">
                                            <div class="module__body" style="padding: 24px; background: white; border-top: 1px solid #e2e8f0;">
                                                <ul class="mini-test-list lesson-list--module">
                                                    @foreach ($chapter->miniTests->sortBy('thuTu') as $miniTest)
                                                        @php
                                                            $isFreeMiniTest = $freeMiniTestId && $miniTest->maMT == $freeMiniTestId;

                                                            if ($isEnrolled) {
                                                                $labelClass = 'label--unlocked';
                                                                $labelText = 'Unlocked';
                                                            } else {
                                                                $labelClass = $isFreeMiniTest ? 'label--free' : 'label--paid';
                                                                $labelText = $isFreeMiniTest ? 'Free' : 'Paid';
                                                            }

                                                            $scoreData = $miniTestScores[$miniTest->maMT] ?? null;
                                                            $bestScore = $scoreData['best_score'] ?? null;
                                                            $latestStatus = $scoreData['latest_status'] ?? null;
                                                            $latestResultId = $scoreData['latest_result_id'] ?? null;
                                                            $inProgressResultId = $scoreData['in_progress_result_id'] ?? null;
                                                            $latestIsGraded = $scoreData['latest_is_fully_graded'] ?? false;
                                                            $attemptsUsed = (int) ($scoreData['attempts_used'] ?? 0);
                                                            $attemptLimit = (int) ($miniTest->attempts_allowed ?? 0);
                                                            $attemptsLeft = $attemptLimit > 0 ? max(0, $attemptLimit - $attemptsUsed) : null;
                                                            $hasAttempts = $attemptsUsed > 0;
                                                        @endphp
                                                        <li class="mini-test-item" data-mini-test-id="{{ $miniTest->maMT }}">
                                                            <span class="label {{ $labelClass }}">{{ $labelText }}</span>
                                                            @if($isEnrolled || $isFreeMiniTest)
                                                                @if($inProgressResultId)
                                                                    <a href="{{ route('student.minitests.attempt', $inProgressResultId) }}" class="mini-test-link mini-test-link--ongoing">
                                                                        <div class="lesson-list__meta">
                                                                            <span class="lesson-list__eyebrow">Practice {{ $miniTest->thuTu }}</span>
                                                                            <span class="lesson-list__title">{{ $miniTest->title }}</span>
                                                                        </div>
                                                                        <span class="badge badge--minitest">TIẾP TỤC</span>
                                                                    </a>
                                                                @elseif($attemptLimit > 0 && $attemptsLeft === 0 && $latestResultId)
                                                                    <a href="{{ route('student.minitests.result', $latestResultId) }}" class="mini-test-link mini-test-link--history">
                                                                        <div class="lesson-list__meta">
                                                                            <span class="lesson-list__eyebrow">Practice {{ $miniTest->thuTu }}</span>
                                                                            <span class="lesson-list__title">{{ $miniTest->title }}</span>
                                                                        </div>
                                                                        <span class="badge badge--minitest">XEM KẾT QUẢ</span>
                                                                    </a>
                                                                @else
                                                                    <form method="POST" action="{{ route('student.minitests.start', $miniTest->maMT) }}" class="mini-test-start-form" style="display:inline;">
                                                                        @csrf
                                                                        <button type="submit" class="mini-test-link" style="background:transparent;border:0;padding:0;text-align:left;width:100%;">
                                                                            <div class="lesson-list__meta">
                                                                                <span class="lesson-list__eyebrow">Practice {{ $miniTest->thuTu }}</span>
                                                                                <span class="lesson-list__title">{{ $miniTest->title }}</span>
                                                                            </div>
                                                                            <span class="badge badge--minitest">REVIEW EXERCISES</span>
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            @else
                                                                <a href="#" class="mini-test-link" >
                                                                    <div class="lesson-list__meta">
                                                                        <span class="lesson-list__eyebrow">Review Exercises {{ $miniTest->thuTu }}</span>
                                                                        <span class="lesson-list__title">{{ $miniTest->title }}</span>
                                                                    </div>
                                                                    <span class="badge badge--minitest">REVIEW EXERCISES</span>
                                                                </a>
                                                            @endif
                                                            <ul class="meta-list meta-list--inline mt-1">
                                                                <li><i class="bi bi-clock"></i> {{ $miniTest->time_limit_min }} phút</li>
                                                                <li><i class="bi bi-question-circle"></i> {{ $miniTest->questions->count() }} câu</li>
                                                                <li><i class="bi bi-trophy"></i> {{ $miniTest->max_score }} điểm</li>
                                                                <li>
                                                                    <i class="bi bi-repeat"></i>
                                                                    @if($attemptLimit > 0)
                                                                        {{ $attemptsUsed }}/{{ $attemptLimit }} lượt
                                                                    @else
                                                                        {{ $attemptsUsed > 0 ? $attemptsUsed . ' lượt đã làm' : 'Chưa làm' }}
                                                                    @endif
                                                                </li>
                                                                @if($scoreData && $hasAttempts)
                                                                    <li>
                                                                        @if($latestStatus === \App\Models\MiniTestResult::STATUS_IN_PROGRESS)
                                                                            <span class="badge" style="background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; padding: 4px 12px; border-radius: 12px; font-weight: 600;">
                                                                                <i class="bi bi-play-circle me-1"></i>Đang làm dở
                                                                            </span>
                                                                        @elseif($latestIsGraded && $bestScore !== null)
                                                                            <span class="badge" style="background: linear-gradient(135deg, #34c759, #30b350); color: white; padding: 4px 12px; border-radius: 12px; font-weight: 600;">
                                                                                <i class="bi bi-check-circle-fill me-1"></i>{{ number_format($bestScore, 1) }}/{{ $miniTest->max_score }}
                                                                            </span>
                                                                        @elseif($latestResultId)
                                                                            <span class="badge" style="background: linear-gradient(135deg, #ffc107, #ff9800); color: white; padding: 4px 12px; border-radius: 12px; font-weight: 600;">
                                                                                <i class="bi bi-hourglass-split me-1"></i>Chờ chấm
                                                                            </span>
                                                                        @endif
                                                                    </li>
                                                                @endif
                                                            </ul>
                                                            @if($attemptLimit > 0 && $attemptsLeft === 0 && $latestResultId)
                                                                <p class="mini-test-note text-muted mb-0">Đã hết lượt làm. Xem lại lịch sử và kết quả chi tiết của bạn.</p>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </article>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Sidebar -->
            <aside class="course-sidebar" data-reveal-from-right>
                @php
                    $sidebarCtaClass = $isEnrolled
                        ? 'course-card__cta--active'
                        : ($isInCart ? 'course-card__cta--in-cart' : '');
                    $sidebarCtaText = $isEnrolled
                        ? 'Đã sở hữu'
                        : ($isInCart ? 'Đã trong giỏ hàng' : 'Thêm vào giỏ hàng');
                    $sidebarCtaAria = $isEnrolled
                        ? 'Bạn đã sở hữu khóa học này'
                        : ($isInCart
                            ? 'Khóa học đã trong giỏ hàng'
                            : 'Thêm ' . $course->tenKH . ' vào giỏ hàng');
                @endphp


                <div class="course-sidebar__card">
                    <h4>Trọn gói bao gồm</h4>
                    <ul class="course-sidebar__list">
                        <li>Tài liệu đính kèm sẵn sàng</li>
                        <li>Review Exercises từng chương</li>
                        <li>Chứng chỉ hoàn thành</li>
                        <li>Cập nhật nội dung trọn đời</li>
                    </ul>
                </div>

                <div class="course-sidebar__card">
                    <h4>Cần trợ giúp?</h4>
                    <p>Team OCC luôn sẵn sàng tư vấn khóa học & hỗ trợ đăng ký.</p>
                    <a class="btn btn--ghost" href="mailto:support@occ.edu.vn">
                        <i class="fa-regular fa-envelope" aria-hidden="true"></i>
                        support@occ.edu.vn
                    </a>
                    <a class="course-sidebar__support-phone" href="tel:0968000000">
                        <i class="fa-solid fa-phone" aria-hidden="true"></i>
                        0968 000 000
                    </a>
                </div>
            </aside>
        </div>
    </section>

    <!-- Course Reviews -->
    <section
        id="course-reviews"
        class="section section--course-reviews course-tab-panel"
        data-course-tab-panel="reviews"
        data-reveal-on-scroll
    >
        <div class="oc-container">
            <div class="course-reviews__grid">
                <div class="course-reviews__summary">
                    <h2>Đánh giá khóa học</h2>
                    <div class="course-reviews__score">
                        <span class="course-reviews__score-value">
                            {{ $averageRating !== null ? number_format((float) $averageRating, 1, ",", ".") : "--" }}
                        </span>
                        <div class="course-reviews__stars">
                            @for ($star = 1; $star <= 5; $star++)
                                @php
                                    $starFill = $averageRating !== null ? max(min($averageRating - ($star - 1), 1), 0) : 0;
                                @endphp
                                <i class="@if($starFill >= 1) fas fa-star @elseif($starFill >= 0.5) fas fa-star-half-alt @else far fa-star @endif"></i>
                            @endfor
                        </div>
                        <span class="course-reviews__count">{{ $totalReviews }} đánh giá</span>
                    </div>
                    <ul class="course-reviews__breakdown">
                        @foreach ($ratingSummary['breakdown'] ?? [] as $star => $count)
                            @php
                                $percent = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                            @endphp
                            <li>
                                <span class="course-reviews__breakdown-label">{{ $star }} sao</span>
                                <div class="course-reviews__breakdown-bar">
                                    <span style="width: {{ $percent }}%"></span>
                                </div>
                                <span class="course-reviews__breakdown-count">{{ $count }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="course-reviews__form">
                    @if ($isEnrolled)
                        <form method="post" action="{{ route('student.courses.reviews.store', $course->slug) }}" class="review-form" id="courseReviewForm" data-reveal-from-left>
                            @csrf
                            <input type="hidden" name="diemSo" value="{{ $studentRatingValue ?? '' }}">
                            <div class="review-form__rating" data-rating-input data-initial="{{ $studentRatingValue ?? 0 }}">
                                @for ($star = 1; $star <= 5; $star++)
                                    <button type="button" class="review-form__star" data-value="{{ $star }}" aria-label="{{ $star }} sao">
                                        <i class="@if(($studentRatingValue ?? 0) >= $star) fas fa-star @else far fa-star @endif"></i>
                                    </button>
                                @endfor
                            </div>
                            <div class="review-form__field">
                                <label for="reviewComment">Nhận xét (không bắt buộc)</label>
                                <textarea id="reviewComment" name="nhanxet" rows="4" placeholder="Chia sẻ trải nghiệm học tập...">{{ $studentReviewContent }}</textarea>
                            </div>
                            <button type="submit" class="btn btn--primary">
                                {{ $studentReview ? 'Cập nhật đánh giá' : 'Gửi đánh giá' }}
                            </button>
                        </form>
                    @elseif ($isAuthenticated)
                        <div class="course-reviews__notice">Chỉ học viên đã kích hoạt khóa học mới có thể đánh giá.</div>
                    @else
                        <div class="course-reviews__notice">Đăng nhập để gửi đánh giá cho khóa học này.</div>
                    @endif
                </div>
            </div>

            <div class="course-reviews__list">
                @forelse ($courseReviews as $review)
                    @php
                        $reviewScore = (int) round($review->diemSo ?? 0);
                        $reviewerName = optional($review->student)->hoTen
                            ?? optional(optional($review->student)->user)->hoTen
                            ?? 'Học viên ẩn danh';
                        $reviewDate = $review->ngayDG ?? $review->updated_at ?? $review->created_at;
                    @endphp
                    <article class="course-review" data-reveal-from-right>
                        <header class="course-review__header">
                            <div class="course-review__avatar" aria-hidden="true">{{ strtoupper(substr($reviewerName, 0, 1)) }}</div>
                            <div class="course-review__meta">
                                <span class="course-review__name">{{ $reviewerName }}</span>
                                <span class="course-review__date">{{ optional($reviewDate)->format('d/m/Y') }}</span>
                            </div>
                            <div class="course-review__score">
                                <span class="course-review__score-badge">{{ $reviewScore }}/5</span>
                                <div class="course-review__stars">
                                    @for ($star = 1; $star <= 5; $star++)
                                        <i class="@if($reviewScore >= $star) fas fa-star @else far fa-star @endif"></i>
                                    @endfor
                                </div>
                            </div>
                        </header>
                        <div class="course-review__body">
                            @if (!empty($review->nhanxet))
                                <p>{!! nl2br(e($review->nhanxet)) !!}</p>
                            @else
                                <p class="course-review__empty">Người học không để lại nhận xét chi tiết.</p>
                            @endif
                        </div>
                    </article>
                @empty
                    <p class="course-reviews__empty">Chưa có đánh giá nào cho khóa học này.</p>
                @endforelse
            </div>
            @if ($courseReviews->hasPages())
                <div class="course-reviews__pagination">
                    {{ $courseReviews->links() }}
                </div>
            @endif
            <div class="course-instructor-highlight" data-reveal-on-scroll>
                <div class="course-instructor-highlight__intro">
                    <span class="course-instructor-highlight__eyebrow">Giảng viên đồng hành</span>
                    <p>Đội ngũ OCC và giảng viên phụ trách theo sát từng chương, phản hồi câu hỏi trong vòng 24 giờ và điều phối mentor khi cần thiết.</p>
                    <p class="course-instructor-highlight__meta-text">
                        {{ $totalReviews > 0 ? $totalReviews . ' học viên đã để lại đánh giá tích cực.' : 'Hãy là người đầu tiên đánh giá trải nghiệm học tập.' }}
                    </p>
                </div>
                <div class="course-instructor-highlight__profile">
                    <div class="instructor-card">
                        <div class="instructor-card__avatar" aria-hidden="true">{{ $teacherInitial }}</div>
                        <div class="instructor-card__body">
                            <span class="instructor-card__label">Giảng viên</span>
                            <h3>{{ $teacherName }}</h3>
                            <p>{{ $teacherSpeciality }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Courses -->
    @if ($relatedCourses->count())
        <section class="section" data-reveal-on-scroll>
            <div class="oc-container">
                <div class="section__header">
                    <h2>Khóa học gợi ý</h2>
                    <p>Các khóa học khác cùng danh mục "{{ optional($course->category)->tenDanhMuc ?? 'Chưa có danh mục' }}" có thể phù hợp với bạn.</p>
                </div>
                <div class="card-grid">
                    @foreach ($relatedCourses as $related)
                                                @php
                            $categoryName = optional($related->category)->tenDanhMuc ?? 'Chương trình nổi bật';
                            $inCart = in_array($related->maKH, $cartIds ?? [], true);
                            $isActive = in_array($related->maKH, $activeCourseIds ?? [], true);
                            if ($isActive) {
                                $inCart = false;
                            }
                            $ctaClass = $isActive
                                ? 'course-card__cta--active'
                                : ($inCart ? 'course-card__cta--in-cart' : '');
                            $ctaText = $isActive
                                ? 'Đã sở hữu'
                                : ($inCart ? 'Đã trong giỏ hàng' : 'Thêm vào giỏ hàng');
                            $promotion = $related->active_promotion;
                            $hasPromotion = $related->saving_amount > 0;
                            $promotionLabel = $promotion?->tenKM;
                            $promotionEnds = $promotion && $promotion->ngayKetThuc
                                ? optional($promotion->ngayKetThuc)->format('d/m')
                                : null;
                        @endphp
                        <article class="course-card {{ $hasPromotion ? 'course-card--has-promo' : '' }}" data-reveal-scale>
                            <div class="course-card__category">
                                <span class="chip chip--category">{{ $categoryName }}</span>
                            </div>
                            <div class="course-card__media">
                                <a href="{{ route('student.courses.show', $related->slug) }}" class="course-card__thumb">
                                    <img src="{{ $related->cover_image_url }}" alt="{{ $related->tenKH }}" loading="lazy">
                                </a>
                                <div class="course-card__media-meta">
                                    <span class="course-card__media-tag {{ $hasPromotion ? 'is-promo' : '' }}">
                                        <i class="fa-solid fa-gift" aria-hidden="true"></i>
                                        {{ $hasPromotion ? ($promotionLabel ?? 'Ưu đãi đang diễn ra') : 'Giá niêm yết ổn định' }}
                                    </span>
                                </div>
                            </div>
                            <div class="course-card__body">
                                <h3><a href="{{ route('student.courses.show', $related->slug) }}">{{ $related->tenKH }}</a></h3>
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
                                                {{ $hasPromotion ? 'Đã giảm ' . $related->saving_percent . '%' : 'Ổn định' }}
                                            </span>
                                        </div>
                                        <div class="course-card__price-value">
                                            {{ number_format($related->sale_price, 0, ',', '.') }} VND
                                        </div>
                                        <div class="course-card__price-meta">
                                            @if ($hasPromotion)
                                                <span class="course-card__origin">{{ number_format($related->original_price, 0, ',', '.') }} VND</span>
                                                <span class="course-card__saving">
                                                    <i class="fa-solid fa-arrow-trend-down" aria-hidden="true"></i>
                                                    Tiết kiệm {{ number_format($related->saving_amount, 0, ',', '.') }} VND
                                                </span>
                                            @else
                                                <span class="course-card__note">
                                                    Bao gồm tài liệu & mentor đồng hành
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @php
                                        $relatedCtaAria = $isActive
                                            ? 'Đã sở hữu khóa học này'
                                            : ($inCart
                                                ? 'Khóa học đã trong giỏ hàng'
                                                : 'Thêm ' . $related->tenKH . ' vào giỏ hàng');
                                    @endphp
                                    <button
                                        type="button"
                                        class="course-card__cta {{ $ctaClass }}"
                                        data-add-to-cart="{{ $related->maKH }}"
                                        data-cart-adding-label="Đang thêm..."
                                        data-cart-added-label="Đã trong giỏ hàng"
                                        aria-label="{{ $relatedCtaAria }}"
                                        @if($isActive || $inCart) disabled aria-disabled="true" @endif
                                    >
                                        {{ $ctaText }}
                                    </button>
                                    @if(!$isActive && !$inCart)
                                        <form method="post" action="{{ route('student.cart.store') }}" class="cart-form d-none" data-course-id="{{ $related->maKH }}">
                                            @csrf
                                            <input type="hidden" name="course_id" value="{{ $related->maKH }}">
                                        </form>
                                    @endif
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
    <script src="{{ asset('js/Student/ajax-forms.js') }}" defer></script>
@endpush

<!-- Enroll Prompt Modal -->
<div class="modal fade" id="enrollPromptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 800px;">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Đăng ký để mở khóa nội dung</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-4 mb-4">
                    <div class="course-image-wrapper" style="min-width: 320px; padding: 0;">
                        <img src="{{ $course->cover_image_url }}" alt="{{ $course->tenKH }}"
                             style="width: 100%; height: 200px; border-radius: 20px; object-fit: contain;">
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="fs-5 fw-bold mb-3">{{ $course->tenKH }}</h6>
                        <p class="text-muted mb-4" style="font-size: 0.95rem; line-height: 1.6;">
                            {{ Str::limit($course->moTa, 200) }}
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
                            <div class="course-price-card {{ $courseHasPromotion ? 'course-price-card--promo' : '' }} course-price-card--compact">
                                <div class="course-price-card__header">
                                    <span class="course-price-card__eyebrow">{{ $coursePriceEyebrow }}</span>
                                    <span class="course-price-card__pill {{ $courseHasPromotion ? 'is-promo' : '' }}">{{ $coursePricePill }}</span>
                                </div>
                                <div class="course-price-card__value">{{ $courseSalePrice }} VND</div>
                                <div class="course-price-card__meta">
                                    @if ($courseHasPromotion)
                                        <span class="course-price-card__origin">{{ $courseOriginalPrice }} VND</span>
                                        <span class="course-price-card__saving">
                                            <i class="fa-solid fa-arrow-trend-down" aria-hidden="true"></i>
                                            {{ $coursePriceNote }}
                                        </span>
                                        @if ($activePromotionEnds)
                                            <span class="course-price-card__expiry">
                                                <i class="fa-regular fa-clock" aria-hidden="true"></i>
                                                Ưu đãi đến {{ $activePromotionEnds }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="course-price-card__note">
                                            <i class="fa-regular fa-file-lines" aria-hidden="true"></i>
                                            {{ $coursePriceNote }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border rounded-4 p-4 bg-light">
                    <h6 class="fw-bold mb-3">Quyền lợi khi đăng ký khóa học:</h6>
                    <ul class="list-unstyled mb-0 row row-cols-2">
                        <li class="d-flex align-items-center gap-2 mb-3 col"><i class="fas fa-check-circle text-success"></i> Tài liệu định dạng sẵn</li>
                        <li class="d-flex align-items-center gap-2 mb-3 col"><i class="fas fa-check-circle text-success"></i> Review Exercises từng chương</li>
                        <li class="d-flex align-items-center gap-2 mb-3 col"><i class="fas fa-check-circle text-success"></i> Final test tổng hợp</li>
                        <li class="d-flex align-items-center gap-2 col"><i class="fas fa-check-circle text-success"></i> Chứng chỉ hoàn thành</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                @if($isEnrolled)
                    <button type="button" class="btn btn-secondary px-4" disabled><i class="fas fa-check me-2"></i> Đã sở hữu</button>
                @elseif($isInCart)
                    <button type="button" class="btn btn-warning px-4 text-white" disabled><i class="fas fa-hourglass-half me-2"></i> Đã trong giỏ hàng</button>
                @else
                    <form method="post" action="{{ route('student.cart.store') }}" style="display: inline;">
                        @csrf
                        <input type="hidden" name="course_id" value="{{ $course->maKH }}">
                        <button type="submit" class="btn btn-primary px-4"><i class="fas fa-shopping-cart me-2"></i> Thêm vào giỏ hàng</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
