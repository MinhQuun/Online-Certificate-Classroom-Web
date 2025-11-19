@extends('layouts.student')

@section('title', 'Khóa học của tôi')

@push('styles')
    @php
        $pageStyle = 'css/Student/pages-my-courses.css';
    @endphp
    <link rel="stylesheet" href="{{ asset($pageStyle) }}?v={{ student_asset_version($pageStyle) }}">
@endpush

@section('content')
<main class="my-courses-page">
    <div class="my-courses-container">
        {{-- Header --}}
        <div class="my-courses-header">
            <div class="header-content">
                <h1>
                    <i class="fa-solid fa-book-tanakh"></i>
                    Khóa học của tôi
                </h1>
                <p class="subtitle">Quản lý và theo dõi tiến độ các khóa học bạn đã đăng ký</p>
            </div>
        </div>

        {{-- Tabs Filter --}}
        <div class="my-courses-tabs">
            <a href="{{ route('student.my-courses') }}"
               class="tab {{ $status === 'all' ? 'active' : '' }}">
                <span class="tab-label">Tất cả</span>
                <span class="tab-count">{{ $counts['all'] }}</span>
            </a>
            <a href="{{ route('student.my-courses', ['status' => 'active']) }}"
               class="tab {{ $status === 'active' ? 'active' : '' }}">
                <span class="tab-label">Đang học</span>
                <span class="tab-count">{{ $counts['active'] }}</span>
            </a>
            <a href="{{ route('student.my-courses', ['status' => 'expired']) }}"
               class="tab {{ $status === 'expired' ? 'active' : '' }}">
                <span class="tab-label">Đã hết hạn</span>
                <span class="tab-count">{{ $counts['expired'] }}</span>
            </a>
        </div>

        {{-- Courses Grid --}}
        @if($enrollments->count() > 0)
            <div class="courses-grid">
                @foreach($enrollments as $enrollment)
                    @php
                        $course = $enrollment->course;
                                                $statusClass = match($enrollment->trangThai) {
                            'ACTIVE' => 'active',
                            'PENDING' => 'active',
                            'EXPIRED' => 'expired',
                            default => 'unknown'
                        };
                                                $statusLabel = match($enrollment->trangThai) {
                            'ACTIVE' => 'Đang học',
                            'PENDING' => 'Đang học',
                            'EXPIRED' => 'Đã hết hạn',
                            default => 'Không xác định'
                        };
                    @endphp

                    <article class="course-card" data-course-id="{{ $course->maKH }}">
                        {{-- Course Image --}}
                        <div class="course-card__image">
                            <a href="{{ route('student.courses.show', $course->slug) }}">
                                <img src="{{ $course->cover_image_url }}" alt="{{ $course->tenKH }}" loading="lazy">
                            </a>
                            <span class="course-status {{ $statusClass }}">{{ $statusLabel }}</span>
                        </div>

                        {{-- Course Info --}}
                        <div class="course-card__body">
                            {{-- Category --}}
                            @if($course->category)
                                <div class="course-category">
                                    <i class="fa-solid fa-tag"></i>
                                    {{ $course->category->tenDanhMuc }}
                                </div>
                            @endif

                            {{-- Title --}}
                            <h3 class="course-title">
                                <a href="{{ route('student.courses.show', $course->slug) }}">
                                    {{ $course->tenKH }}
                                </a>
                            </h3>

                            {{-- Teacher --}}
                            @if($course->teacher)
                                <div class="course-teacher">
                                    <i class="fa-solid fa-chalkboard-user"></i>
                                    {{ $course->teacher->hoTen ?? $course->teacher->name }}
                                </div>
                            @endif

                            {{-- Progress Bar --}}
                            @if($enrollment->trangThai === 'ACTIVE')
                                <div class="progress-section">
                                    <div class="progress-header">
                                        <span class="progress-label">Tiến độ học tập</span>
                                        <span class="progress-percent">{{ $enrollment->progress_percent ?? 0 }}%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: {{ $enrollment->progress_percent ?? 0 }}%"></div>
                                    </div>
                                </div>
                            @endif

            {{-- Actions --}}
            <div class="course-actions">
                @if(in_array($enrollment->trangThai, ['ACTIVE', 'PENDING'], true))
                    <a href="{{ route('student.courses.show', $course->slug) }}" class="btn btn-primary">
                        <i class="fa-solid fa-play"></i>
                        Tiếp tục học
                    </a>
                @else
                    <a href="{{ route('student.courses.show', $course->slug) }}" class="btn btn-secondary">
                        <i class="fa-solid fa-eye"></i>
                                        Xem chi tiết
                    </a>
                @endif  
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($enrollments->hasPages())
                <div class="pagination-wrapper">
                    {{ $enrollments->appends(['status' => $status])->links() }}
                </div>
            @endif
        @else
            {{-- Empty State --}}
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <h3>Chưa có khóa học nào</h3>
                <p>
                    @if($status === 'all')
                        Bạn chưa đăng ký khóa học nào. Khám phá và đăng ký ngay để bắt đầu hành trình học tập!
                    @elseif($status === 'active')
                        Bạn chưa có khóa học nào đang hoạt động.
                    @else
                        Bạn chưa có khóa học nào đã hết hạn.
                    @endif
                </p>
                <a href="{{ route('student.courses.index') }}" class="btn btn-primary">
                    <i class="fa-solid fa-compass"></i>
                    Khám phá khóa học
                </a>
            </div>
        @endif
    </div>
</main>
@endsection

@push('scripts')
    @php
        $pageScript = 'js/Student/my-courses.js';
    @endphp
    <script src="{{ asset($pageScript) }}?v={{ student_asset_version($pageScript) }}"></script>
@endpush
