@extends('layouts.student')

@section('title', 'Tiến độ học tập')

@push('styles')
    @php
        $pageStyle = 'css/Student/lesson-progress.css';
    @endphp
    <link rel="stylesheet" href="{{ asset($pageStyle) }}?v={{ student_asset_version($pageStyle) }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/Student/lesson-progress.js') }}" defer></script>
@endpush

@php
    $lessonStatusLabels = [
        'NOT_STARTED' => 'Chưa bắt đầu',
        'IN_PROGRESS' => 'Đang học',
        'COMPLETED' => 'Hoàn thành',
    ];
@endphp

@section('content')
    <div class="progress-page">
        <div class="oc-container">
            <header class="progress-page__header section__header">
                <h1>Tiến độ học tập</h1>
                <p>Theo dõi quá trình học theo từng khóa học, tính cả bài video và bài tập ôn hoàn thành.</p>
            </header>

            @if($enrollments->isEmpty())
                <div class="progress-empty">
                    <div class="progress-empty__icon" aria-hidden="true"><i class="fa-solid fa-graduation-cap"></i></div>
                    <h2>Bạn chưa đăng ký khóa học nào</h2>
                    <p>Bắt đầu hành trình mới bằng cách chọn một khóa học phù hợp với mục tiêu của bạn.</p>
                    <a class="btn btn--primary" href="{{ route('student.courses.index') }}">Khám phá khóa học</a>
                </div>
            @else
                <section class="progress-overview" aria-label="Tổng quan tiến độ">
                    <article class="overview-card">
                        <span class="overview-card__label">Khóa học đang học</span>
                        <strong class="overview-card__value">{{ $overviewMetrics['totalCourses'] }}</strong>
                        <span class="overview-card__hint">Số khóa học đang kích hoạt</span>
                    </article>
                    <article class="overview-card">
                        <span class="overview-card__label">Tổng thời gian đã học</span>
                        <strong class="overview-card__value">{{ $overviewMetrics['totalLearningReadable'] }}</strong>
                        <span class="overview-card__hint">{{ number_format($overviewMetrics['totalLearningSeconds']) }} giây</span>
                    </article>
                    <article class="overview-card">
                        <span class="overview-card__label">Tiến độ trung bình</span>
                        <strong class="overview-card__value">
                            {{ $overviewMetrics['averageProgress'] !== null ? $overviewMetrics['averageProgress'] . '%' : '0%' }}
                        </strong>
                        <span class="overview-card__hint">Tính trên các khóa có dữ liệu</span>
                    </article>
                </section>

                <section class="course-progress" aria-label="Tiến độ từng khóa học">
                    @foreach($snapshots as $snapshot)
                        @php
                            $course = $snapshot['course'];
                            $metrics = $snapshot['metrics'];
                            $nextLesson = $snapshot['nextLesson'] ?? null;
                            $chapters = $snapshot['chapters'];
                            $videoProgress = $snapshot['video_progress'];
                            $overall = $metrics['overall_percent'] ?? 0;
                            $lessonPercent = $metrics['lesson_percent'] ?? 0;
                            $videoPercent = $metrics['video_percent'] ?? null;
                            $miniPercent = $metrics['mini_percent'] ?? null;
                            $avgMini = $metrics['avg_mini_score'];
                            $latestLabel = $metrics['latest_activity_for_humans'] ?? 'Chưa có hoạt động';
                            $videoInsights = $snapshot['video_insights'] ?? [];
                            $mostViewedVideos = collect($videoInsights['most_viewed'] ?? []);
                            $videoViewCount = (int) ($metrics['video_view_count'] ?? 0);
                            $replayedVideos = (int) ($metrics['video_replayed_lessons'] ?? 0);
                        @endphp
                        <article class="progress-card" data-progress-card>
                            <div class="progress-card__header">
                                <div class="progress-card__cover">
                                    <img src="{{ $course->cover_image_url }}" alt="{{ $course->tenKH }}">
                                </div>
                                <div class="progress-card__title">
                                    <h2>{{ $course->tenKH }}</h2>
                                    <div class="progress-card__meta">
                                        <span class="chip chip--soft">{{ optional($course->category)->tenDanhMuc ?? 'Không xác định' }}</span>
                                        @if($metrics['latest_activity'])
                                            <span class="progress-card__activity">Hoạt động gần nhất {{ $latestLabel }}</span>
                                        @else
                                            <span class="progress-card__activity">Chưa có hoạt động ghi nhận</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="progress-card__meter" style="--progress: {{ $overall }}" data-progress-target="{{ $overall }}">
                                    <div class="progress-card__meter-inner">
                                        <span class="progress-card__meter-value">{{ $overall }}%</span>
                                        <span class="progress-card__meter-label">Tổng quan</span>
                                    </div>
                                </div>
                            </div>

                            <div class="progress-card__body">
                                <div class="progress-card__stats" role="list">
                                    <div class="stat-item" role="listitem">
                                        <span class="stat-item__label">Bài học</span>
                                        <strong class="stat-item__value">{{ $metrics['completed_lessons'] }}/{{ $metrics['total_lessons'] }}</strong>
                                        <span class="stat-item__sub">{{ $lessonPercent }}%</span>
                                    </div>
                                    <div class="stat-item" role="listitem">
                                        <span class="stat-item__label">Video</span>
                                        <strong class="stat-item__value">{{ $metrics['completed_videos'] }}/{{ $metrics['total_videos'] }}</strong>
                                        <span class="stat-item__sub">{{ $videoPercent !== null ? $videoPercent . '%' : 'N/A' }}</span>
                                    </div>
                                    <div class="stat-item" role="listitem">
                                        <span class="stat-item__label">Lượt xem video</span>
                                        <strong class="stat-item__value">{{ number_format($videoViewCount) }}</strong>
                                        <span class="stat-item__sub">
                                            {{ $replayedVideos > 0 ? $replayedVideos . ' video xem lặp lại' : 'Ghi nhận mỗi lần xem' }}
                                        </span>
                                    </div>
                                    <div class="stat-item" role="listitem">
                                        <span class="stat-item__label">Bài tập ôn</span>
                                        <strong class="stat-item__value">{{ $metrics['completed_minitests'] }}/{{ $metrics['total_minitests'] }}</strong>
                                        <span class="stat-item__sub">{{ $miniPercent !== null ? $miniPercent . '%' : 'N/A' }}</span>
                                    </div>
                                    <div class="stat-item" role="listitem">
                                        <span class="stat-item__label">Điểm trung bình</span>
                                        <strong class="stat-item__value">{{ $avgMini !== null ? number_format($avgMini, 2) : 'Chưa có' }}</strong>
                                        <span class="stat-item__sub">Bài tập ôn</span>
                                    </div>
                                    <div class="stat-item" role="listitem">
                                        <span class="stat-item__label">Thời gian xem</span>
                                        <strong class="stat-item__value">{{ $videoProgress['watched_readable'] }}</strong>
                                        <span class="stat-item__sub">{{ number_format($videoProgress['watched_seconds']) }} giây</span>
                                    </div>
                                    <div class="stat-item" role="listitem">
                                        <span class="stat-item__label">Lượt truy cập</span>
                                        <strong class="stat-item__value">{{ number_format($metrics['total_lesson_views']) }}</strong>
                                        <span class="stat-item__sub">Lần vào bài học</span>
                                    </div>
                                </div>

                                <div class="video-insights">
                                    <div class="video-insights__summary">
                                        <div>
                                            <span class="video-insights__label">Tổng lượt xem video</span>
                                            <strong class="video-insights__value">{{ number_format($videoViewCount) }} lượt</strong>
                                            <span class="video-insights__hint">
                                                {{ $replayedVideos }} video xem lặp lại
                                                @if(!empty($videoInsights['last_viewed_for_humans']))
                                                    - Lần xem gần nhất {{ $videoInsights['last_viewed_for_humans'] }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    @if($mostViewedVideos->isNotEmpty())
                                        <div class="video-insights__list">
                                            @foreach($mostViewedVideos as $item)
                                                <div class="video-pill">
                                                    <div class="video-pill__top">
                                                        <span class="video-pill__title">{{ $item['title'] }}</span>
                                                        <span class="video-pill__count">{{ $item['view_count'] }} lượt xem</span>
                                                    </div>
                                                    @if(!empty($item['last_viewed_for_humans']))
                                                        <span class="video-pill__meta">Lần cuối {{ $item['last_viewed_for_humans'] }}</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div class="progress-card__actions">
                                    @if($nextLesson)
                                        <a href="{{ route('student.lessons.show', $nextLesson['lesson']->maBH) }}"
                                            class="btn btn--primary progress-card__cta">
                                            Tiếp tục: {{ $nextLesson['lesson']->tieuDe }}
                                        </a>
                                    @endif
                                    <a href="{{ route('student.courses.show', $course->slug) }}"
                                        class="btn btn--ghost progress-card__cta progress-card__cta--ghost">
                                        Xem thông tin khóa học
                                    </a>
                                </div>

                                <div class="progress-card__chapters" data-progress-accordion>
                                    @foreach($chapters as $chapter)
                                        @php
                                            $lessonItems = $chapter['lessons'];
                                            $miniTestItems = $chapter['miniTests'];
                                        @endphp
                                        <article class="chapter-card" data-chapter>
                                            <button class="chapter-card__header" type="button" data-chapter-toggle aria-expanded="false">
                                                <div>
                                                    <span class="chapter-card__title">{{ $chapter['title'] }}</span>
                                                    @if(!empty($chapter['description']))
                                                        <p class="chapter-card__description">{{ $chapter['description'] }}</p>
                                                    @endif
                                                </div>
                                                <div class="chapter-card__info">
                                                    <span class="chapter-card__percent">{{ $chapter['lesson_completion_percent'] }}%</span>
                                                    <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                                                </div>
                                            </button>
                                            <div class="chapter-card__body" data-chapter-body hidden>
                                                <ul class="lesson-list">
                                                    @foreach($lessonItems as $lesson)
                                                        @php
                                                            $statusClass = strtolower($lesson['status']);
                                                            $statusKey = strtoupper($lesson['status']);
                                                            $statusLabel = $lessonStatusLabels[$statusKey] ?? $lesson['status'];
                                                        @endphp
                                                        <li class="lesson-item lesson-item--{{ $statusClass }}">
                                                            <div class="lesson-item__main">
                                                                <span class="lesson-item__title">{{ $lesson['title'] }}</span>
                                                                <span class="lesson-item__type">{{ strtoupper($lesson['type']) }}</span>
                                                                @if(strtolower((string) $lesson['type']) === 'video')
                                                                    <div class="lesson-item__views">
                                                                        <span class="lesson-item__chip">{{ number_format($lesson['view_count'] ?? 0) }} lượt xem</span>
                                                                        @if(!empty($lesson['last_viewed_for_humans']))
                                                                            <span class="lesson-item__viewtime">Lần gần nhất {{ $lesson['last_viewed_for_humans'] }}</span>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="lesson-item__meta">
                                                                @if($lesson['percent'] !== null)
                                                                    <span class="lesson-item__percent">{{ $lesson['percent'] }}%</span>
                                                                @endif
                                                                <span class="lesson-item__status">{{ $statusLabel }}</span>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>

                                                @if($miniTestItems->isNotEmpty())
                                                    <div class="minitest-list">
                                                        <h3>Bài tập ôn</h3>
                                                        <ul>
                                                            @foreach($miniTestItems as $test)
                                                                <li class="minitest-item {{ $test['best_score'] !== null ? 'is-completed' : '' }}">
                                                                    <div>
                                                                        <span class="minitest-item__title">{{ $test['title'] }}</span>
                                                                        <span class="minitest-item__score">
                                                                            @if($test['best_score'] !== null)
                                                                                {{ number_format($test['best_score'], 2) }}/{{ number_format($test['max_score'], 2) }}
                                                                            @else
                                                                                Chưa làm
                                                                            @endif
                                                                        </span>
                                                                    </div>
                                                                    @if(!empty($test['last_attempt_at']))
                                                                        <span class="minitest-item__time">
                                                                            Lần gần nhất: {{ \Carbon\Carbon::parse($test['last_attempt_at'])->diffForHumans() }}
                                                                        </span>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        </article>
                    @endforeach
                </section>
            @endif
        </div>
    </div>
@endsection
