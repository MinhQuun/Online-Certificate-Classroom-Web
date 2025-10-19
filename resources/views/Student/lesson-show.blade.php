@extends('layouts.student')

@section('title', $lesson->tieuDe . ' - ' . $course->tenKH)

@push('styles')
    @php
        $pageStyle = 'css/Student/pages-lesson.css';
    @endphp
    <link rel="stylesheet" href="{{ asset($pageStyle) }}?v={{ student_asset_version($pageStyle) }}">
@endpush

@section('content')
    <nav class="breadcrumbs" aria-label="Đường dẫn trang">
        <div class="oc-container breadcrumbs__inner">
            <a href="{{ route('student.courses.index') }}">Khóa học</a>
            <span>/</span>
            <a href="{{ route('student.courses.show', $course->slug) }}">{{ $course->tenKH }}</a>
            <span>/</span>
            <span>{{ $lesson->tieuDe }}</span>
        </div>
    </nav>

    @php
        $courseCover = $course->hinhanh ? asset('Assets/' . $course->hinhanh) : asset('Assets/logo.png');
        $videos = $lesson->materials->filter(fn($m) => strtolower($m->loai) === 'video');
        $audios = $lesson->materials->filter(fn($m) => strtolower($m->loai) === 'audio');
        $pdfs   = $lesson->materials->filter(fn($m) => strtolower($m->loai) === 'pdf');
        $docs   = $lesson->materials->filter(fn($m) => in_array(strtolower($m->loai), ['doc', 'document', 'file', 'ppt', 'pptx']));
        $chapterMiniTests = $lesson->chapter->miniTests;
        $finalTests = $course->finalTests;
    @endphp

    <section class="section">
        <div class="oc-container lesson-layout">
            <div class="lesson-layout__main">
                <div class="section__header">
                    <h1>{{ $lesson->tieuDe }}</h1>
                    <p class="muted">{{ $lesson->moTa }}</p>
                </div>

                @if ($videos->count())
                    <div class="lesson-media">
                        @foreach ($videos as $video)
                            <video controls preload="metadata" poster="{{ $courseCover }}">
                                <source src="{{ $video->public_url }}" type="{{ $video->mime_type }}">
                                Trình duyệt hiện tại không hỗ trợ video.
                            </video>
                            @break
                        @endforeach
                    </div>
                @endif

                @if ($audios->count())
                    <div class="lesson-audio">
                        <h2>Nội dung audio</h2>
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
                    <div class="lesson-pdfs">
                        <h2>Tài liệu PDF</h2>
                        <div class="pdf-grid">
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
                    <div class="lesson-docs">
                        <h2>Tài liệu tham khảo</h2>
                        <div class="doc-grid">
                            @foreach ($docs as $doc)
                                <div class="doc-card">
                                    <div class="doc-card__title">{{ $doc->tenTL }}</div>
                                    <a class="btn btn--ghost" href="{{ $doc->public_url }}" target="_blank" rel="noopener">Mở tài liệu</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($chapterMiniTests->count())
                    <div class="mini-tests">
                        <h2>Mini test của chương</h2>
                        <div class="mini-tests__grid">
                            @foreach ($chapterMiniTests as $miniTest)
                                <article class="mini-test-card">
                                    <header>
                                        <span class="chip">Mini test</span>
                                        <h4>{{ $miniTest->title }}</h4>
                                    </header>
                                    <ul class="meta-list meta-list--inline">
                                        <li>Thời gian: {{ $miniTest->time_limit_min }} phút</li>
                                        <li>Lần làm: {{ $miniTest->attempts_allowed }}</li>
                                        <li>Điểm tối đa: {{ $miniTest->max_score }}</li>
                                    </ul>
                                    <p class="muted">Trọng số điểm: {{ $miniTest->trongSo }}</p>
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
                    <div class="final-tests">
                        <h2>Bài kiểm tra cuối khóa</h2>
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
                                        <li>Thời gian: {{ $test->time_limit_min }} phút</li>
                                        <li>Tổng số câu hỏi: {{ $test->total_questions }}</li>
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
                    <h3>Lộ trình khóa học</h3>
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

                    @if($finalTests->count())
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