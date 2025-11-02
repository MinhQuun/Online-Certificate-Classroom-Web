@extends('layouts.admin')
@section('title', 'Quản lý combo khóa học')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Admin/admin-combos.css') }}">
@endpush

@section('content')
    @php
        $statusOptions = [
            '' => 'Tất cả trạng thái',
            'PUBLISHED' => 'Đang mở bán',
            'DRAFT' => 'Bản nháp',
            'ARCHIVED' => 'Đã lưu trữ',
        ];

        $availabilityOptions = [
            '' => 'Tất cả thời điểm',
            'active' => 'Đang diễn ra',
            'upcoming' => 'Sắp mở bán',
            'expired' => 'Đã kết thúc',
        ];

        $formatCurrency = function ($value) {
            $value = (int) round($value);
            return number_format($value, 0, ',', '.') . ' VND';
        };
    @endphp

    <section class="page-header">
        <span class="kicker">Admin</span>
        <h1 class="title">Combo khóa học</h1>
        <p class="muted">Cấu hình các gói combo, giá bán và khuyến mãi theo dịp.</p>
    </section>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Thông tin chưa hợp lệ.</strong> Vui lòng kiểm tra lại:
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-3 combo-stats mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <span class="stat-label">Tổng combo</span>
                <strong class="stat-value">{{ $stats['total'] ?? 0 }}</strong>
                <span class="stat-meta text-muted">Toàn bộ gói đã tạo</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <span class="stat-label">Đang mở bán</span>
                <strong class="stat-value text-success">{{ $stats['published'] ?? 0 }}</strong>
                <span class="stat-meta text-muted">Combo hiển thị cho học viên</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <span class="stat-label">Bản nháp</span>
                <strong class="stat-value text-warning">{{ $stats['draft'] ?? 0 }}</strong>
                <span class="stat-meta text-muted">Chưa công khai</span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <span class="stat-label">Lưu trữ</span>
                <strong class="stat-value text-muted">{{ $stats['archived'] ?? 0 }}</strong>
                <span class="stat-meta text-muted">Combo không còn bán</span>
            </div>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="{{ route('admin.combos.index') }}" class="row g-3 align-items-end">
                <div class="col-lg-4">
                    <label for="filter-search" class="form-label">Tìm kiếm</label>
                    <input
                        type="text"
                        id="filter-search"
                        name="q"
                        class="form-control"
                        value="{{ request('q') }}"
                        placeholder="Nhập tên combo, mã combo..."
                    >
                </div>
                <div class="col-lg-3">
                    <label for="filter-status" class="form-label">Trạng thái</label>
                    <select name="status" id="filter-status" class="form-select">
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3">
                    <label for="filter-availability" class="form-label">Khung thời gian</label>
                    <select name="availability" id="filter-availability" class="form-select">
                        @foreach ($availabilityOptions as $value => $label)
                            <option value="{{ $value }}" {{ request('availability') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-funnel me-1"></i> Lọc
                    </button>
                    <a href="{{ route('admin.combos.index') }}" class="btn btn-outline-secondary" title="Xóa bộ lọc">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
        <div>
            <h2 class="section-title mb-0">Danh sách combo</h2>
            <p class="text-muted small mb-0">
                Mỗi combo nên có tối thiểu hai khóa học kèm giá ưu đãi so với mua lẻ.
            </p>
        </div>
        <button
            type="button"
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#comboCreateModal"
        >
            <i class="bi bi-plus-circle me-1"></i> Tạo combo mới
        </button>
    </div>

    @if ($combos->isEmpty())
        <div class="empty-state">
            <div class="empty-icon" aria-hidden="true">
                <i class="bi bi-box-seam"></i>
            </div>
            <h3>Chưa có combo nào</h3>
            <p class="text-muted mb-3">
                Hãy kết hợp các khóa học theo lộ trình và mức giá hấp dẫn để học viên dễ dàng lựa chọn.
            </p>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#comboCreateModal">
                Bắt đầu tạo combo
            </button>
        </div>
    @else
        <div class="table-responsive combo-table-wrapper">
            <table class="table table-hover align-middle combo-table">
                <thead>
                    <tr>
                        <th scope="col">Combo</th>
                        <th scope="col">Khóa học</th>
                        <th scope="col" class="text-end">Giá bán</th>
                        <th scope="col">Thời gian</th>
                        <th scope="col">Trạng thái</th>
                        <th scope="col" class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($combos as $combo)
                        @php
                            $activePromotion = $combo->active_promotion;
                            $comboPayload = [
                                'id' => $combo->maGoi,
                                'name' => $combo->tenGoi,
                                'description' => $combo->moTa,
                                'price' => (int) round($combo->gia),
                                'start_date' => optional($combo->ngayBatDau)->format('Y-m-d'),
                                'end_date' => optional($combo->ngayKetThuc)->format('Y-m-d'),
                                'status' => $combo->trangThai,
                                'image' => $combo->hinhanh ? asset($combo->hinhanh) : null,
                                'promotion_id' => $activePromotion?->maKM,
                                'promotion_price' => $activePromotion && $activePromotion->pivot
                                    ? (int) $activePromotion->pivot->giaUuDai
                                    : null,
                                'courses' => $combo->courses->map(fn ($courseItem) => [
                                    'id' => $courseItem->maKH,
                                    'name' => $courseItem->tenKH,
                                    'price' => (int) $courseItem->hocPhi,
                                    'order' => (int) ($courseItem->pivot->thuTu ?? 1),
                                ])->values(),
                            ];

                            $availabilityBadge = match (true) {
                                $combo->is_active => ['success', 'Đang mở bán'],
                                !$combo->is_active && $combo->trangThai === 'PUBLISHED' => ['warning', 'Chưa mở bán'],
                                $combo->trangThai === 'ARCHIVED' => ['secondary', 'Đã lưu trữ'],
                                default => ['secondary', 'Bản nháp'],
                            };

                            $destroyParams = array_merge(['combo' => $combo->maGoi], request()->query());
                        @endphp
                        <tr>
                            <td>
                                <div class="combo-info">
                                    <div class="combo-thumb">
                                        <img src="{{ $combo->cover_image_url }}" alt="" loading="lazy">
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <strong>{{ $combo->tenGoi }}</strong>
                                            <span class="badge bg-{{ $availabilityBadge[0] }} rounded-pill">
                                                {{ $availabilityBadge[1] }}
                                            </span>
                                        </div>
                                        <div class="text-muted small">
                                            Mã combo: #{{ $combo->maGoi }} &middot;
                                            Cập nhật: {{ optional($combo->updated_at)->format('d/m/Y H:i') }}
                                        </div>
                                        @if ($activePromotion)
                                            <div class="combo-promo-tag">
                                                <i class="bi bi-gift me-1"></i>
                                                {{ $activePromotion->tenKM }}
                                                <span class="text-muted">
                                                    ({{ optional($activePromotion->ngayBatDau)->format('d/m') }}
                                                    - {{ optional($activePromotion->ngayKetThuc)->format('d/m') }})
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <ul class="combo-course-list">
                                    @foreach ($combo->courses as $courseItem)
                                        <li>
                                            <span class="order">{{ $courseItem->pivot->thuTu ?? $loop->iteration }}</span>
                                            <span class="name">{{ $courseItem->tenKH }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-end">
                                <div class="price-stack">
                                    <div class="price-sale">{{ $formatCurrency($combo->sale_price) }}</div>
                                    <div class="price-origin">{{ $formatCurrency($combo->original_price) }}</div>
                                    @if ($combo->saving_amount > 0)
                                        <span class="price-saving">
                                            Tiết kiệm {{ $formatCurrency($combo->saving_amount) }}
                                            ({{ $combo->saving_percent }}%)
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="text-muted small">
                                    <div>
                                        <i class="bi bi-calendar-check me-1"></i>
                                        {{ optional($combo->ngayBatDau)->format('d/m/Y') ?? 'Không giới hạn' }}
                                    </div>
                                    <div>
                                        <i class="bi bi-calendar-x me-1"></i>
                                        {{ optional($combo->ngayKetThuc)->format('d/m/Y') ?? 'Không giới hạn' }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $combo->trangThai }}</span>
                                <div class="text-muted small">
                                    Tạo bởi: {{ $combo->creator->hoTen ?? $combo->creator->name ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button
                                        type="button"
                                        class="btn btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#comboEditModal"
                                        data-combo='@json($comboPayload, JSON_UNESCAPED_UNICODE)'
                                    >
                                        <i class="bi bi-pencil-square me-1"></i> Sửa
                                    </button>
                                    <form
                                        method="post"
                                        action="{{ route('admin.combos.destroy', $destroyParams) }}"
                                        onsubmit="return confirm('Bạn chắc chắn muốn xoá combo này?');"
                                    >
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="bi bi-trash me-1"></i> Xoá
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end">
            {{ $combos->links() }}
        </div>
    @endif

    {{-- Create combo modal --}}
    <div class="modal fade" id="comboCreateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <form
                    class="combo-form"
                    method="post"
                    enctype="multipart/form-data"
                    data-combo-form="create"
                    action="{{ route('admin.combos.store', request()->query()) }}"
                >
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-layers me-2 text-primary"></i> Tạo combo mới
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-lg-5">
                                <div class="mb-3">
                                    <label class="form-label">Tên combo <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        name="tenGoi"
                                        class="form-control"
                                        value="{{ old('tenGoi') }}"
                                        maxlength="150"
                                        required
                                    >
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Mô tả</label>
                                    <textarea
                                        name="moTa"
                                        class="form-control"
                                        rows="4"
                                        maxlength="2000"
                                    >{{ old('moTa') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ảnh đại diện</label>
                                    <input type="file" name="hinhanh" class="form-control" accept="image/*">
                                    <div class="form-text">Tỉ lệ đề xuất 4:3, kích thước tối đa 3MB.</div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Giá bán combo <span class="text-danger">*</span></label>
                                        <input
                                            type="number"
                                            name="gia"
                                            class="form-control"
                                            min="0"
                                            step="1000"
                                            value="{{ old('gia', 0) }}"
                                            data-combo-price-input
                                            required
                                        >
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Trạng thái</label>
                                        <select name="trangThai" class="form-select">
                                            <option value="PUBLISHED" {{ old('trangThai') === 'PUBLISHED' ? 'selected' : '' }}>Đang mở bán</option>
                                            <option value="DRAFT" {{ old('trangThai') === 'DRAFT' ? 'selected' : '' }}>Bản nháp</option>
                                            <option value="ARCHIVED" {{ old('trangThai') === 'ARCHIVED' ? 'selected' : '' }}>Lưu trữ</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-3 mt-0">
                                    <div class="col-md-6">
                                        <label class="form-label">Ngày bắt đầu</label>
                                        <input
                                            type="date"
                                            name="ngayBatDau"
                                            class="form-control"
                                            value="{{ old('ngayBatDau') }}"
                                        >
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Ngày kết thúc</label>
                                        <input
                                            type="date"
                                            name="ngayKetThuc"
                                            class="form-control"
                                            value="{{ old('ngayKetThuc') }}"
                                        >
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label">Khuyến mãi áp dụng</label>
                                    <select name="promotion_id" class="form-select" data-promotion-select>
                                        <option value="">-- Không áp dụng --</option>
                                        @foreach ($promotions as $promotion)
                                            <option
                                                value="{{ $promotion->maKM }}"
                                                data-type="{{ $promotion->loaiUuDai }}"
                                                data-value="{{ (int) round($promotion->giaTriUuDai) }}"
                                                data-start="{{ optional($promotion->ngayBatDau)->format('Y-m-d') }}"
                                                data-end="{{ optional($promotion->ngayKetThuc)->format('Y-m-d') }}"
                                            >
                                                {{ $promotion->tenKM }} ({{ $promotion->trangThai }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mt-2 collapse" data-promotion-price-wrapper>
                                    <label class="form-label">Giá sau khuyến mãi</label>
                                    <input
                                        type="number"
                                        name="promotion_price"
                                        class="form-control"
                                        min="0"
                                        step="1000"
                                        value="{{ old('promotion_price') }}"
                                        data-promotion-price-input
                                    >
                                    <div class="form-text">
                                        Có thể để trống để hệ thống tự tính từ loại ưu đãi.
                                    </div>
                                </div>
                                <div class="combo-price-summary mt-4" data-combo-summary>
                                    <div>
                                        <span>Tổng học phí</span>
                                        <strong data-combo-original>0 VND</strong>
                                    </div>
                                    <div>
                                        <span>Giá combo</span>
                                        <strong data-combo-sale>0 VND</strong>
                                    </div>
                                    <div>
                                        <span>Tiết kiệm</span>
                                        <strong data-combo-saving>0 VND</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="combo-course-picker card shadow-sm">
                                    <div class="card-body">
                                        <h6 class="card-title d-flex justify-content-between align-items-center">
                                            <span>Khóa học trong combo <span class="text-danger">*</span></span>
                                            <span class="badge bg-primary" data-course-count>0 khóa học</span>
                                        </h6>
                                        <div class="row g-2 align-items-center mb-3">
                                            <div class="col-md-8">
                                                <select class="form-select" data-course-picker>
                                                    <option value="">-- Chọn khóa học --</option>
                                                    @foreach ($courses as $course)
                                                        <option
                                                            value="{{ $course->maKH }}"
                                                            data-price="{{ (int) $course->hocPhi }}"
                                                        >
                                                            {{ $course->tenKH }} ({{ $formatCurrency($course->hocPhi) }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 d-grid">
                                                <button type="button" class="btn btn-outline-primary" data-course-add>
                                                    <i class="bi bi-plus-circle me-1"></i> Thêm khóa học
                                                </button>
                                            </div>
                                        </div>
                                        <div class="combo-course-empty" data-course-empty>
                                            <i class="bi bi-collection me-2"></i> Chưa có khóa học nào trong combo.
                                        </div>
                                        <ul class="combo-course-selected" data-course-list></ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Lưu combo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit combo modal --}}
    <div class="modal fade" id="comboEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <form
                    class="combo-form"
                    method="post"
                    enctype="multipart/form-data"
                    data-combo-form="edit"
                >
                    @csrf
                    @method('put')
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-pencil-square me-2 text-primary"></i> Chỉnh sửa combo
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-lg-5">
                                <input type="hidden" name="combo_id" data-combo-id>
                                <div class="mb-3">
                                    <label class="form-label">Tên combo <span class="text-danger">*</span></label>
                                    <input type="text" name="tenGoi" class="form-control" maxlength="150" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Mô tả</label>
                                    <textarea name="moTa" class="form-control" rows="4" maxlength="2000"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ảnh đại diện</label>
                                    <input type="file" name="hinhanh" class="form-control" accept="image/*">
                                    <div class="form-text">Tải ảnh mới để thay thế ảnh hiện tại.</div>
                                    <div class="mt-2 d-none" data-current-image>
                                        <img src="" alt="Preview" class="img-thumbnail" width="160">
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Giá bán combo <span class="text-danger">*</span></label>
                                        <input
                                            type="number"
                                            name="gia"
                                            class="form-control"
                                            min="0"
                                            step="1000"
                                            data-combo-price-input
                                            required
                                        >
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Trạng thái</label>
                                        <select name="trangThai" class="form-select">
                                            <option value="PUBLISHED">Đang mở bán</option>
                                            <option value="DRAFT">Bản nháp</option>
                                            <option value="ARCHIVED">Lưu trữ</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-3 mt-0">
                                    <div class="col-md-6">
                                        <label class="form-label">Ngày bắt đầu</label>
                                        <input type="date" name="ngayBatDau" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Ngày kết thúc</label>
                                        <input type="date" name="ngayKetThuc" class="form-control">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label">Khuyến mãi áp dụng</label>
                                    <select name="promotion_id" class="form-select" data-promotion-select>
                                        <option value="">-- Không áp dụng --</option>
                                        @foreach ($promotions as $promotion)
                                            <option
                                                value="{{ $promotion->maKM }}"
                                                data-type="{{ $promotion->loaiUuDai }}"
                                                data-value="{{ (int) round($promotion->giaTriUuDai) }}"
                                                data-start="{{ optional($promotion->ngayBatDau)->format('Y-m-d') }}"
                                                data-end="{{ optional($promotion->ngayKetThuc)->format('Y-m-d') }}"
                                            >
                                                {{ $promotion->tenKM }} ({{ $promotion->trangThai }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mt-2 collapse" data-promotion-price-wrapper>
                                    <label class="form-label">Giá sau khuyến mãi</label>
                                    <input
                                        type="number"
                                        name="promotion_price"
                                        class="form-control"
                                        min="0"
                                        step="1000"
                                        data-promotion-price-input
                                    >
                                    <div class="form-text">
                                        Có thể để trống để hệ thống tự tính từ loại ưu đãi.
                                    </div>
                                </div>
                                <div class="combo-price-summary mt-4" data-combo-summary>
                                    <div>
                                        <span>Tổng học phí</span>
                                        <strong data-combo-original>0 VND</strong>
                                    </div>
                                    <div>
                                        <span>Giá combo</span>
                                        <strong data-combo-sale>0 VND</strong>
                                    </div>
                                    <div>
                                        <span>Tiết kiệm</span>
                                        <strong data-combo-saving>0 VND</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="combo-course-picker card shadow-sm">
                                    <div class="card-body">
                                        <h6 class="card-title d-flex justify-content-between align-items-center">
                                            <span>Khóa học trong combo <span class="text-danger">*</span></span>
                                            <span class="badge bg-primary" data-course-count>0 khóa học</span>
                                        </h6>
                                        <div class="row g-2 align-items-center mb-3">
                                            <div class="col-md-8">
                                                <select class="form-select" data-course-picker>
                                                    <option value="">-- Chọn khóa học --</option>
                                                    @foreach ($courses as $course)
                                                        <option
                                                            value="{{ $course->maKH }}"
                                                            data-price="{{ (int) $course->hocPhi }}"
                                                        >
                                                            {{ $course->tenKH }} ({{ $formatCurrency($course->hocPhi) }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 d-grid">
                                                <button type="button" class="btn btn-outline-primary" data-course-add>
                                                    <i class="bi bi-plus-circle me-1"></i> Thêm khóa học
                                                </button>
                                            </div>
                                        </div>
                                        <div class="combo-course-empty" data-course-empty>
                                            <i class="bi bi-collection me-2"></i> Chưa có khóa học nào trong combo.
                                        </div>
                                        <ul class="combo-course-selected" data-course-list></ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@php
    $dataset = [
        'courses' => $courses->map(fn ($course) => [
            'id' => $course->maKH,
            'name' => $course->tenKH,
            'price' => (int) $course->hocPhi,
            'slug' => $course->slug,
        ])->values(),
        'promotions' => $promotions->map(fn ($promotion) => [
            'id' => $promotion->maKM,
            'name' => $promotion->tenKM,
            'type' => $promotion->loaiUuDai,
            'value' => (int) round($promotion->giaTriUuDai),
            'start' => optional($promotion->ngayBatDau)->format('Y-m-d'),
            'end' => optional($promotion->ngayKetThuc)->format('Y-m-d'),
            'status' => $promotion->trangThai,
        ])->values(),
        'updateUrlTemplate' => route('admin.combos.update', array_merge(['combo' => '__ID__'], request()->query())),
    ];
@endphp

@push('scripts')
    <script id="combo-form-dataset" type="application/json">
        {!! json_encode($dataset, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
    <script src="{{ asset('js/Admin/combos.js') }}" defer></script>
@endpush
