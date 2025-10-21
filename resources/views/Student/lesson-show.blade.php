@extends('layouts.student')

@section('title', $lesson->tieuDe . ' - ' . $course->tenKH)

@push('styles')
    @php
        $pageStyle = 'css/Student/pages-lesson.css';
    @endphp
    <link rel="stylesheet" href="{{ asset($pageStyle) }}?v={{ student_asset_version($pageStyle) }}">
@endpush

@section('content')
    <nav class="breadcrumbs" aria-label="Duong dan trang">
        <div class="oc-container breadcrumbs__inner">
            <a href="{{ route('student.courses.index') }}">Khoa hoc</a>
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
        $finalTests = $course->finalTests;
        $lessonTypeLabel = $lesson->loai ? strtoupper($lesson->loai) : 'LESSON';
        $materialsCount = $lesson->materials->count();
        $downloadableCount = $pdfs->count() + $docs->count();
        $primaryVideo = $videos->first();
        $chapterOrder = optional($lesson->chapter)->thuTu;
    @endphp

    <section class="lesson-hero">
        <div class="oc-container lesson-hero__grid">
            <div class="lesson-hero__info">
                <div class="lesson-hero__eyebrow">
                    @if ($lessonTypeLabel)
                        <span class="chip chip--soft">{{ $lessonTypeLabel }}</span>
                    @endif
                    @if ($chapterOrder)
                        <span class="lesson-hero__chapter">Chuong {{ $chapterOrder }}</span>
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
                    @if ($finalTests->count())
                        <li>
                            <strong>{{ $finalTests->count() }}</strong>
                            <span>Final test</span>
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
                            <h2>Video bai hoc</h2>
                            <span class="badge badge--video">{{ $videos->count() }} video</span>
                        </header>
                        <div class="lesson-media__frame">
                            <video controls preload="metadata" poster="{{ $courseCover }}">
                                <source src="{{ $primaryVideo->public_url }}" type="{{ $primaryVideo->mime_type }}">
                                Trình duyệt hiện tại không hỗ trợ video.
                            </video>
                        </div>
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
                            <h2>Noi dung audio</h2>
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
                            <h2>Tai lieu tham khao</h2>
                            <span class="badge badge--doc">{{ $docs->count() }} file</span>
                        </header>
                        <div class="lesson-card__body doc-grid">
                            @foreach ($docs as $doc)
                                <div class="doc-card">
                                    <div class="doc-card__title">{{ $doc->tenTL }}</div>
                                    <a class="btn btn--ghost" href="{{ $doc->public_url }}" target="_blank" rel="noopener">Mo tai lieu</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($chapterMiniTests->count())
                    <div class="lesson-card mini-tests">
                        <header class="lesson-card__header">
                            <h2>Mini test của chương</h2>
                            <span class="badge badge--accent">{{ $chapterMiniTests->count() }} bài kiểm tra</span>
                        </header>
                        <div class="mini-tests__grid">
                            @foreach ($chapterMiniTests as $miniTest)
                                <article class="mini-test-card">
                                    <header>
                                        <span class="chip">Mini test</span>
                                        <h4>{{ $miniTest->title }}</h4>
                                    </header>
                                    <div class="meta-content">
                                        <ul class="meta-list meta-list--inline">
                                            <li><strong>⏱️</strong> {{ $miniTest->time_limit_min }} phút</li>
                                            <li><strong>🔄</strong> {{ $miniTest->attempts_allowed }} lần làm</li>
                                            <li><strong>⭐</strong> {{ $miniTest->max_score }} điểm</li>
                                        </ul>
                                        <p class="muted">Trọng số: <strong>{{ $miniTest->trongSo }}</strong></p>
                                    </div>
                                    @if ($miniTest->materials->count())
                                        <div class="resource-list">
                                            @foreach ($miniTest->materials as $resource)
                                                @php
                                                    $resTypeKey = preg_replace('/[^a-z0-9]+/', '-', strtolower($resource->loai)) ?: 'default';
                                                @endphp
                                                <a href="{{ $resource->public_url }}" target="_blank" rel="noopener">
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

                @if ($finalTests->count())
                    <div class="lesson-card final-tests">
                        <header class="lesson-card__header">
                            <h2>Bài kiểm tra cuối khóa</h2>
                            <span class="badge badge--accent">{{ $finalTests->count() }} bai</span>
                        </header>
                        <div class="final-tests__grid">
                            @foreach ($finalTests as $test)
                                <article class="final-test-card">
                                    <header>
                                        <span class="chip chip--accent">Final test</span>
                                        <h3>{{ $test->title }}</h3>
                                    </header>
                                    <ul class="meta-list meta-list--inline">
                                        @if ($test->dotTest)
                                            <li>Đợt tổ chức: {{ $test->dotTest }}</li>
                                        @endif
                                        <li>Thoi gian: {{ $test->time_limit_min }} phut</li>
                                        <li>Tong so cau hoi: {{ $test->total_questions }}</li>
                                    </ul>
                                    @if ($test->materials->count())
                                        <div class="resource-list">
                                            @foreach ($test->materials as $resource)
                                                @php
                                                    $resTypeKey = preg_replace('/[^a-z0-9]+/', '-', strtolower($resource->loai)) ?: 'default';
                                                @endphp
                                                <a href="{{ $resource->public_url }}" target="_blank" rel="noopener">
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

            <aside class="lesson-layout__aside">
                <div class="aside-card">
                    <div class="aside-card__head">
                        <h3>Lượt trình khóa học</h3>
                        <p class="muted">Theo dõi chương và chọn bài học để di chuyển nhanh.</p>
                    </div>
                    <a class="btn btn--ghost" href="{{ route('student.courses.show', $course->slug) }}">Xem khóa học</a>
                    @foreach ($course->chapters as $chapter)
                        <div class="accordion" data-accordion>
                            <button class="module__toggle" type="button">
                                <div class="module__info">
                                    <span class="module__eyebrow">Chuong {{ $chapter->thuTu }}</span>
                                    <span class="module__title">{{ $chapter->tenChuong }}</span>
                                </div>
                                <span class="module__chevron" aria-hidden="true"></span>
                            </button>
                            <div class="module__panel">
                                <div class="module__body">
                                    <ul class="lesson-list lesson-list--compact">
                                        @foreach ($chapter->lessons as $item)
                                            <li class="{{ $item->maBH === $lesson->maBH ? 'is-active' : '' }}">
                                                <a href="{{ route('student.lessons.show', $item->maBH) }}">Bai {{ $item->thuTu }}: {{ $item->tieuDe }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @if ($chapter->miniTests->count())
                                        <div class="aside-mini">
                                            @foreach ($chapter->miniTests as $miniTest)
                                                <span>Mini test: {{ $miniTest->title }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if ($finalTests->count())
                        <div class="aside-final">
                            <h4>Final test</h4>
                            <ul>
                                @foreach ($finalTests as $test)
                                    <li>{{ $test->title }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </aside>
        </div>
    </section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const accordions = document.querySelectorAll('.accordion');
    
    accordions.forEach(accordion => {
        const toggle = accordion.querySelector('.module__toggle');
        const panel = accordion.querySelector('.module__panel');
        
        // Set initial state
        accordion.setAttribute('aria-expanded', 'false');
        
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            const isExpanded = accordion.getAttribute('aria-expanded') === 'true';
            
            // Close all other accordions (optional)
            accordions.forEach(otherAccordion => {
                if (otherAccordion !== accordion) {
                    otherAccordion.setAttribute('aria-expanded', 'false');
                }
            });
            
            // Toggle current accordion
            accordion.setAttribute('aria-expanded', !isExpanded);
            
            if (!isExpanded) {
                panel.style.maxHeight = panel.scrollHeight + 'px';
            } else {
                panel.style.maxHeight = '0';
            }
        });
        
        // Auto-expand if contains active lesson
        const isActive = accordion.querySelector('.lesson-list li.is-active');
        if (isActive) {
            accordion.setAttribute('aria-expanded', 'true');
            panel.style.maxHeight = panel.scrollHeight + 'px';
        }
    });
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            accordions.forEach(accordion => {
                const isExpanded = accordion.getAttribute('aria-expanded') === 'true';
                if (isExpanded) {
                    const panel = accordion.querySelector('.module__panel');
                    panel.style.maxHeight = panel.scrollHeight + 'px';
                }
            });
        }, 250);
    });
});
</script>
@endpush
