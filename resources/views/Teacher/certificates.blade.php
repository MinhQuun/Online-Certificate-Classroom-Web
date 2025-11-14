@php
    use App\Models\Certificate;
@endphp

@extends('layouts.teacher')

@section('title', 'Theo dõi chứng chỉ')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Teacher/certificates.css') }}">
@endpush

@section('content')
<div class="teacher-certificate-page">
    <div class="certificate-header card shadow-sm mb-4">
        <div>
            <p class="text-muted mb-1 text-uppercase small">Giám sát</p>
            <h1 class="h4">Chứng chỉ khóa học</h1>
            <p class="mb-0 text-muted">Theo dõi tình trạng cấp chứng chỉ cho từng học viên theo khóa học bạn phụ trách.</p>
        </div>
        <form method="get" class="course-selector">
            <label for="course-select" class="form-label">Khóa học</label>
            <div class="d-flex gap-2">
                <select id="course-select" name="course" class="form-select">
                    @foreach ($courses as $course)
                        <option value="{{ $course->maKH }}" {{ $course->maKH == $activeCourseId ? 'selected' : '' }}>
                            {{ $course->tenKH }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Xem</button>
            </div>
        </form>
    </div>

    <div class="certificate-stats mb-4">
        <div class="stat-card">
            <span class="label">Tổng học viên</span>
            <strong>{{ number_format($metrics['total']) }}</strong>
        </div>
        <div class="stat-card">
            <span class="label">Đã cấp</span>
            <strong class="text-success">{{ number_format($metrics['issued']) }}</strong>
        </div>
        <div class="stat-card">
            <span class="label">Đang xét</span>
            <strong class="text-warning">{{ number_format($metrics['pending']) }}</strong>
        </div>
        <div class="stat-card">
            <span class="label">Thu hồi</span>
            <strong class="text-danger">{{ number_format($metrics['revoked']) }}</strong>
        </div>
        <div class="stat-card">
            <span class="label">Chưa cấp</span>
            <strong>{{ number_format($metrics['none']) }}</strong>
        </div>
    </div>

    <form method="get" class="certificate-filters card shadow-sm mb-4">
        <input type="hidden" name="course" value="{{ $activeCourseId }}">
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
            <div class="col-md-4">
                <label for="filter-search" class="form-label">Tên hoặc email học viên</label>
                <input
                    type="text"
                    id="filter-search"
                    name="search"
                    class="form-control"
                    placeholder="Ví dụ: Nguyen Van A"
                    value="{{ $filters['search'] }}"
                >
            </div>
            <div class="col-md-5 d-flex gap-2">
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-funnel me-1"></i> Lọc danh sách
                </button>
                <a href="{{ route('teacher.certificates.index', ['course' => $activeCourseId]) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Đặt lại
                </a>
            </div>
        </div>
    </form>

    @if ($activeCourseId && $rows->count())
        <div class="card shadow-sm">
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
                                </td>
                                <td>
                                    <span class="text-muted">{{ $row['email'] ?? '—' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="progress-chip">{{ $row['progress'] }}%</span>
                                </td>
                                <td class="text-center">
                                    <span class="certificate-status {{ $badgeClass }}">
                                        {{ $statusLabels[$status] ?? $status }}
                                    </span>
                                </td>
                                <td>
                                    {{ optional($certificate?->issued_at)->format('d/m/Y') ?? 'Chưa có' }}
                                </td>
                                <td>
                                    {{ $certificate?->code ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif ($activeCourseId)
        <div class="empty-state card">
            <div class="card-body text-center">
                <i class="bi bi-inboxes mb-2"></i>
                <h5 class="mb-1">Không tìm thấy dữ liệu</h5>
                <p class="text-muted mb-0">Chưa có học viên nào phù hợp với điều kiện lọc.</p>
            </div>
        </div>
    @else
        <div class="empty-state card">
            <div class="card-body text-center">
                <i class="bi bi-journal-bookmark mb-2"></i>
                <h5 class="mb-1">Bạn chưa có khóa học nào</h5>
                <p class="text-muted mb-0">Hãy thêm khóa học để theo dõi chứng chỉ.</p>
            </div>
        </div>
    @endif
</div>
@endsection
