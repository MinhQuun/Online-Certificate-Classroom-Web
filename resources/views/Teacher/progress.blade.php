@extends('layouts.teacher')

@section('title', 'Tiến độ học tập')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Teacher/progress.css') }}">
@endpush

@section('content')
    <section class="page-header">
        <span class="kicker">Giảng viên</span>
        <h1 class="title">Tiến độ học tập</h1>
        <p class="muted">Theo dõi tiến độ học viên theo từng khóa. Trang này chỉ để xem, không thay đổi dữ liệu.</p>
    </section>

    @if($courses->isEmpty())
        <div class="alert alert-info border-0 shadow-sm">
            <i class="bi bi-info-circle me-2"></i>
            Bạn chưa có khóa học nào. Khi có học viên ghi danh, dữ liệu sẽ hiển thị tại đây.
        </div>
    @else
        <div class="card border-0 shadow-sm mb-4 course-panel">
            <div class="card-body">
                <div class="course-panel__header d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <p class="text-uppercase text-muted small mb-1">Tổng quan khóa học</p>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h4 class="mb-0">Khóa học đang phụ trách</h4>
                            <span class="pill">Chỉ xem</span>
                        </div>
                        <p class="mb-0 text-muted small">Xem nhanh số lượng học viên và mức độ hoàn thành.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('teacher.progress.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-clockwise me-1"></i> Tải lại
                        </a>
                    </div>
                </div>
                <div class="course-panel__controls">
                    <div class="course-panel__row">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="pill pill-ghost">Tổng: <span data-course-total>{{ count($courseSummaries) }}</span> khóa</span>
                            <span class="pill pill-ghost">Trung bình: {{ $metrics['average'] ?? '0' }}%</span>
                            <span class="pill pill-ghost" data-course-count>Đang hiển thị: {{ count($courseSummaries) }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <div class="search-input" data-search-wrapper>
                                <i class="bi bi-search"></i>
                                <input type="search" class="form-control" placeholder="Tìm tên khóa học..."
                                       data-course-search aria-label="Tìm tên khóa học">
                            </div>
                            <select class="form-select form-select-sm w-auto" data-course-sort aria-label="Sắp xếp">
                                <option value="name-asc">Sắp xếp: Tên (A-Z)</option>
                                <option value="progress-desc">Tiến độ giảm dần</option>
                                <option value="students-desc">Học viên nhiều nhất</option>
                                <option value="active-first">Đang xem lên trước</option>
                            </select>
                            <div class="btn-group btn-group-sm course-view-toggle" role="group">
                                <button type="button" class="btn btn-outline-secondary active" data-course-view="grid">
                                    <i class="bi bi-grid-3x3-gap-fill me-1"></i> Lưới
                                </button>
                                <button type="button" class="btn btn-outline-secondary" data-course-view="list">
                                    <i class="bi bi-list-ul me-1"></i> Danh sách
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="filter-chips">
                        <button type="button" class="filter-chip is-active" data-course-filter="all">Tất cả</button>
                        <button type="button" class="filter-chip" data-course-filter="has-learners">Có học viên</button>
                        <button type="button" class="filter-chip" data-course-filter="no-learners">Chưa có học viên</button>
                        <button type="button" class="filter-chip" data-course-filter="in-progress">Trung bình > 0%</button>
                        <button type="button" class="filter-chip" data-course-filter="active">Đang xem</button>
                    </div>
                </div>
                <div class="course-grid" data-course-grid data-view="grid">
                    @foreach($courseSummaries as $summary)
                        @php $isActive = $activeCourse && $activeCourse->maKH === $summary['id']; @endphp
                        <div class="course-grid__item" data-course-item
                             data-title="{{ $summary['name'] }}"
                             data-average="{{ (int) $summary['average'] }}"
                             data-learners="{{ (int) $summary['total'] }}"
                             data-active="{{ $isActive ? '1' : '0' }}">
                            <div class="course-card h-100 {{ $isActive ? 'is-active' : '' }}">
                                <div class="course-card__top">
                                    <div class="flex-grow-1">
                                        <div class="course-card__eyebrow">Học viên: {{ $summary['total'] }}</div>
                                        <div class="course-card__title">{{ $summary['name'] }}</div>
                                        <div class="course-card__meta">
                                            <span>Tiến độ trung bình</span>
                                            <strong>{{ $summary['average'] }}%</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column align-items-end gap-2">
                                        @if($isActive)
                                            <span class="pill pill-live">Đang xem</span>
                                        @endif
                                        <a class="btn btn-outline-primary btn-sm"
                                           href="{{ route('teacher.progress.show', $summary['id']) }}">
                                            Xem chi tiết
                                        </a>
                                    </div>
                                </div>
                                <div class="course-card__progress">
                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                        <span>Hoàn thành</span>
                                        <span>{{ $summary['average'] }}%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar"
                                             style="width: {{ $summary['average'] }}%;"
                                             aria-valuenow="{{ $summary['average'] }}" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                                <div class="course-card__stats">
                                    <div class="course-card__stat">
                                        <span class="dot dot-success"></span>
                                        <div>
                                            <span class="text-muted small d-block">Hoàn thành</span>
                                            <strong>{{ $summary['completed'] }}</strong>
                                        </div>
                                    </div>
                                    <div class="course-card__stat">
                                        <span class="dot dot-info"></span>
                                        <div>
                                            <span class="text-muted small d-block">Đang học</span>
                                            <strong>{{ $summary['in_progress'] }}</strong>
                                        </div>
                                    </div>
                                    <div class="course-card__stat">
                                        <span class="dot dot-muted"></span>
                                        <div>
                                            <span class="text-muted small d-block">Chưa bắt đầu</span>
                                            <strong>{{ $summary['not_started'] }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="course-grid-wrapper" data-course-scroll>
                    <div class="course-grid" data-course-grid data-view="grid">
                        @foreach($courseSummaries as $summary)
                            @php $isActive = $activeCourse && $activeCourse->maKH === $summary['id']; @endphp
                            <div class="course-grid__item" data-course-item
                                 data-title="{{ $summary['name'] }}"
                                 data-average="{{ (int) $summary['average'] }}"
                                 data-learners="{{ (int) $summary['total'] }}"
                                 data-active="{{ $isActive ? '1' : '0' }}">
                                <div class="course-card h-100 {{ $isActive ? 'is-active' : '' }}">
                                    <div class="course-card__top">
                                        <div class="flex-grow-1">
                                            <div class="course-card__eyebrow">Học viên: {{ $summary['total'] }}</div>
                                            <div class="course-card__title">{{ $summary['name'] }}</div>
                                            <div class="course-card__meta">
                                                <span>Tiến độ trung bình</span>
                                                <strong>{{ $summary['average'] }}%</strong>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column align-items-end gap-2">
                                            @if($isActive)
                                                <span class="pill pill-live">Đang xem</span>
                                            @endif
                                            <a class="btn btn-outline-primary btn-sm"
                                               href="{{ route('teacher.progress.show', $summary['id']) }}">
                                                Xem chi tiết
                                            </a>
                                        </div>
                                    </div>
                                    <div class="course-card__progress">
                                        <div class="d-flex justify-content-between small text-muted mb-1">
                                            <span>Hoàn thành</span>
                                            <span>{{ $summary['average'] }}%</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar"
                                                 style="width: {{ $summary['average'] }}%;"
                                                 aria-valuenow="{{ $summary['average'] }}" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="course-card__stats">
                                        <div class="course-card__stat">
                                            <span class="dot dot-success"></span>
                                            <div>
                                                <span class="text-muted small d-block">Hoàn thành</span>
                                                <strong>{{ $summary['completed'] }}</strong>
                                            </div>
                                        </div>
                                        <div class="course-card__stat">
                                            <span class="dot dot-info"></span>
                                            <div>
                                                <span class="text-muted small d-block">Đang học</span>
                                                <strong>{{ $summary['in_progress'] }}</strong>
                                            </div>
                                        </div>
                                        <div class="course-card__stat">
                                            <span class="dot dot-muted"></span>
                                            <div>
                                                <span class="text-muted small d-block">Chưa bắt đầu</span>
                                                <strong>{{ $summary['not_started'] }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="course-empty text-center py-4" data-course-empty hidden>
                    <i class="bi bi-search fs-3 text-muted d-block mb-2"></i>
                    <p class="mb-1 fw-semibold">Không tìm thấy khóa học phù hợp</p>
                    <p class="text-muted small mb-0">Hãy thử từ khóa khác hoặc thay đổi bộ lọc.</p>
                </div>
            </div>
        </div>

        @if($activeCourse)
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3 mt-4">
                <div>
                    <h5 class="mb-1">{{ $activeCourse->tenKH }}</h5>
                    <p class="mb-0 text-muted small">Chi tiết tiến độ học viên (chỉ xem, không chỉnh sửa).</p>
                </div>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="legend d-none d-md-flex align-items-center gap-3">
                        <span class="legend-dot bg-success"></span><span class="text-muted small">Hoàn thành</span>
                        <span class="legend-dot bg-info"></span><span class="text-muted small">Đang học</span>
                        <span class="legend-dot bg-warning"></span><span class="text-muted small">Chưa bắt đầu</span>
                    </div>
                    <a href="{{ route('teacher.progress.index') }}" class="btn btn-outline-secondary btn-sm">Quay lại tổng quan</a>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="progress-card summary-card h-100">
                        <div class="value">{{ $metrics['average'] }}%</div>
                        <div class="label">Tiến độ trung bình</div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="progress-card summary-card h-100">
                        <div class="value">{{ $metrics['completed'] }}</div>
                        <div class="label">Đã hoàn thành</div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="progress-card summary-card h-100">
                        <div class="value">{{ $metrics['in_progress'] }}</div>
                        <div class="label">Đang học</div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="progress-card summary-card h-100">
                        <div class="value">{{ $metrics['not_started'] }}</div>
                        <div class="label">Chưa bắt đầu</div>
                    </div>
                </div>
            </div>

            @if($enrollments->isEmpty())
                <div class="alert alert-light border">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-people fs-4 text-muted"></i>
                        <div>
                            <h5 class="mb-1">Chưa có học viên trong khóa này</h5>
                            <p class="mb-0 text-muted">Khi học viên ghi danh, tiến độ sẽ hiển thị tại đây.</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm progress-table-card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 progress-table">
                                <thead>
                                    <tr>
                                        <th>Học viên</th>
                                        <th>Email</th>
                                        <th>Trạng thái</th>
                                        <th>Tiến độ</th>
                                        <th>Cập nhật</th>
                                        <th>Tiến độ chương</th>
                                        <th>Bài học gần nhất</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $enrollment)
                                        <tr>
                                            <td>
                                                <strong>{{ $enrollment->student_name }}</strong>
                                                <div class="text-muted small">Ghi danh: {{ $enrollment->joined_at ?? 'Chưa cập nhật' }}</div>
                                            </td>
                                            <td>{{ $enrollment->email ?? 'Chưa cập nhật' }}</td>
                                            <td>
                                                <span class="status-badge {{ $enrollment->trangThai }}">
                                                    {{ $statusLabels[$enrollment->trangThai] ?? $enrollment->trangThai }}
                                                </span>
                                            </td>
                                            <td style="min-width: 220px;">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="flex-grow-1 progress" style="height: 10px;">
                                                        <div class="progress-bar {{ $enrollment->progress_percent >= 100 ? 'bg-success' : 'bg-info' }}"
                                                            role="progressbar"
                                                            style="width: {{ $enrollment->progress_percent }}%;"
                                                            aria-valuenow="{{ $enrollment->progress_percent }}"
                                                            aria-valuemin="0"
                                                            aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <span class="fw-semibold">{{ $enrollment->progress_percent }}%</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge rounded-pill bg-light text-muted">
                                                    {{ $enrollment->updated_for_humans ?? 'Chưa cập nhật' }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $studentChapters = $chapterProgress[$enrollment->maHV] ?? [];
                                                @endphp
                                                @if(empty($studentChapters))
                                                    <span class="text-muted small">Chưa có dữ liệu</span>
                                                @else
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @foreach($studentChapters as $chapter)
                                                            <span class="chapter-pill chapter-{{ $chapter['status'] }}">
                                                                <span class="fw-semibold">{{ $chapter['order'] ?? $loop->iteration }}.</span>
                                                                {{ $chapter['title'] }}
                                                                @if($chapter['percent'] !== null)
                                                                    <span class="text-muted">({{ $chapter['percent'] }}%)</span>
                                                                @endif
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($enrollment->last_lesson_title)
                                                    <div class="fw-semibold">{{ $enrollment->last_lesson_title }}</div>
                                                    <div class="text-muted small">Thứ tự #{{ $enrollment->last_lesson_order }}</div>
                                                @else
                                                    <span class="text-muted">Chưa cập nhật</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="alert alert-secondary border-0 shadow-sm">
                <i class="bi bi-graph-up me-2"></i>
                Chọn một khóa học để xem chi tiết tiến độ học viên.
            </div>
        @endif
    @endif
@endsection

@push('scripts')
    <script src="{{ asset('js/Teacher/progress.js') }}"></script>
@endpush
