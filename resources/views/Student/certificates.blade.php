@php
    use App\Models\Certificate;
    use Illuminate\Support\Str;
@endphp

@extends('layouts.student')

@section('title', 'Chứng chỉ của tôi')

@push('styles')
    @php
        $pageStyle = 'css/Student/pages-certificates.css';
    @endphp
    <link rel="stylesheet" href="{{ asset($pageStyle) }}?v={{ student_asset_version($pageStyle) }}">
@endpush

@section('content')
<main class="certificates-page" data-certificates-page>
    <div class="certificates-container">
        <div class="certificates-hero card">
            <div class="hero-text">
                <p class="eyebrow">Student / Certificates</p>
                <h1>
                    <i class="fa-solid fa-award"></i>
                    Chứng chỉ của tôi
                </h1>
                <p class="subtitle">
                    Tổng hợp toàn bộ chứng chỉ nội bộ OCC mà bạn đã được cấp sau khi hoàn thành khóa học.
                </p>
            </div>
            <div class="hero-metrics">
                <div class="metric">
                    <span>Tổng số</span>
                    <strong>{{ number_format($metrics['total']) }}</strong>
                </div>
                <div class="metric">
                    <span>Đã cấp</span>
                    <strong class="text-success">{{ number_format($metrics['issued']) }}</strong>
                </div>
                <div class="metric">
                    <span>Chờ xét</span>
                    <strong class="text-warning">{{ number_format($metrics['pending']) }}</strong>
                </div>
                <div class="metric">
                    <span>Thu hồi</span>
                    <strong class="text-danger">{{ number_format($metrics['revoked']) }}</strong>
                </div>
            </div>
        </div>

        <form id="certificateFilters" class="certificates-filter card" method="get" action="{{ route('student.certificates.index') }}">
            <div class="filter-group">
                <label for="filter-status">Trạng thái</label>
                <select id="filter-status" class="form-select" name="status" data-auto-submit>
                    <option value="">Tất cả</option>
                    @foreach ($statusLabels as $key => $label)
                        <option value="{{ $key }}" {{ $filters['status'] === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label for="filter-search">Từ khóa</label>
                <input
                    type="text"
                    id="filter-search"
                    name="q"
                    class="form-control"
                    placeholder="Mã chứng chỉ, tên khóa học..."
                    value="{{ $filters['search'] }}"
                >
            </div>
            <div class="filter-actions">
                <button class="btn btn-primary w-100" type="submit">
                    <i class="fa-solid fa-filter me-1"></i> Lọc kết quả
                </button>
                <a href="{{ route('student.certificates.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="fa-solid fa-arrow-rotate-left me-1"></i> Đặt lại
                </a>
            </div>
        </form>

        @if ($certificates->count())
            <div class="certificates-grid">
                @foreach ($certificates as $certificate)
                    @php
                        $status = $certificate->trangThai;
                        $statusClass = $statusBadges[$status] ?? 'status-pending';
                        $subjectName = $certificate->course?->tenKH ?? $certificate->tenCC;
                        $canDownload = $certificate->trangThai === Certificate::STATUS_ISSUED && $certificate->pdf_url;
                    @endphp
                    <article class="certificate-card">
                        <div class="card-top">
                            <div>
                                <span class="chip chip-type">Khóa học</span>
                                <h3 class="card-title">{{ $subjectName }}</h3>
                                <p class="card-desc">{{ Str::limit($certificate->moTa ?? 'Hoàn thành theo yêu cầu của OCC', 140) }}</p>
                            </div>
                            <span class="chip {{ $statusClass }}">{{ $statusLabels[$status] ?? $status }}</span>
                        </div>
                        <div class="card-meta">
                            <div>
                                <span>Mã chứng chỉ</span>
                                <div class="copy-chip">
                                    <strong>{{ $certificate->code }}</strong>
                                    <button type="button" data-copy-code="{{ $certificate->code }}">Sao chép</button>
                                </div>
                            </div>
                            <div>
                                <span>Ngày cấp</span>
                                <strong>{{ optional($certificate->issued_at)->format('d/m/Y') ?? 'Chưa có' }}</strong>
                            </div>
                            <div>
                                <span>Trạng thái file</span>
                                <strong>{{ $canDownload ? 'Có thể tải' : 'Chưa sẵn sàng' }}</strong>
                            </div>
                        </div>
                        <div class="card-actions">
                            <a href="{{ route('student.certificates.show', $certificate->maCC) }}" class="btn btn-light">
                                <i class="fa-solid fa-eye me-1"></i> Xem chi tiết
                            </a>
                            @if ($canDownload)
                                <a href="{{ route('student.certificates.download', $certificate->maCC) }}" class="btn btn-primary">
                                    <i class="fa-solid fa-download me-1"></i> Tải PDF
                                </a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($certificates->hasPages())
                <div class="pagination-wrapper">
                    {{ $certificates->links() }}
                </div>
            @endif
        @else
            <div class="certificates-empty card">
                <i class="fa-solid fa-inbox"></i>
                <h3>Chưa có chứng chỉ nào</h3>
                <p>Hoàn thành các khóa học của bạn để nhận chứng chỉ từ OCC.</p>
                <a href="{{ route('student.courses.index') }}" class="btn btn-primary">
                    <i class="fa-solid fa-compass me-1"></i> Khám phá khóa học
                </a>
            </div>
        @endif
    </div>
</main>
@endsection

@push('scripts')
    @php
        $pageScript = 'js/Student/certificates.js';
    @endphp
    <script src="{{ asset($pageScript) }}?v={{ student_asset_version($pageScript) }}" defer></script>
@endpush
