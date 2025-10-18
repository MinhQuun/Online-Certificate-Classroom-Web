@extends('student.layout')

@section('title', 'Khoa hoc')

@section('content')
<section class="oc-hero">
    <div class="oc-container oc-hero__grid">
        <div class="oc-hero__text">
            <span class="oc-chip oc-chip--soft">For learners</span>
            <h1>Khoa hoc chung chi truc tuyen</h1>
            <p>Giao dien hoc vien sang, hien dai va tap trung vao trai nghiem hoc tap.</p>
            <form method="get" class="oc-search">
                <input type="text" name="q" value="{{ $q }}" placeholder="Tim khoa hoc...">
                <button type="submit">Tim</button>
            </form>
        </div>
        <div class="oc-hero__media">
            <img src="/Assets/{{ $courses->first()?->hinhanh ?? 'logo.png' }}" alt="Hero image">
        </div>
    </div>
</section>

<section class="oc-section">
    <div class="oc-container">
        <div class="oc-grid">
            @forelse ($courses as $course)
                <article class="oc-card">
                    <a href="{{ route('student.courses.show', $course->slug) }}" class="oc-card__thumb">
                        <img src="/Assets/{{ $course->hinhanh }}" alt="{{ $course->tenKH }}">
                        <span class="oc-badge">{{ number_format((float) $course->hocPhi, 0, ',', '.') }}&#8363;</span>
                    </a>
                    <div class="oc-card__body">
                        <h3 class="oc-card__title">
                            <a href="{{ route('student.courses.show', $course->slug) }}">{{ $course->tenKH }}</a>
                        </h3>
                        <p class="oc-card__desc">{{ $course->moTa }}</p>
                    </div>
                </article>
            @empty
                <p>Chua co khoa hoc.</p>
            @endforelse
        </div>

        <div class="oc-pagination">
            {{ $courses->withQueryString()->links() }}
        </div>
    </div>
</section>
@endsection

