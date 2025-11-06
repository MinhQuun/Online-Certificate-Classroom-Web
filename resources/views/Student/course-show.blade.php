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
        $nonEnrolledError = $errors->getBag('review')->first('review');
    @endphp

    <!-- Hero Section -->
    <section class="course-hero">
        <div class="oc-container course-hero__grid">
            <div class="course-hero__text">
                <span class="chip chip--soft">Lộ trình chứng chỉ</span>
                <h1>{{ $course->tenKH }}</h1>
                <p>{{ $course->moTa }}</p>
                <ul class="course-hero__stats">
                    <li><strong>{{ $course->thoiHanNgay }}</strong> <span>Ngày học</span></li>
                    <li><strong>{{ number_format((float) $course->hocPhi, 0, ",", ".") }}₫</strong> <span>Học phí</span></li>
                    <li><strong>{{ $course->chapters->count() }}</strong> <span>Chương học</span></li>
                    <li><strong>{{ $averageRating !== null ? number_format((float) $averageRating, 1, ",", ".") : "--" }}
                        <div class="course-rating-summary__stars">
                            @for ($star = 1; $star <= 5; $star++)
                                @php
                                    $starFill = $averageRating !== null ? max(min($averageRating - ($star - 1), 1), 0) : 0;
                                @endphp
                                <i class="@if($starFill >= 1) fas fa-star @elseif($starFill >= 0.5) fas fa-star-half-alt @else far fa-star @endif"></i>
                            @endfor
                        </div>
                        </strong> <span>Đánh giá ({{ $totalReviews }})</span></li>
                </ul>
                <div class="course-hero__meta">
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
            <div class="course-hero__media">
                <img src="{{ $courseCover }}" alt="{{ $course->tenKH }}" loading="lazy">
            </div>
        </div>
    </section>

    <!-- Access Flags -->
    <div id="courseAccessFlags"
         data-authenticated="{{ $isAuthenticated ? '1' : '0' }}"
         data-enrolled="{{ $isEnrolled ? '1' : '0' }}"
         data-pending="{{ $isPending ? '1' : '0' }}"
         data-free-lesson="{{ $freeLessonId ?? '' }}"
         data-free-minitest="{{ $freeMiniTestId ?? '' }}"
         hidden></div>

    <!-- Main Content -->
    <section class="section">
        <div class="oc-container course-layout">
            @if (!$isEnrolled)
                <div id="lockedNotice" class="course-locked-notice" role="alert" hidden>
                    <div class="course-locked-notice__content">
                        @if($isPending)
                            <strong>Khóa học đang chờ kích hoạt.</strong>
                            <span>Kiểm tra email để lấy mã kích hoạt hoặc truy cập <a href="{{ route('student.activations.form') }}">Mã kích hoạt</a> để kích hoạt ngay.</span>
                        @else
                            <strong>Khóa học chưa được kích hoạt.</strong>
                            <span>Bạn cần mua khóa học để mở khóa toàn bộ tài nguyên.</span>
                        @endif
                    </div>
                    <button type="button" class="course-locked-notice__close" aria-label="Đóng thông báo">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Course Content -->
            <div class="course-layout__main">
                <div class="section__header">
                    <h2>Nội dung khóa học</h2>
                    <p>Khóa học được chia thành các chương kèm theo bài kiểm tra nhỏ (mini test), giúp bạn đánh giá tiến độ trước khi chuyển sang nội dung mới.</p>
                </div>

                <!-- Chapters -->
                @foreach ($course->chapters as $chapter)
                    <article class="module is-open" data-accordion>
                        <header class="module__header">
                            <button class="module__toggle" type="button" aria-expanded="true">
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
                                    <article class="module module--nested" data-accordion style="margin-top: 24px;">
                                        <header class="module__header">
                                            <button class="module__toggle" type="button" aria-expanded="false">
                                                <div class="module__info">
                                                    <span class="module__title" style="font-size: 18px; font-weight: 700;">MiniTest</span>
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
                                                            $isGraded = $scoreData['is_fully_graded'] ?? false;
                                                        @endphp                                                        
                                                        <li class="mini-test-item" data-mini-test-id="{{ $miniTest->maMT }}">
                                                            <span class="label {{ $labelClass }}">{{ $labelText }}</span>
                                                            @if($isEnrolled || $isFreeMiniTest)
                                                                <form method="POST" action="{{ route('student.minitests.start', $miniTest->maMT) }}" class="mini-test-start-form" style="display:inline;">
                                                                    @csrf
                                                                    <button type="submit" class="mini-test-link" style="background:transparent;border:0;padding:0;text-align:left;width:100%;">
                                                                        <div class="lesson-list__meta">
                                                                            <span class="lesson-list__eyebrow">MiniTest {{ $miniTest->thuTu }}</span>
                                                                            <span class="lesson-list__title">{{ $miniTest->title }}</span>
                                                                        </div>
                                                                        <span class="badge badge--minitest">MINI TEST</span>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <a href="#" class="mini-test-link" >
                                                                    <div class="lesson-list__meta">
                                                                        <span class="lesson-list__eyebrow">MiniTest {{ $miniTest->thuTu }}</span>
                                                                        <span class="lesson-list__title">{{ $miniTest->title }}</span>
                                                                    </div>
                                                                    <span class="badge badge--minitest">MINI TEST</span>
                                                                </a>
                                                            @endif
                                                            <ul class="meta-list meta-list--inline mt-1">
                                                                <li><i class="bi bi-clock"></i> {{ $miniTest->time_limit_min }} phút</li>
                                                                <li><i class="bi bi-question-circle"></i> {{ $miniTest->questions->count() }} câu</li>
                                                                <li><i class="bi bi-trophy"></i> {{ $miniTest->max_score }} điểm</li>
                                                                @if($scoreData)
                                                                    <li>
                                                                        @if($isGraded)
                                                                            <span class="badge" style="background: linear-gradient(135deg, #34c759, #30b350); color: white; padding: 4px 12px; border-radius: 12px; font-weight: 600;">
                                                                                <i class="bi bi-check-circle-fill me-1"></i>{{ number_format($bestScore, 1) }}/{{ $miniTest->max_score }}
                                                                            </span>
                                                                        @else
                                                                            <span class="badge" style="background: linear-gradient(135deg, #ffc107, #ff9800); color: white; padding: 4px 12px; border-radius: 12px; font-weight: 600;">
                                                                                <i class="bi bi-hourglass-split me-1"></i>Chờ chấm
                                                                            </span>
                                                                        @endif
                                                                    </li>
                                                                @endif
                                                            </ul>
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
            <aside class="course-sidebar">
                <div class="course-sidebar__card">
                    <div class="course-sidebar__price">{{ number_format((float) $course->hocPhi, 0, ',', '.') }}₫</div>
                    <form method="post" action="{{ route('student.cart.store') }}" class="course-sidebar__cta">
                        @csrf
                        <input type="hidden" name="course_id" value="{{ $course->maKH }}">
                        <button type="submit"
                                class="btn btn--primary {{ $isEnrolled ? 'btn--owned' : ($isPending ? 'btn--pending' : ($isInCart ? 'btn--in-cart' : '')) }}"
                                style="text-align: center; padding: 16px 24px; font-weight: 700; font-size: 16px; border-radius: 12px;"
                                @if($isEnrolled || $isPending || $isInCart) disabled @endif>
                            {{ $isEnrolled ? 'Đã kích hoạt' : ($isPending ? 'Chờ kích hoạt' : ($isInCart ? 'Đã trong giỏ hàng' : 'Thêm vào giỏ hàng')) }}
                        </button>
                    </form>
                    @if($isEnrolled)
                        <p class="course-sidebar__note course-sidebar__note--owned">Bạn đã sở hữu khóa học này. Tất cả tài nguyên đã được mở khóa.</p>
                    @elseif($isPending)
                        <p class="course-sidebar__note course-sidebar__note--pending">Khóa học đang chờ kích hoạt. Hãy nhập mã tại <a href="{{ route('student.activations.form') }}">Mã kích hoạt</a> để bắt đầu học.</p>
                    @elseif($isInCart)
                        <a class="course-sidebar__link" href="{{ route('student.cart.index') }}">Đến giỏ hàng</a>
                    @endif
                    <ul class="course-sidebar__list">
                        <li>Tài liệu định dạng sẵn</li>
                        <li>Mini test từng chương</li>
                        <li>Final test tổng hợp</li>
                        <li>Chứng chỉ hoàn thành</li>
                    </ul>
                </div>

                <div class="course-sidebar__card course-sidebar__card--muted">
                    <h4>Thông tin lịch học</h4>
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

    <!-- Course Reviews -->
    <section id="course-reviews" class="section section--course-reviews">
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
                    @if (session('review_status') === 'success')
                        <div class="alert alert-success">{{ session('review_message') }}</div>
                    @endif
                    @if ($nonEnrolledError)
                        <div class="alert alert-warning">{{ $nonEnrolledError }}</div>
                    @endif
                    @if ($errors->getBag('default')->has('diemSo') || $errors->getBag('default')->has('nhanxet'))
                        <div class="alert alert-danger">
                            @foreach ($errors->getBag('default')->get('diemSo') as $message)
                                <div>{{ $message }}</div>
                            @endforeach
                            @foreach ($errors->getBag('default')->get('nhanxet') as $message)
                                <div>{{ $message }}</div>
                            @endforeach
                        </div>
                    @endif

                    @if ($isEnrolled)
                        <form method="post" action="{{ route('student.courses.reviews.store', $course->slug) }}" class="review-form" id="courseReviewForm">
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
                    <article class="course-review">
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
        </div>
    </section>

    <!-- Related Courses -->
    @if ($relatedCourses->count())
        <section class="section">
            <div class="oc-container">
                <div class="section__header">
                    <h2>Khóa học gợi ý</h2>
                    <p>Các khóa học khác cùng danh mục "{{ optional($course->category)->tenDanhMuc ?? 'Chưa có danh mục' }}" có thể phù hợp với bạn.</p>
                </div>
                <div class="card-grid">
                    @foreach ($relatedCourses as $related)
                        @php
                            $isOwned = in_array($related->maKH, $activeCourseIds);
                            $inCart = in_array($related->maKH, $cartIds);
                        @endphp
                        <article class="course-card">
                            <div class="course-card__category">
                                <span class="chip chip--category">{{ optional($related->category)->tenDanhMuc ?? 'Khác' }}</span>
                            </div>
                            <a href="{{ route('student.courses.show', $related->slug) }}" class="course-card__thumb">
                                <img src="{{ $related->cover_image_url }}" alt="{{ $related->tenKH }}" loading="lazy">
                            </a>
                            <div class="course-card__body">
                                <h3><a href="{{ route('student.courses.show', $related->slug) }}">{{ $related->tenKH }}</a></h3>
                                <div class="course-card__footer">
                                    <div class="course-card__price-block">
                                        <strong>{{ number_format((float) $related->hocPhi, 0, ',', '.') }}₫</strong>
                                    </div>
                                    <form method="post" action="{{ route('student.cart.store') }}">
                                        @csrf
                                        <input type="hidden" name="course_id" value="{{ $related->maKH }}">
                                        <button type="submit"
                                                class="course-card__cta {{ $isOwned ? 'course-card__cta--owned' : ($inCart ? 'course-card__cta--in-cart' : '') }}"
                                                @if($isOwned || $inCart) disabled @endif>
                                            {{ $isOwned ? 'Đã mua' : ($inCart ? 'Đã trong giỏ hàng' : 'Thêm vào giỏ hàng') }}
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
                            <strong class="fs-3 text-primary">{{ number_format((float) $course->hocPhi, 0, ',', '.') }}₫</strong>
                        </div>
                    </div>
                </div>
                <div class="border rounded-4 p-4 bg-light">
                    <h6 class="fw-bold mb-3">Quyền lợi khi đăng ký khóa học:</h6>
                    <ul class="list-unstyled mb-0 row row-cols-2">
                        <li class="d-flex align-items-center gap-2 mb-3 col"><i class="fas fa-check-circle text-success"></i> Tài liệu định dạng sẵn</li>
                        <li class="d-flex align-items-center gap-2 mb-3 col"><i class="fas fa-check-circle text-success"></i> Mini test từng chương</li>
                        <li class="d-flex align-items-center gap-2 mb-3 col"><i class="fas fa-check-circle text-success"></i> Final test tổng hợp</li>
                        <li class="d-flex align-items-center gap-2 col"><i class="fas fa-check-circle text-success"></i> Chứng chỉ hoàn thành</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                @if($isEnrolled)
                    <button type="button" class="btn btn-secondary px-4" disabled><i class="fas fa-check me-2"></i> Đã kích hoạt</button>
                @elseif($isPending)
                    <button type="button" class="btn btn-warning px-4 text-white" disabled><i class="fas fa-hourglass-half me-2"></i> Chờ kích hoạt</button>
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