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
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="mb-1">Khóa học đang phụ trách</h5>
                    <p class="mb-0 text-muted small">Xem nhanh số lượng học viên và mức độ hoàn thành.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-muted text-uppercase">Chỉ xem</span>
                    <a href="{{ route('teacher.progress.index') }}" class="btn btn-outline-secondary btn-sm">Tải lại</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($courseSummaries as $summary)
                        @php $isActive = $activeCourse && $activeCourse->maKH === $summary['id']; @endphp
                        <div class="col-md-6 col-xl-4">
                            <div class="progress-card summary-card h-100 {{ $isActive ? 'is-active' : '' }}">
                                <div class="d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <div class="fw-semibold mb-1">{{ $summary['name'] }}</div>
                                        <div class="text-muted small">Học viên: {{ $summary['total'] }}</div>
                                        <div class="text-muted small">Tiến độ trung bình: {{ $summary['average'] }}%</div>
                                    </div>
                                    <a class="btn btn-outline-primary btn-sm"
                                       href="{{ route('teacher.progress.show', $summary['id']) }}">
                                        {{ $isActive ? 'Đang xem' : 'Xem chi tiết' }}
                                    </a>
                                </div>
                                <div class="mt-3">
                                    <div class="progress small rounded-pill bg-light mb-2">
                                        <div class="progress-bar" role="progressbar"
                                             style="width: {{ $summary['average'] }}%;"
                                             aria-valuenow="{{ $summary['average'] }}" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="status-chip chip-success">Hoàn thành: {{ $summary['completed'] }}</span>
                                        <span class="status-chip chip-info">Đang học: {{ $summary['in_progress'] }}</span>
                                        <span class="status-chip chip-muted">Chưa bắt đầu: {{ $summary['not_started'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if($activeCourse)
            <form class="card border-0 shadow-sm mb-4 progress-filter" method="GET"
                action="{{ route('teacher.progress.show', ['course' => $activeCourse->maKH]) }}" id="progressFilterForm">
                <div class="card-body row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label text-muted text-uppercase small mb-1">Khóa học</label>
                        <select class="form-select form-select-lg" name="course" id="progressCourseSelector"
                            data-show-template="{{ route('teacher.progress.show', ['course' => '__COURSE__']) }}">
                            @foreach($courses as $course)
                                <option value="{{ $course->maKH }}" @selected($activeCourse && $activeCourse->maKH === $course->maKH)>
                                    {{ $course->tenKH }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted text-uppercase small mb-1">Trạng thái</label>
                        <select class="form-select" name="status" id="progressStatusFilter">
                            <option value="">Tất cả</option>
                            @foreach($statusLabels as $key => $label)
                                <option value="{{ $key }}" @selected($filters['status'] === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted text-uppercase small mb-1">Tìm kiếm</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="search" class="form-control" name="search" id="progressSearchInput" value="{{ $filters['search'] }}"
                                placeholder="Nhập tên học viên hoặc email">
                        </div>
                    </div>
                    <div class="col-md-2 d-grid gap-2">
                        <button class="btn btn-primary">
                            <i class="bi bi-filter me-1"></i> Lọc
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="progressFilterReset">
                            Xóa lọc
                        </button>
                    </div>
                </div>
            </form>

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
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
