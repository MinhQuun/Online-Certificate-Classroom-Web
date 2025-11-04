@extends('layouts.admin')
@section('title', 'Quản lý khuyến mãi')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Admin/admin-promotions.css') }}">
@endpush

@php
    use App\Models\Promotion;

    $targetOptions = [
        '' => 'Tất cả đối tượng',
        Promotion::TARGET_COMBO => 'Combo khóa học',
        Promotion::TARGET_COURSE => 'Khóa học đơn lẻ',
        Promotion::TARGET_BOTH => 'Combo & khóa học',
    ];

    $statusOptions = [
        '' => 'Tất cả trạng thái',
        'ACTIVE' => 'Đang kích hoạt',
        'INACTIVE' => 'Tạm dừng',
        'EXPIRED' => 'Hết hạn',
    ];

    $typeOptions = [
        '' => 'Tất cả loại ưu đãi',
        Promotion::TYPE_PERCENT => 'Giảm theo %',
        Promotion::TYPE_FIXED => 'Giảm theo số tiền',
        Promotion::TYPE_GIFT => 'Quà tặng',
    ];

    $formatCurrency = static fn ($value) => number_format((float) $value, 0, ',', '.') . ' VND';
@endphp

@section('content')
    <section class="page-header">
        <span class="kicker">Admin</span>
        <h1 class="title">Khuyến mãi</h1>
        <p class="muted">Quản lý các chương trình khuyến mãi cho combo và khóa học đơn lẻ.</p>
    </section>

    @if ($errors->any())
        <div class="alert alert-danger validation-errors" role="alert" id="validation-errors">
            <h6 class="mb-2">Thông tin chưa hợp lệ, vui lòng kiểm tra:</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="promo-stats mb-4">
        <article class="stats-card">
            <span class="stats-label">Tổng khuyến mãi</span>
            <span class="stats-value">{{ number_format($stats['total'] ?? 0) }}</span>
            <span class="stats-meta">Toàn bộ chương trình</span>
        </article>
        <article class="stats-card">
            <span class="stats-label">Đang hoạt động</span>
            <span class="stats-value text-success">{{ number_format($stats['active'] ?? 0) }}</span>
            <span class="stats-meta">Khuyến mãi kích hoạt</span>
        </article>
        <article class="stats-card">
            <span class="stats-label">Tạm dừng</span>
            <span class="stats-value text-warning">{{ number_format($stats['inactive'] ?? 0) }}</span>
            <span class="stats-meta">Đang tạm khoá</span>
        </article>
        <article class="stats-card">
            <span class="stats-label">Hết hạn</span>
            <span class="stats-value text-danger">{{ number_format($stats['expired'] ?? 0) }}</span>
            <span class="stats-meta">Khuyến mãi đã qua</span>
        </article>
    </section>

    <div class="card promo-filter mb-4">
        <div class="card-body">
            <form method="get" action="{{ route('admin.promotions.index') }}" class="row g-3 align-items-end">
                <div class="col-lg-4">
                    <label for="filter-search" class="form-label">Tìm kiếm</label>
                    <input
                        type="text"
                        id="filter-search"
                        name="q"
                        class="form-control"
                        value="{{ request('q') }}"
                        placeholder="Nhập tên khuyến mãi, mã khuyến mãi..."
                    >
                </div>
                <div class="col-lg-3">
                    <label for="filter-target" class="form-label">Đối tượng</label>
                    <select name="target" id="filter-target" class="form-select">
                        @foreach ($targetOptions as $value => $label)
                            <option value="{{ $value }}" {{ request('target') === (string) $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3">
                    <label for="filter-type" class="form-label">Loại ưu đãi</label>
                    <select name="type" id="filter-type" class="form-select">
                        @foreach ($typeOptions as $value => $label)
                            <option value="{{ $value }}" {{ request('type') === (string) $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <label for="filter-status" class="form-label">Trạng thái</label>
                    <select name="status" id="filter-status" class="form-select">
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === (string) $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex flex-wrap gap-2 justify-content-end">
                    <button type="submit" class="btn btn-outline-primary">Lọc</button>
                    <a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card promo-card">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h2 class="h5 mb-1">Danh sách khuyến mãi</h2>
                <p class="muted mb-0">Theo dõi trạng thái, đối tượng và hiệu lực của khuyến mãi.</p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#promotionCreateModal">
                <i class="bi bi-plus-circle me-2"></i> Tạo khuyến mãi
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle promo-table">
                <thead>
                <tr>
                    <th scope="col">Khuyến mãi</th>
                    <th scope="col">Đối tượng</th>
                    <th scope="col">Ưu đãi</th>
                    <th scope="col">Thời gian</th>
                    <th scope="col">Liên kết</th>
                    <th scope="col">Trạng thái</th>
                    <th scope="col" class="text-end">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($promotions as $promotion)
                    @php
                        $payload = [
                            'id' => $promotion->maKM,
                            'name' => $promotion->tenKM,
                            'description' => $promotion->moTa,
                            'target' => $promotion->apDungCho,
                            'type' => $promotion->loaiUuDai,
                            'value' => (float) $promotion->giaTriUuDai,
                            'start' => optional($promotion->ngayBatDau)->format('Y-m-d'),
                            'end' => optional($promotion->ngayKetThuc)->format('Y-m-d'),
                            'limit' => $promotion->soLuongGioiHan,
                            'status' => $promotion->trangThai,
                        ];

                        $typeLabel = match ($promotion->loaiUuDai) {
                            Promotion::TYPE_PERCENT => 'Giảm ' . (int) $promotion->giaTriUuDai . '%',
                            Promotion::TYPE_FIXED => 'Giảm ' . $formatCurrency($promotion->giaTriUuDai),
                            default => 'Quà tặng/Khác',
                        };

                        $targetLabel = $targetOptions[$promotion->apDungCho] ?? $promotion->apDungCho;
                    @endphp
                    <tr>
                        <td>
                            <div class="promo-name">
                                <strong>{{ $promotion->tenKM }}</strong>
                                <div class="muted small">Mã #: {{ $promotion->maKM }}</div>
                            </div>
                            @if ($promotion->moTa)
                                <div class="promo-desc text-muted small">
                                    {{ \Illuminate\Support\Str::limit($promotion->moTa, 110) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark promo-target">{{ $targetLabel }}</span>
                        </td>
                        <td>
                            <div class="promo-benefit">
                                <span class="badge bg-info text-dark">{{ $typeLabel }}</span>
                                @if ($promotion->soLuongGioiHan)
                                    <div class="muted small mt-1">
                                        Giới hạn: {{ number_format($promotion->soLuongGioiHan) }} lần
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="promo-duration">
                                <div>{{ optional($promotion->ngayBatDau)->format('d/m/Y') }} - {{ optional($promotion->ngayKetThuc)->format('d/m/Y') }}</div>
                                <div class="muted small">
                                    {!! $promotion->trangThai === 'ACTIVE' ? '<i class=\"bi bi-clock-history me-1\"></i>Đang hoạt động' : '' !!}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="promo-relations">
                                <span class="relation-pill">
                                    <i class="bi bi-layers me-1"></i>{{ $promotion->combos_count }} combo
                                </span>
                                <span class="relation-pill">
                                    <i class="bi bi-journal-text me-1"></i>{{ $promotion->courses_count }} khóa học
                                </span>
                            </div>
                        </td>
                        <td>
                            @php
                                $statusClass = match ($promotion->trangThai) {
                                    'ACTIVE' => 'status-active',
                                    'INACTIVE' => 'status-paused',
                                    'EXPIRED' => 'status-expired',
                                    default => '',
                                };
                            @endphp
                            <span class="promo-status {{ $statusClass }}">{{ $statusOptions[$promotion->trangThai] ?? $promotion->trangThai }}</span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-primary-soft"
                                    data-bs-toggle="modal"
                                    data-bs-target="#promotionEditModal"
                                    data-promotion='@json($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)'
                                >
                                    <i class="bi bi-pencil-square me-1"></i>Sửa
                                </button>
                                <form method="post" action="{{ route('admin.promotions.destroy', $promotion) }}" data-confirm-delete>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger-soft">
                                        <i class="bi bi-trash me-1"></i>Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="promo-empty text-center">
                                <i class="bi bi-gift me-2"></i>Chưa có khuyến mãi nào. Nhấn <strong>Tạo khuyến mãi</strong> để bắt đầu nha!
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            @include('components.pagination', [
                'paginator' => $promotions,
                'ariaLabel' => 'Đổi trang khuyến mãi',
                'containerClass' => 'mb-0',
            ])
        </div>
    </div>

    {{-- Modal: Create --}}
    <div class="modal fade" id="promotionCreateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <form class="modal-content promotion-form" action="{{ route('admin.promotions.store') }}" method="post" data-promotion-form="create">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tạo khuyến mãi mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label class="form-label">Tên khuyến mãi <span class="text-danger">*</span></label>
                        <input type="text" name="tenKM" class="form-control" required value="{{ old('tenKM') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Mô tả (không bắt buộc)</label>
                        <textarea name="moTa" class="form-control" rows="3">{{ old('moTa') }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Đối tượng <span class="text-danger">*</span></label>
                        <select name="apDungCho" class="form-select" required>
                            <option value="">-- Chọn --</option>
                            @foreach ($targetOptions as $value => $label)
                                @if ($value !== '')
                                    <option value="{{ $value }}" {{ old('apDungCho') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Loại ưu đãi <span class="text-danger">*</span></label>
                        <select name="loaiUuDai" class="form-select" required data-promotion-type>
                            @foreach ($typeOptions as $value => $label)
                                @if ($value !== '')
                                    <option value="{{ $value }}" {{ old('loaiUuDai') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4" data-value-wrapper>
                        <label class="form-label" data-value-label>Giảm giá: <span class="text-danger">*</span></label>
                        <input
                            type="number"
                            name="giaTriUuDai"
                            class="form-control"
                            value="{{ old('giaTriUuDai') }}"
                            min="0"
                            step="0.01"
                            required
                        >
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                        <input type="date" name="ngayBatDau" class="form-control" value="{{ old('ngayBatDau') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                        <input type="date" name="ngayKetThuc" class="form-control" value="{{ old('ngayKetThuc') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Giới hạn số lần <span class="text-muted">(tùy chọn)</span></label>
                        <input type="number" name="soLuongGioiHan" class="form-control" value="{{ old('soLuongGioiHan') }}" min="1">
                        <div class="form-text">Để trống nếu không giới hạn.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select name="trangThai" class="form-select" required>
                            <option value="ACTIVE" {{ old('trangThai', 'ACTIVE') === 'ACTIVE' ? 'selected' : '' }}>Đang kích hoạt</option>
                            <option value="INACTIVE" {{ old('trangThai') === 'INACTIVE' ? 'selected' : '' }}>Tạm dừng</option>
                            <option value="EXPIRED" {{ old('trangThai') === 'EXPIRED' ? 'selected' : '' }}>Hết hạn</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button class="btn btn-primary">Lưu khuyến mãi</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Edit --}}
    <div class="modal fade" id="promotionEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <form class="modal-content promotion-form" method="post" data-promotion-form="edit">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Cập nhật khuyến mãi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label class="form-label">Tên khuyến mãi <span class="text-danger">*</span></label>
                        <input type="text" name="tenKM" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Mô tả</label>
                        <textarea name="moTa" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Đối tượng <span class="text-danger">*</span></label>
                        <select name="apDungCho" class="form-select" required>
                            @foreach ($targetOptions as $value => $label)
                                @if ($value !== '')
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Loại ưu đãi <span class="text-danger">*</span></label>
                        <select name="loaiUuDai" class="form-select" required data-promotion-type>
                            @foreach ($typeOptions as $value => $label)
                                @if ($value !== '')
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4" data-value-wrapper>
                        <label class="form-label" data-value-label>Giảm giá: <span class="text-danger">*</span></label>
                        <input type="number" name="giaTriUuDai" class="form-control" min="0" step="0.01" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                        <input type="date" name="ngayBatDau" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                        <input type="date" name="ngayKetThuc" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Giới hạn số lần</label>
                        <input type="number" name="soLuongGioiHan" class="form-control" min="1">
                        <div class="form-text">Để trống nếu không giới hạn.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select name="trangThai" class="form-select" required>
                            <option value="ACTIVE">Đang kích hoạt</option>
                            <option value="INACTIVE">Tạm dừng</option>
                            <option value="EXPIRED">Hết hạn</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@php
    $dataset = [
        'statusOptions' => $statusOptions,
        'targetOptions' => array_filter($targetOptions, static fn ($key) => $key !== '', ARRAY_FILTER_USE_KEY),
        'typeOptions' => array_filter($typeOptions, static fn ($key) => $key !== '', ARRAY_FILTER_USE_KEY),
        'updateUrlTemplate' => route('admin.promotions.update', ['promotion' => '__ID__']),
    ];
@endphp

@push('scripts')
    <script id="promotion-form-dataset" type="application/json">
        {!! json_encode($dataset, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
    <script src="{{ asset('js/Admin/promotions.js') }}" defer></script>
@endpush
