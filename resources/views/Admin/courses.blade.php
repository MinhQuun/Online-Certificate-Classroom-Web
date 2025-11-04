@extends('layouts.admin')
@section('title', 'Quản lý khóa học')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Admin/admin-courses.css') }}">
@endpush

@section('content')
    <section class="page-header">
        <span class="kicker">Admin</span>
        <h1 class="title">Quản lý khóa học</h1>
        <p class="muted">Thêm, chỉnh sửa, xóa và quản lý toàn bộ khóa học trên hệ thống.</p>
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

    <div class="card filter-card courses-filter mb-3">
        <div class="card-body">
            <form class="row g-2 align-items-center" method="get" action="{{ route('admin.courses.index') }}">
                <div class="col-lg-4">
                    <label for="filter-q" class="visually-hidden">Từ khóa</label>
                    <input
                        id="filter-q"
                        class="form-control"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Tìm theo tên khóa học, mã KH..."
                    >
                </div>
                <div class="col-lg-3">
                    <label for="filter-category" class="visually-hidden">Danh mục</label>
                    <select name="category_id" id="filter-category" class="form-select">
                        <option value="">— Tất cả danh mục —</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->maDanhMuc }}" {{ request('category_id') == $cat->maDanhMuc ? 'selected' : '' }}>
                                {{ $cat->tenDanhMuc }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3">
                    <label for="filter-status" class="visually-hidden">Trạng thái</label>
                    <select name="status" id="filter-status" class="form-select">
                        <option value="">— Tất cả trạng thái —</option>
                        <option value="PUBLISHED" {{ request('status') == 'PUBLISHED' ? 'selected' : '' }}>Đã công bố</option>
                        <option value="DRAFT" {{ request('status') == 'DRAFT' ? 'selected' : '' }}>Bản nháp</option>
                    </select>
                </div>
                <div class="col-lg-2 d-flex gap-2 justify-content-lg-end">
                    <button class="btn btn-outline-primary">Lọc</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.courses.index') }}">Xóa lọc</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="m-0">Danh sách khóa học</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
                <i class="bi bi-plus-circle me-1"></i> Thêm mới
            </button>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover courses-table table-fixed">
                <colgroup>
                    <col style="width: 90px;">
                    <col style="width: 24%;">
                    <col style="width: 16%;">
                    <col style="width: 16%;">
                    <col style="width: 13%;">
                    <col style="width: 15%;">
                    <col style="width: 10%;">
                    <col style="width: 12%;">
                </colgroup>
                <thead>
                    <tr>
                        <th>Mã KH</th>
                        <th>Khóa học</th>
                        <th>Danh mục</th>
                        <th>Giảng viên</th>
                        <th class="text-end">Học phí</th>
                        <th>Khuyến mãi</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($courses as $course)
                        @php
                            $statusClass = $course->trangThai === 'PUBLISHED' ? 'text-bg-success' : 'text-bg-warning';
                            $statusText = $course->trangThai === 'PUBLISHED' ? 'Đã công bố' : 'Bản nháp';
                            $activePromotion = $course->active_promotion;
                            $promotionPrice = $activePromotion && $activePromotion->pivot
                                ? (int) $activePromotion->pivot->giaUuDai
                                : null;
                        @endphp
                        <tr>
                            <td><strong>{{ $course->maKH }}</strong></td>
                            <td class="course-cell">
                                <div class="course-name" title="{{ $course->tenKH }}">{{ $course->tenKH }}</div>
                                <div class="course-slug"><code>{{ $course->slug }}</code></div>
                            </td>
                            <td class="text-truncate" title="{{ optional($course->category)->tenDanhMuc }}">
                                {{ optional($course->category)->tenDanhMuc ?? '—' }}
                            </td>
                            <td class="text-truncate" title="{{ optional($course->teacher)->hoTen }}">
                                {{ optional($course->teacher)->hoTen ?? '—' }}
                            </td>
                            <td class="text-end fw-semibold text-primary">
                                {{ number_format($course->hocPhi, 0, ',', '.') }} đ
                            </td>
                            <td class="course-promo-cell">
                                @if ($activePromotion)
                                    <div class="course-promo-name">{{ $activePromotion->tenKM }}</div>
                                    <div class="course-promo-meta">
                                        @if ($activePromotion->loaiUuDai === \App\Models\Promotion::TYPE_PERCENT)
                                            Giảm {{ (int) $activePromotion->giaTriUuDai }}%
                                        @elseif ($activePromotion->loaiUuDai === \App\Models\Promotion::TYPE_FIXED)
                                            Giảm {{ number_format($activePromotion->giaTriUuDai, 0, ',', '.') }} đ
                                        @else
                                            Quà tặng / Khác
                                        @endif
                                        @if ($promotionPrice)
                                            · Giá ưu đãi: {{ number_format($promotionPrice, 0, ',', '.') }} đ
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted small">Chưa áp dụng</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                            <td class="text-end">
                                <button
                                    type="button"
                                    class="btn btn-primary-soft btn-sm action-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEdit"
                                    data-id="{{ $course->maKH }}"
                                    data-action="{{ route('admin.courses.update', $course->maKH) }}"
                                    data-name="{{ $course->tenKH }}"
                                    data-slug="{{ $course->slug }}"
                                    data-category="{{ $course->maDanhMuc }}"
                                    data-teacher="{{ $course->maND }}"
                                    data-fee="{{ $course->hocPhi }}"
                                    data-duration="{{ $course->thoiHanNgay }}"
                                    data-start="{{ optional($course->ngayBatDau)->format('Y-m-d') }}"
                                    data-end="{{ optional($course->ngayKetThuc)->format('Y-m-d') }}"
                                    data-desc="{{ $course->moTa }}"
                                    data-status="{{ $course->trangThai }}"
                                    data-promotion-id="{{ $activePromotion?->maKM }}"
                                    data-promotion-price="{{ $promotionPrice }}"
                                    data-image="{{ $course->hinhanh }}"
                                    data-image-url="{{ $course->cover_image_url }}"
                                >
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form
                                    action="{{ route('admin.courses.destroy', $course->maKH) }}"
                                    method="post"
                                    class="d-inline form-delete"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger-soft action-btn">
                                        <i class="bi bi-trash me-1"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Không có dữ liệu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('components.pagination', [
            'paginator' => $courses,
            'ariaLabel' => 'Điều hướng trang khóa học',
        ])
    </div>

    <div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <form
                class="modal-content"
                action="{{ route('admin.courses.store') }}"
                method="post"
                enctype="multipart/form-data"
                data-slug-form
            >
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Thêm khóa học mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label for="c_name" class="form-label">Tên khóa học <span class="text-danger">*</span></label>
                        <input
                            id="c_name"
                            name="tenKH"
                            class="form-control"
                            value="{{ old('tenKH') }}"
                            required
                            data-slug-source
                        >
                    </div>
                    <div class="col-md-6">
                        <label for="c_slug" class="form-label">Slug</label>
                        <input
                            id="c_slug"
                            type="text"
                            name="slug"
                            class="form-control"
                            value="{{ old('slug') }}"
                            placeholder="vi-du-khoa-hoc-online"
                            data-slug-target
                            autocomplete="off"
                        >
                    </div>
                    <div class="col-md-6">
                        <label for="c_category" class="form-label">Danh mục <span class="text-danger">*</span></label>
                        <select id="c_category" name="maDanhMuc" class="form-select" required>
                            <option value="">Chọn danh mục</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->maDanhMuc }}" {{ old('maDanhMuc') == $cat->maDanhMuc ? 'selected' : '' }}>
                                    {{ $cat->tenDanhMuc }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="c_teacher" class="form-label">Giảng viên <span class="text-danger">*</span></label>
                        <select id="c_teacher" name="maND" class="form-select" required>
                            <option value="">Chọn giảng viên</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->maND }}" {{ old('maND') == $teacher->maND ? 'selected' : '' }}>
                                    {{ $teacher->hoTen }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="c_fee" class="form-label">Học phí <span class="text-danger">*</span></label>
                        <input
                            id="c_fee"
                            type="text"
                            name="hocPhi"
                            class="form-control"
                            value="{{ old('hocPhi') }}"
                            required
                            inputmode="numeric"
                        >
                        <div class="form-text">Nhập số, hệ thống sẽ tự định dạng theo VND.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="c_duration" class="form-label">Thời hạn (ngày) <span class="text-danger">*</span></label>
                        <input
                            id="c_duration"
                            type="number"
                            name="thoiHanNgay"
                            class="form-control"
                            min="1"
                            value="{{ old('thoiHanNgay', 30) }}"
                            required
                        >
                    </div>
                    <div class="col-md-6">
                        <label for="c_start" class="form-label">Ngày bắt đầu</label>
                        <input
                            id="c_start"
                            type="date"
                            name="ngayBatDau"
                            class="form-control"
                            value="{{ old('ngayBatDau') }}"
                        >
                    </div>
                    <div class="col-md-6">
                        <label for="c_end" class="form-label">Ngày kết thúc</label>
                        <input
                            id="c_end"
                            type="date"
                            name="ngayKetThuc"
                            class="form-control"
                            value="{{ old('ngayKetThuc') }}"
                        >
                    </div>
                    <div class="col-md-6">
                        <label for="c_promotion" class="form-label">Khuyến mãi</label>
                        <select
                            id="c_promotion"
                            name="promotion_id"
                            class="form-select"
                            data-promotion-select
                        >
                            <option value="">Chưa áp dụng</option>
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
                            <div class="mt-2 collapse" data-promotion-price-wrapper>
                                <label class="form-label" for="c_promotion_price">Giá ưu đãi</label>
                                <input
                                    type="number"
                                    name="promotion_price"
                                id="c_promotion_price"
                                class="form-control"
                                min="0"
                                    placeholder="Giá sau ưu đãi"
                                    data-promotion-price-input
                                >
                            <div class="form-text" data-promotion-help>Để trống để hệ thống tính theo quy tắc khuyến mãi.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="c_image" class="form-label">Ảnh khóa học</label>
                        <input
                            id="c_image"
                            type="file"
                            name="hinhanh"
                            class="form-control"
                            accept="image/*"
                        >
                    </div>
                    <div class="col-12">
                        <label for="c_desc" class="form-label">Mô tả</label>
                        <textarea
                            id="c_desc"
                            name="moTa"
                            class="form-control"
                            rows="4"
                            maxlength="2000"
                        >{{ old('moTa') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu khóa học</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <form
                id="formEdit"
                class="modal-content"
                method="post"
                enctype="multipart/form-data"
            >
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Chỉnh sửa khóa học</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label for="e_name" class="form-label">Tên khóa học <span class="text-danger">*</span></label>
                        <input id="e_name" name="tenKH" class="form-control" required data-slug-source>
                    </div>
                    <div class="col-md-6">
                        <label for="e_slug" class="form-label">Slug</label>
                        <input
                            id="e_slug"
                            type="text"
                            name="slug"
                            class="form-control"
                            data-slug-target
                            autocomplete="off"
                        >
                    </div>
                    <div class="col-md-6">
                        <label for="e_category" class="form-label">Danh mục <span class="text-danger">*</span></label>
                        <select id="e_category" name="maDanhMuc" class="form-select" required>
                            <option value="">Chọn danh mục</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->maDanhMuc }}">{{ $cat->tenDanhMuc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="e_teacher" class="form-label">Giảng viên <span class="text-danger">*</span></label>
                        <select id="e_teacher" name="maND" class="form-select" required>
                            <option value="">Chọn giảng viên</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->maND }}">{{ $teacher->hoTen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="e_fee" class="form-label">Học phí <span class="text-danger">*</span></label>
                        <input
                            id="e_fee"
                            type="text"
                            name="hocPhi"
                            class="form-control"
                            required
                            inputmode="numeric"
                        >
                    </div>
                    <div class="col-md-6">
                        <label for="e_duration" class="form-label">Thời hạn (ngày) <span class="text-danger">*</span></label>
                        <input
                            id="e_duration"
                            type="number"
                            name="thoiHanNgay"
                            class="form-control"
                            min="1"
                            required
                        >
                    </div>
                    <div class="col-md-6">
                        <label for="e_start" class="form-label">Ngày bắt đầu</label>
                        <input id="e_start" type="date" name="ngayBatDau" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="e_end" class="form-label">Ngày kết thúc</label>
                        <input id="e_end" type="date" name="ngayKetThuc" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="e_status" class="form-label">Trạng thái</label>
                        <select id="e_status" name="trangThai" class="form-select">
                            <option value="PUBLISHED">Đã công bố</option>
                            <option value="DRAFT">Bản nháp</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="e_promotion" class="form-label">Khuyến mãi</label>
                        <select
                            id="e_promotion"
                            name="promotion_id"
                            class="form-select"
                            data-promotion-select
                        >
                            <option value="">Chưa áp dụng</option>
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
                        <div class="mt-2 collapse" data-promotion-price-wrapper>
                            <label class="form-label" for="e_promotion_price">Giá ưu đãi</label>
                            <input
                                type="number"
                                name="promotion_price"
                                id="e_promotion_price"
                                class="form-control"
                                min="0"
                                placeholder="Giá sau ưu đãi"
                                data-promotion-price-input
                            >
                            <div class="form-text" data-promotion-help>Để trống để hệ thống tính theo quy tắc khuyến mãi.</div>
                        </div>
                    </div>
                    <div class="col-12" data-current-image-wrapper>
                        <label class="form-label d-block">Hình ảnh hiện tại</label>
                        <div class="d-flex align-items-center gap-3 flex-wrap" data-current-image-container>
                            <img
                                src=""
                                alt="Hình ảnh khóa học"
                                class="img-thumbnail mb-2 d-none"
                                style="max-height: 160px;"
                                data-current-image
                            >
                            <span class="text-muted" data-current-image-empty>Khóa học chưa có hình.</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="e_image" class="form-label">Cập nhật hình ảnh</label>
                        <input
                            id="e_image"
                            type="file"
                            name="hinhanh"
                            class="form-control"
                            accept="image/*"
                        >
                    </div>
                    <div class="col-12">
                        <label for="e_desc" class="form-label">Mô tả</label>
                        <textarea
                            id="e_desc"
                            name="moTa"
                            class="form-control"
                            rows="4"
                            maxlength="2000"
                        ></textarea>
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

@push('scripts')
    <script id="course-promotion-dataset" type="application/json">
        {!! json_encode([
            'promotions' => $promotions->map(function ($promotion) {
                return [
                    'id' => $promotion->maKM,
                    'name' => $promotion->tenKM,
                    'type' => $promotion->loaiUuDai,
                    'value' => (int) round($promotion->giaTriUuDai),
                    'start' => optional($promotion->ngayBatDau)->format('Y-m-d'),
                    'end' => optional($promotion->ngayKetThuc)->format('Y-m-d'),
                    'target' => $promotion->apDungCho,
                    'status' => $promotion->trangThai,
                ];
            })->values(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
    <script src="{{ asset('js/Admin/slug-helper.js') }}" defer></script>
    <script src="{{ asset('js/Admin/admin-courses.js') }}" defer></script>
@endpush
