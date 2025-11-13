@php
    use App\Models\Certificate;
    use App\Models\CertificateTemplate;
    use Illuminate\Support\Str;
@endphp

@extends('layouts.admin')
@section('title', 'Quản lý chứng chỉ')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Admin/admin-certificates.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/Admin/admin-certificates.js') }}" defer></script>
@endpush

@section('content')
    <section class="page-header certificate-header">
        <div>
            <span class="kicker">Admin • Certificates</span>
            <h1 class="title">Quản lý chứng chỉ</h1>
            <p class="muted">
                Theo dõi, cấu hình và cấp chứng chỉ cho học viên theo chính sách của OCC.
            </p>
        </div>
        <div class="certificate-actions">
            <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#createTemplateModal">
                <i class="bi bi-brush me-1"></i> Thêm mẫu chứng chỉ
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#manualIssueModal">
                <i class="bi bi-magic me-1"></i> Cấp chứng chỉ thủ công
            </button>
        </div>
    </section>

    @if ($errors->any())
        <div class="alert alert-danger mb-3" role="alert">
            <strong>Không thể thực hiện thao tác:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($errors->has('manual_issue'))
        <div class="alert alert-warning mb-3">
            {{ $errors->first('manual_issue') }}
        </div>
    @endif

    <div class="certificate-metrics mb-4">
        <article class="metric-card">
            <div class="metric-label">Tổng chứng chỉ</div>
            <div class="metric-value">{{ number_format($stats['total'] ?? 0) }}</div>
            <div class="metric-hint text-muted">Bao gồm AUTO & MANUAL</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Đã cấp</div>
            <div class="metric-value text-success">{{ number_format($stats['issued'] ?? 0) }}</div>
            <div class="metric-hint text-muted">{{ number_format($stats['todayIssued'] ?? 0) }} cấp hôm nay</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Tự động</div>
            <div class="metric-value text-primary">{{ number_format($stats['auto'] ?? 0) }}</div>
            <div class="metric-hint text-muted">Theo tiến độ Enrollment</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Thủ công</div>
            <div class="metric-value text-warning">{{ number_format($stats['manual'] ?? 0) }}</div>
            <div class="metric-hint text-muted">Do admin cấp tay</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Đã thu hồi</div>
            <div class="metric-value text-danger">{{ number_format($stats['revoked'] ?? 0) }}</div>
            <div class="metric-hint text-muted">Bao gồm mọi loại</div>
        </article>
    </div>

    <section class="card filter-card mb-4">
        <div class="card-body">
            <form class="row g-3 align-items-end" action="{{ route('admin.certificates.index') }}" method="get">
                <div class="col-md-3">
                    <label for="filter-type" class="form-label">Loại chứng chỉ</label>
                    <select class="form-select" id="filter-type" name="type">
                        <option value="">— Tất cả —</option>
                        @foreach ($typeLabels as $key => $label)
                            <option value="{{ $key }}" {{ $filters['type'] === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="filter-status" name="status">
                        <option value="">— Tất cả —</option>
                        @foreach ($statusLabels as $key => $label)
                            <option value="{{ $key }}" {{ $filters['status'] === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-issue-mode" class="form-label">Hình thức cấp</label>
                    <select class="form-select" id="filter-issue-mode" name="issue_mode">
                        <option value="">— Tất cả —</option>
                        @foreach ($issueModeLabels as $key => $label)
                            <option value="{{ $key }}" {{ $filters['issue_mode'] === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-search" class="form-label">Từ khóa</label>
                    <input
                        type="text"
                        id="filter-search"
                        name="search"
                        value="{{ $filters['search'] }}"
                        class="form-control"
                        placeholder="Mã, tên học viên, khóa học..."
                    >
                </div>
                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary">
                        <i class="bi bi-funnel me-1"></i> Lọc dữ liệu
                    </button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.certificates.index') }}">
                        Làm mới
                    </a>
                </div>
            </form>
        </div>
    </section>

    <div class="card certificate-list">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <h2 class="card-title mb-0">Danh sách chứng chỉ</h2>
                <small class="text-muted">Theo dõi chứng chỉ cấp cho học viên</small>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle certificate-table mb-0">
                <thead>
                    <tr>
                        <th>Mã chứng chỉ</th>
                        <th>Học viên</th>
                        <th>Đối tượng</th>
                        <th>Hình thức</th>
                        <th>Trạng thái</th>
                        <th>Ngày cấp</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($certificates as $certificate)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $certificate->code }}</div>
                                <div class="text-muted small">{{ $certificate->tenCC }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">
                                    {{ $certificate->student?->hoTen ?? 'Không xác định' }}
                                </div>
                                <div class="text-muted small">
                                    {{ $certificate->student?->user?->email ?? '---' }}
                                </div>
                            </td>
                            <td>
                                @if ($certificate->loaiCC === Certificate::TYPE_COURSE)
                                    <div class="badge rounded-pill bg-light text-dark mb-1">
                                        Khóa học
                                    </div>
                                    <div class="fw-medium">
                                        {{ $certificate->course?->tenKH ?? 'Đã xóa' }}
                                    </div>
                                @else
                                    <div class="badge rounded-pill bg-light text-dark mb-1">
                                        Combo
                                    </div>
                                    <div class="fw-medium">
                                        {{ $certificate->combo?->tenGoi ?? 'Đã xóa' }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $certificate->issue_mode === Certificate::ISSUE_MODE_AUTO ? 'bg-primary-subtle text-primary-emphasis' : 'bg-warning-subtle text-warning-emphasis' }}">
                                    {{ $issueModeLabels[$certificate->issue_mode] ?? $certificate->issue_mode }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusClass = match ($certificate->trangThai) {
                                        Certificate::STATUS_ISSUED => 'badge bg-success-subtle text-success-emphasis',
                                        Certificate::STATUS_REVOKED => 'badge bg-danger-subtle text-danger-emphasis',
                                        default => 'badge bg-secondary-subtle text-secondary-emphasis',
                                    };
                                @endphp
                                <span class="{{ $statusClass }}">
                                    {{ $statusLabels[$certificate->trangThai] ?? $certificate->trangThai }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold">
                                    {{ optional($certificate->issued_at)?->timezone(config('app.timezone', 'Asia/Ho_Chi_Minh'))?->format('d/m/Y H:i') ?? '---' }}
                                </div>
                                <div class="text-muted small">
                                    {{ $certificate->created_at?->diffForHumans() }}
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm" role="group">
                                    @if ($certificate->pdf_url)
                                        <a
                                            href="{{ $certificate->pdf_url }}"
                                            class="btn btn-outline-primary"
                                            target="_blank"
                                            rel="noopener"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    @endif
                                    @if ($certificate->trangThai !== Certificate::STATUS_REVOKED)
                                        <button
                                            class="btn btn-outline-danger"
                                            type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#revokeModal"
                                            data-action="{{ route('admin.certificates.revoke', $certificate) }}"
                                            data-code="{{ $certificate->code }}"
                                        >
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Chưa có chứng chỉ nào phù hợp với bộ lọc hiện tại.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $certificates->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <div class="row g-4 mt-4">
        <div class="col-12 col-xxl-7">
            <section class="card certificate-policy">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h2 class="card-title mb-0">Chính sách khóa học</h2>
                        <small class="text-muted">Bật/tắt cấp chứng chỉ và yêu cầu % hoàn thành</small>
                    </div>
                    <div class="policy-search">
                        <input
                            type="text"
                            class="form-control cert-policy-search"
                            placeholder="Tìm khóa học..."
                            data-policy-target="#course-policy-table"
                        >
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle" id="course-policy-table">
                        <thead>
                            <tr>
                                <th>Khóa học</th>
                                <th>Cho phép cấp</th>
                                <th>Tiến độ yêu cầu</th>
                                <th class="text-end">Cập nhật</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($coursePolicies as $course)
                                <tr data-policy-row data-name="{{ Str::lower($course->tenKH . ' ' . $course->slug) }}">
                                    <td>
                                        <div class="fw-semibold">{{ $course->tenKH }}</div>
                                        <div class="text-muted small">{{ $course->slug }}</div>
                                    </td>
                                    <td>
                                        <form
                                            class="d-flex align-items-center justify-content-between gap-2 flex-wrap"
                                            action="{{ route('admin.certificates.courses.policy', $course) }}"
                                            method="post"
                                        >
                                            @csrf
                                            @method('PUT')
                                            <div class="form-check form-switch m-0">
                                                <input type="hidden" name="certificate_enabled" value="0">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    role="switch"
                                                    name="certificate_enabled"
                                                    value="1"
                                                    {{ $course->certificate_enabled ? 'checked' : '' }}
                                                >
                                            </div>
                                            <div class="d-flex align-items-center gap-2 flex-grow-1">
                                                <input
                                                    type="number"
                                                    class="form-control form-control-sm"
                                                    name="certificate_progress_required"
                                                    min="0"
                                                    max="100"
                                                    value="{{ $course->certificate_progress_required }}"
                                                >
                                                <span class="text-muted small">%</span>
                                            </div>
                                            <button class="btn btn-sm btn-outline-primary">
                                                Lưu
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
        <div class="col-12 col-xxl-5">
            <section class="card certificate-policy">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h2 class="card-title mb-0">Chính sách combo</h2>
                        <small class="text-muted">Bật/tắt cấp chứng chỉ combo</small>
                    </div>
                    <div class="policy-search">
                        <input
                            type="text"
                            class="form-control cert-policy-search"
                            placeholder="Tìm combo..."
                            data-policy-target="#combo-policy-table"
                        >
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle" id="combo-policy-table">
                        <thead>
                            <tr>
                                <th>Combo</th>
                                <th>Cho phép cấp</th>
                                <th class="text-end">Cập nhật</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($comboPolicies as $combo)
                                <tr data-policy-row data-name="{{ Str::lower($combo->tenGoi . ' ' . $combo->slug) }}">
                                    <td>
                                        <div class="fw-semibold">{{ $combo->tenGoi }}</div>
                                        <div class="text-muted small">{{ $combo->slug }}</div>
                                    </td>
                                    <td>
                                        <form
                                            class="d-flex align-items-center justify-content-between gap-3 flex-wrap"
                                            action="{{ route('admin.certificates.combos.policy', $combo) }}"
                                            method="post"
                                        >
                                            @csrf
                                            @method('PUT')
                                            <div class="form-check form-switch m-0">
                                                <input type="hidden" name="certificate_enabled" value="0">
                                                <input
                                                    type="checkbox"
                                                    class="form-check-input"
                                                    name="certificate_enabled"
                                                    value="1"
                                                    {{ $combo->certificate_enabled ? 'checked' : '' }}
                                                >
                                            </div>
                                            <button class="btn btn-sm btn-outline-primary ms-auto">
                                                Lưu
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <section class="card certificate-templates mt-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h2 class="card-title mb-0">Thư viện mẫu chứng chỉ</h2>
                <small class="text-muted">Quản lý template cho khóa học và combo</small>
            </div>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createTemplateModal">
                <i class="bi bi-plus-circle me-1"></i> Thêm mẫu mới
            </button>
        </div>
        <div class="table-responsive">
            <table class="table align-middle template-table mb-0">
                <thead>
                    <tr>
                        <th>Mẫu</th>
                        <th>Loại</th>
                        <th>Áp dụng</th>
                        <th>Trạng thái</th>
                        <th>Cập nhật</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($templates as $template)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $template->tenTemplate }}</div>
                                <div class="text-muted small">{{ $template->moTa }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ $typeLabels[$template->loaiTemplate] ?? $template->loaiTemplate }}
                                </span>
                            </td>
                            <td>
                                @if ($template->loaiTemplate === CertificateTemplate::TYPE_COURSE)
                                    {{ $template->course?->tenKH ?? 'Mặc định cho tất cả khóa' }}
                                @else
                                    {{ $template->combo?->tenGoi ?? 'Mặc định cho tất cả combo' }}
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                    {{ $templateStatuses[$template->trangThai] ?? $template->trangThai }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold">
                                    {{ optional($template->updated_at)?->format('d/m/Y H:i') ?? '---' }}
                                </div>
                                <div class="text-muted small">
                                    {{ $template->creator?->hoTen ?? $template->creator?->name ?? 'Admin' }}
                                </div>
                            </td>
                            <td class="text-end">
                                <button
                                    class="btn btn-sm btn-outline-secondary"
                                    type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editTemplateModal"
                                    data-action="{{ route('admin.certificates.templates.update', $template) }}"
                                    data-template-id="{{ $template->maTemplate }}"
                                    data-template-name="{{ $template->tenTemplate }}"
                                    data-template-type="{{ $template->loaiTemplate }}"
                                    data-template-course="{{ $template->maKH }}"
                                    data-template-combo="{{ $template->maGoi }}"
                                    data-template-status="{{ $template->trangThai }}"
                                    data-template-url="{{ $template->template_url }}"
                                    data-template-description="{{ $template->moTa }}"
                                    data-template-design='@json($template->design_json, JSON_UNESCAPED_UNICODE)'
                                >
                                    <i class="bi bi-pencil-square me-1"></i> Sửa
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Chưa có mẫu chứng chỉ nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="modal fade" id="manualIssueModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <form
                class="modal-content manual-issue-modal"
                method="post"
                action="{{ route('admin.certificates.manual') }}"
                id="manual-issue-form"
                data-student-source="{{ route('admin.certificates.search.students') }}"
                data-course-source="{{ route('admin.certificates.search.courses') }}"
                data-combo-source="{{ route('admin.certificates.search.combos') }}"
            >
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Cấp chứng chỉ thủ công</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="manual-issue-type">Loại chứng chỉ</label>
                            <select class="form-select" id="manual-issue-type" name="issue_type">
                                <option value="{{ Certificate::TYPE_COURSE }}">Khóa học</option>
                                <option value="{{ Certificate::TYPE_COMBO }}">Combo</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="manual-issued-at">Ngày cấp</label>
                            <input type="datetime-local" class="form-control" name="issued_at" id="manual-issued-at">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="manual-title">Tiêu đề</label>
                            <input type="text" class="form-control" name="title" id="manual-title" maxlength="100">
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">Học viên</label>
                            <div class="cert-search" data-scope="student">
                                <div class="cert-search__control">
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="manual-student-search"
                                        placeholder="Nhập tên hoặc email học viên"
                                        autocomplete="off"
                                    >
                                    <button type="button" class="btn btn-link cert-search__clear" data-target="manual-student-search" aria-label="Xóa học viên">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="student_id" id="manual-student-id">
                                <div class="cert-search__meta text-muted small" id="manual-student-meta">
                                    Chưa chọn học viên
                                </div>
                                <div class="cert-search__suggestions" data-scope="student"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Khóa học / Combo</label>
                            <div class="cert-search" data-scope="target">
                                <div class="cert-search__control">
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="manual-target-search"
                                        placeholder="Nhập tên khóa học"
                                        autocomplete="off"
                                    >
                                    <button type="button" class="btn btn-link cert-search__clear" data-target="manual-target-search" aria-label="Xóa đối tượng">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="target_id" id="manual-target-id">
                                <div class="cert-search__meta text-muted small" id="manual-target-meta">
                                    Chưa chọn đối tượng
                                </div>
                                <div class="cert-search__suggestions" data-scope="target"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label" for="manual-description">Ghi chú (hiển thị trên chứng chỉ)</label>
                        <textarea class="form-control" name="description" id="manual-description" rows="3" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cấp chứng chỉ</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="revokeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="post" id="revoke-form">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Thu hồi chứng chỉ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <p>Nhập lý do thu hồi cho chứng chỉ <strong id="revoke-code">#</strong>.</p>
                    <textarea class="form-control" name="reason" rows="3" required maxlength="240"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Thu hồi</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="createTemplateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form class="modal-content template-modal" method="post" action="{{ route('admin.certificates.templates.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Thêm mẫu chứng chỉ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="create-template-name">Tên mẫu</label>
                            <input type="text" class="form-control" name="tenTemplate" id="create-template-name" required maxlength="150">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="create-template-type">Loại</label>
                            <select class="form-select template-type-select" id="create-template-type" name="loaiTemplate" data-group="create">
                                <option value="{{ CertificateTemplate::TYPE_COURSE }}">Khóa học</option>
                                <option value="{{ CertificateTemplate::TYPE_COMBO }}">Combo</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="create-template-status">Trạng thái</label>
                            <select class="form-select" id="create-template-status" name="trangThai">
                                @foreach ($templateStatuses as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6 template-target" data-group="create" data-target="course">
                            <label class="form-label" for="create-template-course">Khóa học áp dụng</label>
                            <select class="form-select" id="create-template-course" name="maKH">
                                <option value="">— Tất cả khóa học —</option>
                                @foreach ($coursePolicies as $course)
                                    <option value="{{ $course->maKH }}">{{ $course->tenKH }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 template-target d-none" data-group="create" data-target="combo">
                            <label class="form-label" for="create-template-combo">Combo áp dụng</label>
                            <select class="form-select" id="create-template-combo" name="maGoi" disabled>
                                <option value="">— Tất cả combo —</option>
                                @foreach ($comboPolicies as $combo)
                                    <option value="{{ $combo->maGoi }}">{{ $combo->tenGoi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label" for="create-template-url">Đường dẫn watermark/PDF</label>
                        <input type="text" class="form-control" id="create-template-url" name="template_url" maxlength="700">
                    </div>
                    <div class="mt-3">
                        <label class="form-label" for="create-template-description">Mô tả</label>
                        <textarea class="form-control" id="create-template-description" name="moTa" rows="2" maxlength="500"></textarea>
                    </div>
                    <div class="mt-3">
                        <label class="form-label" for="create-template-design">Design JSON (tùy chọn)</label>
                        <textarea class="form-control font-monospace" id="create-template-design" name="design_json" rows="4" placeholder='{"primary":"#2563eb"}'></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu mẫu</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editTemplateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form class="modal-content template-modal" method="post" id="edit-template-form">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Cập nhật mẫu chứng chỉ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="edit-template-name">Tên mẫu</label>
                            <input type="text" class="form-control" name="tenTemplate" id="edit-template-name" required maxlength="150">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="edit-template-type">Loại</label>
                            <select class="form-select template-type-select" id="edit-template-type" name="loaiTemplate" data-group="edit">
                                <option value="{{ CertificateTemplate::TYPE_COURSE }}">Khóa học</option>
                                <option value="{{ CertificateTemplate::TYPE_COMBO }}">Combo</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="edit-template-status">Trạng thái</label>
                            <select class="form-select" id="edit-template-status" name="trangThai">
                                @foreach ($templateStatuses as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6 template-target" data-group="edit" data-target="course">
                            <label class="form-label" for="edit-template-course">Khóa học áp dụng</label>
                            <select class="form-select" id="edit-template-course" name="maKH">
                                <option value="">— Tất cả khóa học —</option>
                                @foreach ($coursePolicies as $course)
                                    <option value="{{ $course->maKH }}">{{ $course->tenKH }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 template-target d-none" data-group="edit" data-target="combo">
                            <label class="form-label" for="edit-template-combo">Combo áp dụng</label>
                            <select class="form-select" id="edit-template-combo" name="maGoi" disabled>
                                <option value="">— Tất cả combo —</option>
                                @foreach ($comboPolicies as $combo)
                                    <option value="{{ $combo->maGoi }}">{{ $combo->tenGoi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label" for="edit-template-url">Đường dẫn watermark/PDF</label>
                        <input type="text" class="form-control" id="edit-template-url" name="template_url" maxlength="700">
                    </div>
                    <div class="mt-3">
                        <label class="form-label" for="edit-template-description">Mô tả</label>
                        <textarea class="form-control" id="edit-template-description" name="moTa" rows="2" maxlength="500"></textarea>
                    </div>
                    <div class="mt-3">
                        <label class="form-label" for="edit-template-design">Design JSON</label>
                        <textarea class="form-control font-monospace" id="edit-template-design" name="design_json" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
@endsection
