@php
    use App\Models\Certificate;
    $activeCourse = $courses->firstWhere('maKH', $activeCourseId);
@endphp

@extends('layouts.teacher')

@section('title', 'Theo dõi chứng chỉ')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Teacher/certificates.css') }}">
@endpush

@section('content')
    <section class="page-header page-header--split">
        <div>
            <span class="kicker">Giảng viên</span>
            <h1 class="title"><i class="bi bi-award me-2"></i> Theo dõi chứng chỉ</h1>
            <p class="muted mb-0">Theo dõi tình trạng cấp chứng chỉ cho từng học viên theo khóa học bạn phụ trách.</p>
        </div>
        <div class="page-header-actions">
            <span class="badge rounded-pill text-bg-light border fw-semibold d-inline-flex align-items-center gap-2">
                <i class="bi bi-shield-check text-success"></i> Chỉ xem
            </span>
        </div>
    </section>

    @if ($courses->isEmpty())
        <div class="empty-state card">
            <div class="card-body text-center">
                <i class="bi bi-journal-bookmark mb-2"></i>
                <h5 class="mb-1">Bạn chưa có khóa học nào</h5>
                <p class="text-muted mb-0">Hãy thêm khóa học để theo dõi chứng chỉ.</p>
            </div>
        </div>
    @else
        <div class="card certificate-hero shadow-sm mb-4">
            <div class="card-body d-flex flex-wrap gap-4 align-items-start justify-content-between">
                <div class="hero-copy">
                    <p class="eyebrow text-uppercase small text-muted mb-1">Khóa học đang xem</p>
                    <h3 class="mb-2">{{ $activeCourse?->tenKH ?? 'Chọn khóa học' }}</h3>
                    <p class="text-muted mb-3">Số liệu hiển thị dựa trên lần cấp gần nhất của từng học viên trong khóa.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="pill pill-ghost">Tổng {{ number_format($metrics['total']) }} học viên</span>
                        <span class="pill pill-ghost">{{ number_format($metrics['issued']) }} đã cấp</span>
                        <span class="pill pill-ghost">{{ number_format($metrics['pending']) }} đang xét</span>
                    </div>
                </div>
                <form method="get" class="hero-form">
                    <label for="course-select" class="form-label">Chọn khóa học</label>
                    <div class="d-flex flex-wrap gap-2">
                        <select id="course-select" name="course" class="form-select form-select-lg">
                            @foreach ($courses as $course)
                                <option value="{{ $course->maKH }}" {{ $course->maKH == $activeCourseId ? 'selected' : '' }}>
                                    {{ $course->tenKH }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-eye me-1"></i> Xem
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-3 mb-4 certificate-stat-row">
            <div class="col-6 col-md-4 col-xl">
                <div class="summary-card certificate-stat total">
                    <div class="stat-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="label">Tổng học viên</div>
                    <div class="value">{{ number_format($metrics['total']) }}</div>
                    <p class="hint text-muted mb-0">Tất cả học viên đã ghi danh trong khóa.</p>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl">
                <div class="summary-card certificate-stat issued">
                    <div class="stat-icon">
                        <i class="bi bi-patch-check"></i>
                    </div>
                    <div class="label">Đã cấp</div>
                    <div class="value">{{ number_format($metrics['issued']) }}</div>
                    <p class="hint text-muted mb-0">Chứng chỉ đã phát hành thành công.</p>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl">
                <div class="summary-card certificate-stat pending">
                    <div class="stat-icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div class="label">Đang xét</div>
                    <div class="value">{{ number_format($metrics['pending']) }}</div>
                    <p class="hint text-muted mb-0">Đang kiểm tra điều kiện cấp.</p>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl">
                <div class="summary-card certificate-stat revoked">
                    <div class="stat-icon">
                        <i class="bi bi-x-octagon"></i>
                    </div>
                    <div class="label">Thu hồi</div>
                    <div class="value">{{ number_format($metrics['revoked']) }}</div>
                    <p class="hint text-muted mb-0">Đã bị thu hồi vì vi phạm/điều chỉnh.</p>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl">
                <div class="summary-card certificate-stat none">
                    <div class="stat-icon">
                        <i class="bi bi-dash-circle"></i>
                    </div>
                    <div class="label">Chưa cấp</div>
                    <div class="value">{{ number_format($metrics['none']) }}</div>
                    <p class="hint text-muted mb-0">Chưa đạt điều kiện cấp chứng chỉ.</p>
                </div>
            </div>
        </div>

        <form method="get" class="certificate-filters card shadow-sm mb-4">
            <input type="hidden" name="course" value="{{ $activeCourseId }}">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="filter-status" class="form-label">Trạng thái chứng chỉ</label>
                        <select id="filter-status" name="status" class="form-select">
                            <option value="">Tất cả</option>
                            @foreach ($statusLabels as $key => $label)
                                <option value="{{ $key }}" {{ $filters['status'] === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="filter-search" class="form-label">Tên hoặc email học viên</label>
                        <div class="input-with-icon">
                            <i class="bi bi-search"></i>
                            <input
                                type="text"
                                id="filter-search"
                                name="search"
                                class="form-control"
                                placeholder="Ví dụ: Nguyen Van A"
                                value="{{ $filters['search'] }}"
                            >
                        </div>
                    </div>
                    <div class="col-md-4 d-flex gap-2 flex-wrap">
                        <button class="btn btn-primary flex-grow-1 flex-md-grow-0" type="submit">
                            <i class="bi bi-funnel me-1"></i> Lọc danh sách
                        </button>
                        <a href="{{ route('teacher.certificates.index', ['course' => $activeCourseId]) }}" class="btn btn-outline-secondary flex-grow-1 flex-md-grow-0">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Đặt lại
                        </a>
                    </div>
                </div>
            </div>
        </form>

        @if ($rows->count())
            <div class="card shadow-sm certificate-table-card">
                <div class="table-responsive">
                    <table class="table align-middle certificate-table mb-0">
                        <thead>
                            <tr>
                                <th>Học viên</th>
                                <th>Email</th>
                                <th class="text-center">Tiến độ</th>
                                <th class="text-center">Trạng thái</th>
                                <th>Ngày cấp gần nhất</th>
                                <th>Mã chứng chỉ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                @php
                                    $certificate = $row['certificate'];
                                    $status = $row['status'];
                                    $badgeClass = match($status) {
                                        Certificate::STATUS_ISSUED => 'status-issued',
                                        Certificate::STATUS_PENDING => 'status-pending',
                                        Certificate::STATUS_REVOKED => 'status-revoked',
                                        default => 'status-none',
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $row['student']?->hoTen ?? 'Học viên #' . $row['enrollment']->maHV }}</div>
                                        <small class="text-muted">Mã ghi danh: {{ $row['enrollment']->maHV }}</small>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $row['email'] ?? 'Đang cập nhật' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="progress-chip">
                                            <span>{{ $row['progress'] }}%</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="certificate-status {{ $badgeClass }}">
                                            {{ $statusLabels[$status] ?? $status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ optional($certificate?->issued_at)->format('d/m/Y') ?? 'Chưa cấp' }}
                                        </div>
                                        <small class="text-muted">Cập nhật theo lần cấp gần nhất</small>
                                    </td>
                                    <td>
                                        @if ($certificate?->code)
                                            <code class="code-chip">{{ $certificate->code }}</code>
                                        @else
                                            <span class="text-muted">Đang cập nhật</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="empty-state card">
                <div class="card-body text-center">
                    <i class="bi bi-inboxes mb-2"></i>
                    <h5 class="mb-1">Không tìm thấy dữ liệu</h5>
                    <p class="text-muted mb-0">Chưa có học viên phù hợp với điều kiện lọc hiện tại.</p>
                </div>
            </div>
        @endif
    @endif
@endsection
