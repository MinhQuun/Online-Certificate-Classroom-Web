@extends('student.layout')

@section('title', $course->tenKH)

@section('content')
<section class="oc-hero oc-hero--course">
    <div class="oc-container oc-hero__grid">
        <div class="oc-hero__text">
            <span class="oc-chip oc-chip--soft">Online Certificate Classroom</span>
            <h1>{{ $course->tenKH }}</h1>
            <p>{{ $course->moTa }}</p>
            <div class="oc-meta">
                <span>Thoi han: {{ $course->thoiHanNgay }} ngay</span>
                <span class="oc-meta__divider">&bull;</span>
                <span>Hoc phi: {{ number_format((float) $course->hocPhi, 0, ',', '.') }}&#8363;</span>
            </div>
        </div>
        <div class="oc-hero__media">
            <img src="/Assets/{{ $course->hinhanh }}" alt="{{ $course->tenKH }}">
        </div>
    </div>
</section>

<section class="oc-section">
    <div class="oc-container oc-layout oc-layout--course">
        <div class="oc-content">
            <div class="oc-section__header">
                <h2>Noi dung khoa hoc</h2>
                <p>Chuong trinh duoc sap xep ro rang theo chuong, di kem mini test va final test danh gia.</p>
            </div>

            @foreach ($course->chapters as $chapter)
                @php
                    $chapterMiniTests = $chapter->miniTests;
                @endphp
                <div class="oc-accordion" data-accordion>
                    <button class="oc-accordion__header" type="button">
                        <div>
                            <span class="oc-accordion__eyebrow">Chuong {{ $chapter->thuTu }}</span>
                            <span class="oc-accordion__title">{{ $chapter->tenChuong }}</span>
                        </div>
                        <span class="oc-accordion__chev" aria-hidden>&#9662;</span>
                    </button>
                    <div class="oc-accordion__panel">
                        <ul class="oc-list">
                            @foreach ($chapter->lessons as $lesson)
                                <li class="oc-list__item">
                                    <a href="{{ route('student.lessons.show', $lesson->maBH) }}">
                                        <span class="oc-list__title">Bai {{ $lesson->thuTu }}: {{ $lesson->tieuDe }}</span>
                                        <span class="oc-tag">{{ strtoupper($lesson->loai) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        @if ($chapterMiniTests->count())
                            <div class="oc-minitest">
                                <h4>Mini test trong chuong</h4>
                                <div class="oc-minitest__grid">
                                    @foreach ($chapterMiniTests as $miniTest)
                                        <article class="oc-minitest__item">
                                            <header>
                                                <span class="oc-chip">Mini test</span>
                                                <h5>{{ $miniTest->title }}</h5>
                                            </header>
                                            <ul class="oc-meta oc-meta--inline">
                                                <li>Thu tu: {{ $miniTest->thuTu }}</li>
                                                <li>Thoi gian: {{ $miniTest->time_limit_min }} phut</li>
                                                <li>Lan lam: {{ $miniTest->attempts_allowed }}</li>
                                            </ul>
                                            <div class="oc-minitest__footer">
                                                <span>Max score: {{ $miniTest->max_score }}</span>
                                                <span>Trong so: {{ $miniTest->trongSo }}</span>
                                            </div>
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
                    </div>
                </div>
            @endforeach

            @if ($course->finalTests->count())
                <div class="oc-finaltest">
                    <div class="oc-section__header">
                        <h2>Final test khoa hoc</h2>
                        <p>Danh gia tong hop sau khi hoan thanh cac chuong.</p>
                    </div>
                    <div class="oc-finaltest__grid">
                        @foreach ($course->finalTests as $test)
                            <article class="oc-finaltest__card">
                                <header>
                                    <span class="oc-chip oc-chip--accent">Final test</span>
                                    <h3>{{ $test->title }}</h3>
                                </header>
                                <ul class="oc-meta">
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
                <div class="oc-aside__price">{{ number_format((float) $course->hocPhi, 0, ',', '.') }}&#8363;</div>
                <a class="oc-btn oc-btn--primary" href="#">Dang ky ngay</a>
                <ul class="oc-aside__list">
                    <li>Hoc linh hoat tren moi thiet bi</li>
                    <li>Tai lieu da dinh dang (video, pdf, audio)</li>
                    <li>Mini test theo chuong va final test tong hop</li>
                </ul>
            </div>
        </aside>
    </div>
</section>
@endsection

