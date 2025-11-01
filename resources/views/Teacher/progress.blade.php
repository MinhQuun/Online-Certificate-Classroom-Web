@extends('layouts.teacher')

@section('title', 'Tiến độ học tập')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Teacher/progress.css') }}">
@endpush

@section('content')
    <section class="page-header">
        <span class="kicker">Giảng viên</span>
        <h1 class="title">Tiến độ học tập</h1>
        <p class="muted">Theo dõi quá trình học của học viên và cập nhật trạng thái kịp thời.</p>
    </section>

    @if($courses->isEmpty())
        <div class="alert alert-info border-0 shadow-sm">
            <i class="bi bi-info-circle me-2"></i>
            Bạn chưa được phân công vào khóa học nào. Khi có học viên ghi danh, dữ liệu sẽ hiển thị ở đây.
        </div>
    @else
        <form class="card border-0 shadow-sm mb-4 progress-filter" method="GET" id="progressFilterForm">
            <div class="card-body row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-muted text-uppercase small mb-1">Khóa học</label>
                    <select class="form-select form-select-lg" name="course" id="progressCourseSelector"
                        data-base-url="{{ route('teacher.progress.index') }}">
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
                        <input type="search" class="form-control" name="search" value="{{ $filters['search'] }}"
                            placeholder="Nhập tên học viên hoặc email">
                    </div>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary">
                        <i class="bi bi-filter me-1"></i> Lọc
                    </button>
                </div>
            </div>
        </form>

        @if($activeCourse)
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
                        <div class="value">{{ $metrics['active'] }}</div>
                        <div class="label">Đang học</div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="progress-card summary-card h-100">
                        <div class="value">{{ $metrics['at_risk'] }}</div>
                        <div class="label">Cần hỗ trợ</div>
                    </div>
                </div>
            </div>

            @if($enrollments->isEmpty())
                <div class="alert alert-light border">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-people fs-4 text-muted"></i>
                        <div>
                            <h5 class="mb-1">Chưa có học viên trong khóa học này</h5>
                            <p class="mb-0 text-muted">Khi học viên được ghi danh, thông tin tiến độ sẽ hiển thị tại đây.</p>
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
                                        <th>Bài học gần nhất</th>
                                        <th class="text-end">Hành động</th>
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
                                                            style="width: {{ $enrollment->progress_percent }}%;">
                                                        </div>
                                                    </div>
                                                    <span class="fw-semibold">{{ $enrollment->progress_percent }}%</span>
                                                </div>
                                            </td>
                                            <td>
                                                @if($enrollment->last_lesson_title)
                                                    <div class="fw-semibold">{{ $enrollment->last_lesson_title }}</div>
                                                    <div class="text-muted small">Thứ tự #{{ $enrollment->last_lesson_order }}</div>
                                                @else
                                                    <span class="text-muted">Chưa cập nhật</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @php
                                                    $enrollmentData = [
                                                        'course_id' => $enrollment->maKH,
                                                        'student_id' => $enrollment->maHV,
                                                        'student_name' => $enrollment->student_name,
                                                        'progress' => (int) $enrollment->progress_percent,
                                                        'status' => $enrollment->trangThai,
                                                        'last_lesson_title' => $enrollment->last_lesson_title,
                                                    ];
                                                @endphp
                                                <button class="btn btn-outline-primary btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#updateProgressModal"
                                                        data-enrollment='@json($enrollmentData)'>
                                                    <i class="bi bi-pencil me-1"></i> Cập nhật
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    @endif

    <div class="modal fade" id="updateProgressModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form method="POST" class="modal-content" id="updateProgressForm">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-graph-up-arrow me-2"></i>Cập nhật tiến độ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted text-uppercase small">Học viên</label>
                        <div id="progressStudentName" class="fw-semibold"></div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Tiến độ (%)</label>
                            <input type="number" class="form-control" name="progress_percent" id="progressPercentInput"
                                min="0" max="100" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" name="status" id="progressStatusInput" required>
                                @foreach($statusLabels as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Bài học cuối</label>
                            <select class="form-select" name="last_lesson_id" id="progressLessonInput">
                                <option value="">-- Không thay đổi --</option>
                                @if($activeCourse)
                                    @foreach($activeCourse->chapters as $chapter)
                                        <optgroup label="Chương {{ $chapter->thuTu }} - {{ $chapter->tenChuong }}">
                                            @foreach($chapter->lessons as $lesson)
                                                <option value="{{ $lesson->maBH }}">{{ $lesson->tieuDe }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>

    <div id="teacherProgressConfig" class="d-none"
        data-update-route="{{ route('teacher.progress.update', ['course' => '__COURSE__', 'student' => '__STUDENT__']) }}">
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/Teacher/progress.js') }}"></script>
@endpush
