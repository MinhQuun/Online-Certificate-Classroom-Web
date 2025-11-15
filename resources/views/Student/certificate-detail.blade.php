@php
    use App\Models\Certificate;
@endphp

@extends('layouts.student')

@section('title', 'Chi tiết chứng chỉ')

@push('styles')
    @php
        $pageStyle = 'css/Student/pages-certificates.css';
    @endphp
    <link rel="stylesheet" href="{{ asset($pageStyle) }}?v={{ student_asset_version($pageStyle) }}">
@endpush

@section('content')
<main class="certificates-page" data-certificates-page>
    <div class="certificates-container">
        <a href="{{ route('student.certificates.index') }}" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
        </a>

        <section class="certificate-detail card">
            <header class="detail-header">
                <span class="chip chip-type">Khóa học</span>
                <h1>{{ $certificate->course?->tenKH ?? $certificate->tenCC }}</h1>
                @if ($certificate->moTa)
                    <p class="subtitle">{{ $certificate->moTa }}</p>
                @endif
                <span class="chip {{ $statusBadges[$certificate->trangThai] ?? 'status-pending' }}">
                    {{ $statusLabels[$certificate->trangThai] ?? $certificate->trangThai }}
                </span>
            </header>

            @if ($certificate->trangThai === Certificate::STATUS_REVOKED)
                <div class="alert alert-danger">
                    <strong><i class="fa-solid fa-circle-exclamation me-1"></i> Chứng chỉ đã bị thu hồi</strong>
                    @if ($certificate->revoked_reason)
                        <div>Lý do: {{ $certificate->revoked_reason }}</div>
                    @endif
                </div>
            @endif

            <div class="detail-grid">
                <div class="detail-tile">
                    <span>Mã chứng chỉ</span>
                    <div class="copy-chip">
                        <strong>{{ $certificate->code }}</strong>
                        <button type="button" data-copy-code="{{ $certificate->code }}">Sao chép</button>
                    </div>
                </div>
                <div class="detail-tile">
                    <span>Ngày cấp</span>
                    <strong>{{ optional($certificate->issued_at)->format('d/m/Y') ?? 'Chưa có' }}</strong>
                </div>
                <div class="detail-tile">
                    <span>Hình thức cấp</span>
                    <strong>{{ $certificate->issue_mode === Certificate::ISSUE_MODE_MANUAL ? 'Thủ công' : 'Tự động' }}</strong>
                </div>
                <div class="detail-tile">
                    <span>Trạng thái file</span>
                    <strong>{{ $certificate->trangThai === Certificate::STATUS_ISSUED && $certificate->pdf_url ? 'Có thể tải' : 'Chưa sẵn sàng' }}</strong>
                </div>
            </div>

            <div class="detail-actions">
                @if ($certificate->trangThai === Certificate::STATUS_ISSUED && $certificate->pdf_url)
                    <a href="{{ route('student.certificates.download', $certificate->maCC) }}" class="btn btn-primary">
                        <i class="fa-solid fa-download me-1"></i> Tải file PDF
                    </a>
                @else
                    <button type="button" class="btn btn-secondary" disabled>
                        <i class="fa-solid fa-lock me-1"></i> Chưa thể tải
                    </button>
                @endif

                @if ($certificate->course?->slug)
                    <a href="{{ route('student.courses.show', $certificate->course->slug) }}" class="btn btn-light">
                        Xem khóa học
                    </a>
                @endif
            </div>
        </section>
    </div>
</main>
@endsection

@push('scripts')
    @php
        $pageScript = 'js/Student/certificates.js';
    @endphp
    <script src="{{ asset($pageScript) }}?v={{ student_asset_version($pageScript) }}" defer></script>
@endpush
