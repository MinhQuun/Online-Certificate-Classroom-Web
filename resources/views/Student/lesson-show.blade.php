@extends('layouts.student')

@section('title', $lesson->tieuDe . ' - ' . $course->tenKH)

@push('styles')
    @php
        $pageStyle = 'css/Student/pages-lesson.css';
        $discussionStyle = 'css/Student/lesson-discussion.css';
    @endphp
    <link rel="stylesheet" href="{{ asset($pageStyle) }}?v={{ student_asset_version($pageStyle) }}">
    <link rel="stylesheet" href="{{ asset($discussionStyle) }}?v={{ student_asset_version($discussionStyle) }}">
@endpush

@section('content')
    <nav class="breadcrumbs" aria-label="Duong dan trang">
        <div class="oc-container breadcrumbs__inner">
            <a href="{{ route('student.courses.index') }}">Khóa học</a>
            <span>/</span>
            <a href="{{ route('student.courses.show', $course->slug) }}">{{ $course->tenKH }}</a>
            <span>/</span>
            <span>{{ $lesson->tieuDe }}</span>
        </div>
    </nav>

    @php
        $courseCover = $course->cover_image_url;
        $videos = $lesson->materials->filter(fn ($m) => strtolower($m->loai) === 'video');
        $audios = $lesson->materials->filter(fn ($m) => strtolower($m->loai) === 'audio');
        $pdfs = $lesson->materials->filter(fn ($m) => strtolower($m->loai) === 'pdf');
        $docs = $lesson->materials->filter(fn ($m) => in_array(strtolower($m->loai), ['doc', 'document', 'file', 'ppt', 'pptx']));
        $chapterMiniTests = $lesson->chapter->miniTests;
        $lessonTypeLabel = $lesson->loai ? strtoupper($lesson->loai) : 'LESSON';
        $materialsCount = $lesson->materials->count();
        $downloadableCount = $pdfs->count() + $docs->count();
        $primaryVideo = $videos->first();
        $chapterOrder = optional($lesson->chapter)->thuTu;
        $canTrackProgress = auth()->check() && ($isEnrolled ?? false) && $primaryVideo;
        $resumeSeconds = $canTrackProgress ? (int) ($lessonProgress->video_progress_seconds ?? 0) : 0;
        $watchedSeconds = $canTrackProgress ? (int) ($lessonProgress->thoiGianHoc ?? 0) : 0;
        $progressStatus = $canTrackProgress ? ($lessonProgress->trangThai ?? 'NOT_STARTED') : 'NOT_STARTED';
        $progressConfig = $canTrackProgress ? [
            'lessonId' => $lesson->maBH,
            'courseId' => $course->maKH,
            'progressUrl' => route('student.lessons.progress.store', ['lesson' => $lesson->maBH]),
            'csrfToken' => csrf_token(),
            'resumeSeconds' => $resumeSeconds,
            'watchedSeconds' => $watchedSeconds,
            'status' => $progressStatus,
            'maxSeekAheadSeconds' => 12,
            'durationSeconds' => $lessonProgress->video_duration_seconds ?? null,
        ] : null;
    @endphp

    <section class="lesson-hero">
        <div class="oc-container lesson-hero__grid">
            <div class="lesson-hero__info">
                <div class="lesson-hero__eyebrow">
                    @if ($lessonTypeLabel)
                        <span class="chip chip--soft">{{ $lessonTypeLabel }}</span>
                    @endif
                    @if ($chapterOrder)
                        <span class="lesson-hero__chapter">Chương {{ $chapterOrder }}</span>
                    @endif
                </div>
                <h1>{{ $lesson->tieuDe }}</h1>
                @if ($lesson->moTa)
                    <p class="muted">{{ $lesson->moTa }}</p>
                @endif
                <ul class="lesson-meta">
                    <li>
                        <strong>Bài {{ $lesson->thuTu }}</strong>
                        <span>Thứ tự bài học</span>
                    </li>
                    <li>
                        <strong>{{ $materialsCount }}</strong>
                        <span>Tài liệu đi kèm</span>
                    </li>
                    @if ($downloadableCount)
                        <li>
                            <strong>{{ $downloadableCount }}</strong>
                            <span>Tài liệu tải về</span>
                        </li>
                    @endif
                    @if ($chapterMiniTests->count())
                        <li>
                            <strong>{{ $chapterMiniTests->count() }}</strong>
                            <span>Mini test chương</span>
                        </li>
                    @endif
                </ul>
                <div class="lesson-hero__actions">
                    <a class="btn btn--ghost" href="{{ route('student.courses.show', $course->slug) }}">Về khóa học</a>
                </div>
            </div>
            <div class="lesson-hero__preview">
                <img src="{{ $courseCover }}" alt="{{ $course->tenKH }}">
                <div class="lesson-hero__preview-meta">
                    <span>{{ $course->tenKH }}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="oc-container lesson-layout">
            <div class="lesson-layout__main">
                @if ($primaryVideo)
                    <div class="lesson-card lesson-card--media">
                        <header class="lesson-card__header">
                            <h2>Video bài học</h2>
                            <span class="badge badge--video">{{ $videos->count() }} video</span>
                        </header>
                        <div class="lesson-media__frame">
                            <video controls preload="metadata" poster="{{ $courseCover }}" data-lesson-video data-progress-enabled="{{ $canTrackProgress ? '1' : '0' }}">
                                <source src="{{ $primaryVideo->public_url }}" type="{{ $primaryVideo->mime_type }}">
                                Trình duyệt hiện tại không hỗ trợ video.
                            </video>
                        </div>
                        <div class="lesson-media__warning" data-progress-warning hidden></div>
                        @if ($primaryVideo->tenTL)
                            <p class="lesson-media__caption muted">{{ $primaryVideo->tenTL }}</p>
                        @endif
                        @if ($videos->count() > 1)
                            <div class="lesson-media__playlist resource-list resource-list--compact">
                                @foreach ($videos->skip(1) as $video)
                                    <a href="{{ $video->public_url }}" target="_blank" rel="noopener">
                                        <span>{{ $video->tenTL }}</span>
                                        <span class="badge badge--video">Video</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                @if ($audios->count())
                    <div class="lesson-card lesson-card--audio">
                        <header class="lesson-card__header">
                            <h2>Nội dung audio</h2>
                            <span class="badge badge--audio">{{ $audios->count() }} file</span>
                        </header>
                        <div class="audio-list">
                            @foreach ($audios as $audio)
                                <div class="audio-item">
                                    <div class="audio-item__title">{{ $audio->tenTL }}</div>
                                    <audio controls preload="metadata">
                                        <source src="{{ $audio->public_url }}" type="{{ $audio->mime_type }}">
                                    </audio>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($pdfs->count())
                    <div class="lesson-card lesson-card--pdf lesson-pdfs">
                        <header class="lesson-card__header">
                            <h2>Tài liệu bài học</h2>
                            <span class="badge badge--pdf">{{ $pdfs->count() }} file</span>
                        </header>
                        <div class="lesson-card__body pdf-grid">
                            @foreach ($pdfs as $pdf)
                                <div class="pdf-card">
                                    <div class="pdf-card__title">{{ $pdf->tenTL }}</div>
                                    <iframe src="{{ $pdf->public_url }}" title="{{ $pdf->tenTL }}"></iframe>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($docs->count())
                    <div class="lesson-card lesson-card--docs lesson-docs">
                        <header class="lesson-card__header">
                            <h2>Tài liệu tham khảo</h2>
                            <span class="badge badge--doc">{{ $docs->count() }} file</span>
                        </header>
                        <div class="lesson-card__body doc-grid">
                            @foreach ($docs as $doc)
                                <div class="doc-card">
                                    <div class="doc-card__title">{{ $doc->tenTL }}</div>
                                    <a class="btn btn--ghost" href="{{ $doc->public_url }}" target="_blank" rel="noopener">Mở tài liệu</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- THÊM ID VÀO ĐÂY --}}
                @if ($chapterMiniTests->count())
                    <div class="lesson-card mini-tests" id="mini-tests">
                        <header class="lesson-card__header">
                            <h2>Mini test của chương</h2>
                            <span class="badge badge--accent">{{ $chapterMiniTests->count() }} bài kiểm tra</span>
                        </header>
                        <div class="mini-tests__grid">
                            @foreach ($chapterMiniTests as $miniTest)
                                @php
                                    $testResult = $miniTestResults->get($miniTest->maMT);
                                    $hasDone = $testResult !== null;
                                    $bestScore = $hasDone ? $testResult->best_score : null;
                                    $attemptsUsed = $hasDone ? $testResult->attempts_used : 0;
                                    $attemptsLeft = $miniTest->attempts_allowed - $attemptsUsed;
                                @endphp
                                <article class="mini-test-card">
                                    <header>
                                        <span class="chip">Mini test</span>
                                        <h4>{{ $miniTest->title }}</h4>
                                    </header>
                                    <div class="meta-content">
                                        <ul class="meta-list meta-list--inline">
                                            <li><strong>⏱️</strong> {{ $miniTest->time_limit_min }} phút</li>
                                            <li><strong>🔄</strong> {{ $attemptsLeft }}/{{ $miniTest->attempts_allowed }} lần còn lại</li>
                                            <li><strong>⭐</strong> {{ $miniTest->max_score }} điểm</li>
                                        </ul>
                                        <p class="muted">Trọng số: <strong>{{ $miniTest->trongSo }}</strong></p>
                                        @if ($hasDone)
                                            <div class="mini-test-score">
                                                <span class="score-label">Điểm cao nhất:</span>
                                                <span class="score-value">{{ number_format($bestScore, 2) }}/{{ $miniTest->max_score }}</span>
                                                @php
                                                    $percentage = ($bestScore / $miniTest->max_score) * 100;
                                                    $scoreClass = $percentage >= 80 ? 'excellent' : ($percentage >= 60 ? 'good' : ($percentage >= 40 ? 'average' : 'poor'));
                                                @endphp
                                                <span class="score-badge score-badge--{{ $scoreClass }}">
                                                    {{ number_format($percentage, 0) }}%
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mini-test-card__actions">
                                        @if ($attemptsLeft > 0)
                                            <a class="btn btn--primary" href="{{ route('student.minitests.show', $miniTest->maMT) }}">
                                                <span>{{ $hasDone ? 'Làm lại bài kiểm tra' : 'Làm bài kiểm tra' }}</span>
                                                <i class="bi bi-arrow-right"></i>
                                            </a>
                                        @else
                                            <button class="btn btn--disabled" disabled>
                                                <span>Đã hết lượt làm bài</span>
                                            </button>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <aside class="lesson-layout__aside">
                <div class="aside-card">
                    <div class="aside-card__head">
                        <h3>Lượt trình khóa học</h3>
                        <p class="muted">Theo dõi chương và chọn bài học để di chuyển nhanh.</p>
                    </div>

                    {{-- THÊM WRAPPER NÀY ĐỂ TẠO VÙNG CUỘN --}}
                    <div class="aside-card__content">
                        <a class="btn btn--ghost" href="{{ route('student.courses.show', $course->slug) }}">Xem khóa học</a>

                        @foreach ($course->chapters as $chapter)
                            <div class="accordion" data-accordion>
                                <button class="module__toggle" type="button">
                                    <div class="module__info">
                                        <span class="module__eyebrow">Chương {{ $chapter->thuTu }}</span>
                                        <span class="module__title">{{ $chapter->tenChuong }}</span>
                                    </div>
                                    <span class="module__chevron" aria-hidden="true"></span>
                                </button>
                                <div class="module__panel">
                                    <div class="module__body">
                                        <ul class="lesson-list lesson-list--compact">
                                            @foreach ($chapter->lessons as $item)
                                                <li class="{{ $item->maBH === $lesson->maBH ? 'is-active' : '' }}">
                                                    <a href="{{ route('student.lessons.show', $item->maBH) }}">Bài {{ $item->thuTu }}: {{ $item->tieuDe }}</a>
                                                </li>
                                            @endforeach
                                        </ul>

                                        {{-- CẬP NHẬT MINI TEST THÀNH LINK --}}
                                        @if ($chapter->miniTests->count())
                                            <div class="aside-mini">
                                                @foreach ($chapter->miniTests as $miniTest)
                                                    <a href="#mini-tests">
                                                        <span>📝</span>
                                                        Mini test: {{ $miniTest->title }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div> {{-- KẾT THÚC WRAPPER CUỘN --}}
                </div>
            </aside>
        </div>
    </section>

    @php
        $discussionPermissions = $discussionBootstrap['permissions'] ?? ['can_post' => false, 'can_reply' => false, 'can_moderate' => false];
        $discussionUserRole = $discussionPermissions['role'] ?? null;
        $discussionIsAuthenticated = !empty($discussionBootstrap['user']);
    @endphp

    <button
        class="lesson-discussion-toggle"
        type="button"
        data-discussion-toggle
        aria-haspopup="dialog"
        aria-controls="lessonDiscussionPanel"
        aria-expanded="false"
    >

        <span class="lesson-discussion-toggle__label">Hỏi đáp</span>
        <span class="lesson-discussion-toggle__count" data-discussion-count>{{ $discussionBootstrap['total'] ?? 0 }}</span>
    </button>

    <div class="lesson-discussion" data-discussion-root>
        <div class="lesson-discussion__overlay" data-discussion-close aria-hidden="true"></div>

        <aside
            class="lesson-discussion__panel"
            id="lessonDiscussionPanel"
            role="dialog"
            aria-modal="true"
            aria-labelledby="lessonDiscussionTitle"
            tabindex="-1"
        >
            <header class="lesson-discussion__header">
                <div class="lesson-discussion__header-info">
                    <h2 id="lessonDiscussionTitle">Hỏi đáp bài học</h2>
                    <p class="lesson-discussion__subtitle">Trao đổi với giáo viên và các bạn cùng khóa.</p>
                </div>
                <button class="lesson-discussion__close" type="button" data-discussion-close aria-label="Đóng hỏi đáp">
                    <i class="bi bi-x-lg"></i>
                </button>
            </header>

            <section class="lesson-discussion__composer" data-discussion-composer>
                @if ($discussionPermissions['can_post'] ?? false)
                    <form class="discussion-form" data-discussion-form>
                        <div class="discussion-form__input">
                            <textarea
                                rows="3"
                                data-discussion-input
                                placeholder="Nhập câu hỏi cụ thể để được giải đáp nhanh hơn..."
                                aria-label="Nội dung câu hỏi"
                            ></textarea>
                        </div>
                        <div class="discussion-form__actions">
                            <button type="submit" class="btn btn--primary" data-discussion-submit>
                                <span>Đăng câu hỏi</span>
                            </button>
                        </div>
                        <p class="discussion-form__hint">Câu hỏi rõ ràng giúp giáo viên và bạn học hỗ trợ chính xác hơn.</p>
                    </form>
                @else
                    <div class="discussion-form__placeholder">
                        @if (! $discussionIsAuthenticated)
                            <p>Đăng nhập để tham gia hỏi đáp và theo dõi phản hồi mới.</p>
                            <a class="btn btn--ghost" href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}">
                                Đăng nhập
                            </a>
                        @elseif ($discussionUserRole === 'GIANG_VIEN')
                            <p>Bạn đang xem với vai trò giảng viên. Hãy phản hồi câu hỏi của học viên bên dưới.</p>
                        @elseif ($discussionUserRole === 'ADMIN')
                            <p>Bạn có thể hỗ trợ học viên bằng cách phản hồi hoặc ghim thông tin quan trọng.</p>
                        @else
                            <p>Tính năng hỏi đáp chỉ dành cho học viên đã tham gia khóa học.</p>
                            <a class="btn btn--ghost" href="{{ route('student.courses.show', $course->slug) }}">Quay lại khóa học</a>
                        @endif
                    </div>
                @endif
            </section>

            <section class="lesson-discussion__list" data-discussion-list>
                <div class="discussion-empty" data-discussion-empty>
                    <i class="bi bi-emoji-smile" aria-hidden="true"></i>
                    <p>Chưa có câu hỏi nào. Hãy mở đầu cuộc trao đổi cho bài học này!</p>
                </div>
            </section>

            <footer class="lesson-discussion__footer" data-discussion-footer>
                <button type="button" class="btn btn--ghost" data-discussion-load-more hidden>Tải thêm</button>
            </footer>
        </aside>
    </div>
@endsection

@push('scripts')
    @if ($progressConfig)
        <script>
            window.lessonProgressConfig = @json($progressConfig);
        </script>
    @endif
    <script>
        window.lessonDiscussionBootstrap = @json($discussionBootstrap ?? []);
    </script>
    <script src="{{ asset('js/Student/lesson-discussion.js') }}" defer></script>
    <script src="{{ asset('js/Student/lesson-show.js') }}" defer></script>
@endpush
