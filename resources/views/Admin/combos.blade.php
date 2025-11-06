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



    <section class="stats-grid combos-stats mb-4">
        <article class="stats-card">
            <span class="stats-label">Tổng combo</span>
            <span class="stats-value">{{ number_format($stats['total'] ?? 0) }}</span>
            <span class="stats-meta">Toàn bộ gói đã tạo</span>
        </article>
        <article class="stats-card">
            <span class="stats-label">Đang mở bán</span>
            <span class="stats-value">{{ number_format($stats['published'] ?? 0) }}</span>
            <span class="stats-meta">Hiển thị cho học viên</span>
        </article>
        <article class="stats-card">
            <span class="stats-label">Bản nháp</span>
            <span class="stats-value">{{ number_format($stats['draft'] ?? 0) }}</span>
            <span class="stats-meta">Chưa công khai</span>
        </article>
        <article class="stats-card">
            <span class="stats-label">Lưu trữ</span>
            <span class="stats-value">{{ number_format($stats['archived'] ?? 0) }}</span>
            <span class="stats-meta">Không còn mở bán</span>
        </article>
    </section>
    <div class="card filter-card combos-filter mb-4">
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
                <div class="col-lg-2 d-flex gap-2 justify-content-lg-end">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1">
                        Lọc
                    </button>
                    <a href="{{ route('admin.combos.index') }}" class="btn btn-outline-secondary">
                        Xóa lọc
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card combos-card">
        <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h5 class="m-0">Danh sách combo</h5>
                <p class="muted small mb-0">
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
        <div class="card-body">
            <div class="empty-state combos-empty">
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
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover combos-table table-fixed">
                <colgroup>
                    <col style="width:26%;">
                    <col style="width:26%;">
                    <col style="width:16%;">
                    <col style="width:12%;">
                    <col style="width:10%;">
                    <col style="width:10%;">
                </colgroup>
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
                                'slug' => $combo->slug,
                                'description' => $combo->moTa,
                                'price' => (int) round($combo->gia),
                                'start_date' => optional($combo->ngayBatDau)->format('Y-m-d'),
                                'end_date' => optional($combo->ngayKetThuc)->format('Y-m-d'),
                                'status' => $combo->trangThai,
                                'image' => $combo->cover_image_url,
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
                                    <div>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <strong>{{ $combo->tenGoi }}</strong>
                                            <span class="badge bg-{{ $availabilityBadge[0] }} rounded-pill">
                                                {{ $availabilityBadge[1] }}
                                            </span>
                                        </div>
                                        <div class="text-muted small">
                                            Cập nhật: {{ optional($combo->updated_at)->format('d/m/Y H:i') }}
                                        </div>
                                        <div class="combo-slug">
                                            <code>{{ $combo->slug }}</code>
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
                            <td class="text-end td-actions">
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
                                @php
                                    $statusMap = [
                                        'PUBLISHED' => ['class' => 'text-bg-success', 'label' => 'Đã công bố'],
                                        'DRAFT' => ['class' => 'text-bg-warning', 'label' => 'Bản nháp'],
                                        'ARCHIVED' => ['class' => 'text-bg-secondary', 'label' => 'Đã lưu trữ'],
                                    ];
                                    $statusMeta = $statusMap[$combo->trangThai] ?? ['class' => 'text-bg-secondary', 'label' => $combo->trangThai];
                                @endphp
                                <span class="badge rounded-pill {{ $statusMeta['class'] }}">
                                    {{ $statusMeta['label'] }}
                                </span>
                            </td>
                            <td class="text-end">
                                    <button
                                        type="button"
                                        class="btn btn-primary-soft action-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#comboEditModal"
                                        data-combo='@json($comboPayload, JSON_UNESCAPED_UNICODE)'
                                    >
                                        <i class="bi bi-pencil me-1"></i>
                                    </button>
                                    <form
                                        method="post"
                                        action="{{ route('admin.combos.destroy', $destroyParams) }}"
                                        onsubmit="return confirm('Bạn chắc chắn muốn xoá combo này?');"
                                    >
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-danger-soft action-btn">
                                            <i class="bi bi-trash me-1"></i>
                                        </button>
                                    </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-footer d-flex justify-content-end">
            {{ $combos->links() }}
        </div>
    @endif
    </div>

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
                data-slug-form
            >
                @csrf
                <input type="hidden" name="_action" value="save_close" data-action-field="combo-create">
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
                                        data-slug-source
                                    >
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Slug</label>
                                    <input
                                        type="text"
                                        name="slug"
                                        class="form-control"
                                        value="{{ old('slug') }}"
                                        placeholder="vi-du-combo-ielts-tu-a-den-z"
                                        data-slug-target
                                        autocomplete="off"
                                    >
                                    <div class="form-text">
                                        Để trống để hệ thống tự tạo slug từ tên combo.
                                    </div>
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
                                            readonly
                                        >
                                        <div class="form-text">Tự động bằng tổng học phí các khóa đã chọn.</div>
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
                                                data-target="{{ $promotion->apDungCho }}"
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
                                    <label class="form-label">Giá ưu đãi</label>
                                    <input
                                        type="number"
                                        name="promotion_price"
                                        class="form-control"
                                        min="0"
                                        step="1000"
                                        value="{{ old('promotion_price') }}"
                                        placeholder="Giá sau ưu đãi"
                                        data-promotion-price-input
                                        readonly
                                    >
                                    <div class="form-text" data-promotion-help>
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
                                                <button type="button" class="btn btn-primary" data-course-add>
                                                    Thêm khóa học
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary" data-form-action="save_close">
                                Lưu combo
                            </button>
                        </div>
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
                action=""
                data-slug-form
            >
                @csrf
                @method('put')
                <input type="hidden" name="_action" value="save_close" data-action-field="combo-edit">
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
                                    <input type="text" name="tenGoi" class="form-control" maxlength="150" required data-slug-source>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Slug</label>
                                    <input
                                        type="text"
                                        name="slug"
                                        class="form-control"
                                        placeholder="vi-du-combo-ielts"
                                        data-slug-target
                                        autocomplete="off"
                                    >
                                    <div class="form-text">Giữ nguyên nếu muốn bảo toàn đường dẫn hiện tại.</div>
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
                                            readonly
                                        >
                                        <div class="form-text">Tự động bằng tổng học phí các khóa đã chọn.</div>
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
                                                data-target="{{ $promotion->apDungCho }}"
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
                                    <label class="form-label">Giá ưu đãi</label>
                                    <input
                                        type="number"
                                        name="promotion_price"
                                        class="form-control"
                                        min="0"
                                        step="1000"
                                        placeholder="Giá sau ưu đãi"
                                        data-promotion-price-input
                                        readonly
                                    >
                                    <div class="form-text" data-promotion-help>
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
                                                <button type="button" class="btn btn-primary" data-course-add>
                                                    Thêm khóa học
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary" data-form-action="save_close">
                                Lưu combo
                            </button>
                        </div>
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
            'target' => $promotion->apDungCho,
        ])->values(),
        'updateUrlTemplate' => route('admin.combos.update', array_merge(['combo' => '__ID__'], request()->query())),
    ];
@endphp

@push('scripts')
    <script id="combo-form-dataset" type="application/json">
        {!! json_encode($dataset, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
    <script src="{{ asset('js/Admin/slug-helper.js') }}" defer></script>
    <script src="{{ asset('js/Admin/combos.js') }}" defer></script>
@endpush
