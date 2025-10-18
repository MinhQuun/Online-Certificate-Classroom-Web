@extends('student.layout')

@section('title', $lesson->tieuDe . ' - ' . $course->tenKH)

@section('content')
<section class="oc-breadcrumbs">
    <div class="oc-container">
        <a href="{{ route('student.courses.index') }}">Khoa hoc</a>
        <span>/</span>
        <a href="{{ route('student.courses.show', $course->slug) }}">{{ $course->tenKH }}</a>
        <span>/</span>
        <span>{{ $lesson->tieuDe }}</span>
    </div>
</section>

<section class="oc-section">
    <div class="oc-container oc-layout oc-layout--lesson">
        <div class="oc-content">
            <div class="oc-section__header">
                <h1>{{ $lesson->tieuDe }}</h1>
                <p class="oc-muted">{{ $lesson->moTa }}</p>
            </div>

            @php
                $videos = $lesson->materials->filter(fn($m) => strtolower($m->loai) === 'video');
                $audios = $lesson->materials->filter(fn($m) => strtolower($m->loai) === 'audio');
                $pdfs   = $lesson->materials->filter(fn($m) => strtolower($m->loai) === 'pdf');
                $docs   = $lesson->materials->filter(fn($m) => in_array(strtolower($m->loai), ['doc','document','file','ppt','pptx']));
            @endphp

            @if($videos->count())
                <div class="oc-player">
                    @foreach ($videos as $video)
                        <video controls preload="metadata" poster="/Assets/{{ $course->hinhanh }}">
                            <source src="{{ $video->public_url }}" type="{{ $video->mime_type }}">
                            Trinh duyet hien tai khong ho tro the video.
                        </video>
                        @break
                    @endforeach
                </div>
            @endif

            @if($audios->count())
                <div class="oc-audio">
                    @foreach ($audios as $audio)
                        <div class="oc-audio__item">
                            <div class="oc-audio__title">{{ $audio->tenTL }}</div>
                            <audio controls preload="metadata">
                                <source src="{{ $audio->public_url }}" type="{{ $audio->mime_type }}">
                            </audio>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($pdfs->count())
                <div class="oc-pdfs">
                    @foreach ($pdfs as $pdf)
                        <div class="oc-pdf__item">
                            <div class="oc-pdf__title">{{ $pdf->tenTL }}</div>
                            <iframe class="oc-pdf__frame" src="{{ $pdf->public_url }}" title="PDF"></iframe>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($docs->count())
                <div class="oc-docs">
                    @foreach ($docs as $doc)
                        <div class="oc-doc__item">
                            <div class="oc-doc__title">{{ $doc->tenTL }}</div>
                            <a class="oc-btn oc-btn--ghost" href="{{ $doc->public_url }}" target="_blank" rel="noopener">Mo tai lieu</a>
                        </div>
                    @endforeach
                </div>
            @endif

            @php
                $chapterMiniTests = $lesson->chapter->miniTests;
                $finalTests = $course->finalTests;
            @endphp

            @if($chapterMiniTests->count())
                <div class="oc-minitest oc-minitest--lesson">
                    <h2>Mini test cua chuong</h2>
                    <div class="oc-minitest__grid">
                        @foreach ($chapterMiniTests as $miniTest)
                            <article class="oc-minitest__item">
                                <header>
                                    <span class="oc-chip">Mini test</span>
                                    <h5>{{ $miniTest->title }}</h5>
                                </header>
                                <ul class="oc-meta oc-meta--inline">
                                    <li>Thoi gian: {{ $miniTest->time_limit_min }} phut</li>
                                    <li>Lan lam: {{ $miniTest->attempts_allowed }}</li>
                                    <li>Max score: {{ $miniTest->max_score }}</li>
                                </ul>
                                <p class="oc-muted">Trong so diem: {{ $miniTest->trongSo }}</p>
                                @if ($miniTest->materials->count())
                                    <div class="oc-resources">
                                        @foreach ($miniTest->materials as $resource)
                                            <a class="oc-resource" href="{{ $resource->public_url }}" target="_blank" rel="noopener">
                                                <span class="oc-resource__name">{{ $resource->tenTL }}</span>
                                                <span class="oc-resource__type">{{ strtoupper($resource->loai) }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($finalTests->count())
                <div class="oc-finaltest oc-finaltest--lesson">
                    <h2>Final test khoa hoc</h2>
                    <div class="oc-finaltest__grid">
                        @foreach ($finalTests as $test)
                            <article class="oc-finaltest__card">
                                <header>
                                    <span class="oc-chip oc-chip--accent">Final test</span>
                                    <h3>{{ $test->title }}</h3>
                                </header>
                                <ul class="oc-meta oc-meta--inline">
                                    @if ($test->dotTest)
                                        <li>Dot to chuc: {{ $test->dotTest }}</li>
                                    @endif
                                    <li>Thoi gian: {{ $test->time_limit_min }} phut</li>
                                    <li>Tong so cau hoi: {{ $test->total_questions }}</li>
                                </ul>
                                @if ($test->materials->count())
                                    <div class="oc-resources">
                                        @foreach ($test->materials as $resource)
                                            <a class="oc-resource" href="{{ $resource->public_url }}" target="_blank" rel="noopener">
                                                <span class="oc-resource__name">{{ $resource->tenTL }}</span>
                                                <span class="oc-resource__type">{{ strtoupper($resource->loai) }}</span>
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

        <aside class="oc-aside">
            <div class="oc-aside__box">
                <h3>Lich hoc</h3>
                @foreach ($course->chapters as $chapter)
                    <div class="oc-accordion" data-accordion>
                        <button class="oc-accordion__header" type="button">
                            <div>
                                <span class="oc-accordion__eyebrow">Chuong {{ $chapter->thuTu }}</span>
                                <span class="oc-accordion__title">{{ $chapter->tenChuong }}</span>
                            </div>
                            <span class="oc-accordion__chev" aria-hidden>&#9662;</span>
                        </button>
                        <div class="oc-accordion__panel">
                            <ul class="oc-list oc-list--compact">
                                @foreach ($chapter->lessons as $item)
                                    <li class="oc-list__item {{ $item->maBH === $lesson->maBH ? 'is-active' : '' }}">
                                        <a href="{{ route('student.lessons.show', $item->maBH) }}">
                                            <span class="oc-list__title">Bai {{ $item->thuTu }}: {{ $item->tieuDe }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            @if ($chapter->miniTests->count())
                                <div class="oc-aside__mini">
                                    @foreach ($chapter->miniTests as $miniTest)
                                        <span>Mini test: {{ $miniTest->title }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                @if($finalTests->count())
                    <div class="oc-aside__final">
                        <h4>Final test</h4>
                        <ul class="oc-list oc-list--compact">
                            @foreach ($finalTests as $test)
                                <li class="oc-list__item">
                                    <span class="oc-list__title">{{ $test->title }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </aside>
    </div>
</section>
@endsection

