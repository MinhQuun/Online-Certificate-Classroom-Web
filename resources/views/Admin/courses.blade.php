@extends('layouts.admin')
@section('title', 'Quản lý khóa học')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Admin/admin-courses.css') }}">
@endpush

@section('content')
<div id="flash-data"
    data-success="{{ session('success') }}"
    data-error="{{ session('error') }}"
    data-info="{{ session('info') }}"
    data-warning="{{ session('warning') }}">
</div>

<!-- Header -->
<section class="page-header">
    <span class="kicker">Admin</span>
    <h1 class="title">Quản lý khóa học</h1>
    <p class="muted">Thêm, chỉnh sửa, xóa và quản lý các khóa học.</p>
</section>

<!-- Filter Card -->
<div class="card courses-filter mb-3">
    <div class="card-body">
        <form class="row g-2 align-items-center" method="get" action="{{ route('admin.courses.index') }}">
            <div class="col-lg-4">
                <input class="form-control" name="q" value="{{ request('q') }}" 
                       placeholder="Tìm theo tên khóa học, mã KH...">
            </div>
            <div class="col-lg-3">
                <select name="category_id" class="form-select">
                    <option value="">— Tất cả danh mục —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->maDanhMuc }}" {{ request('category_id') == $cat->maDanhMuc ? 'selected' : '' }}>
                            {{ $cat->tenDanhMuc }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3">
                <select name="status" class="form-select">
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

<!-- Courses Table -->
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
                <col style="width:80px;">
                <col style="width:22%;">
                <col style="width:18%;">
                <col style="width:16%;">
                <col style="width:12%;">
                <col style="width:10%;">
                <col style="width:12%;">
            </colgroup>
            <thead>
                <tr>
                    <th>Mã KH</th>
                    <th>Tên khóa học</th>
                    <th>Danh mục</th>
                    <th>Giảng viên</th>
                    <th class="text-end">Học phí</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courses as $course)
                    @php
                        $statusClass = $course->trangThai == 'PUBLISHED' ? 'text-bg-success' : 'text-bg-warning';
                        $statusText = $course->trangThai == 'PUBLISHED' ? 'Đã công bố' : 'Bản nháp';
                    @endphp
                    <tr>
                        <td><strong>{{ $course->maKH }}</strong></td>
                        <td class="text-truncate" title="{{ $course->tenKH }}">{{ $course->tenKH }}</td>
                        <td class="text-truncate" title="{{ $course->category->tenDanhMuc }}">{{ $course->category->tenDanhMuc }}</td>
                        <td class="text-truncate" title="{{ $course->teacher->hoTen }}">{{ $course->teacher->hoTen }}</td>
                        <td class="text-end fw-bold text-primary">{{ number_format($course->hocPhi) }}đ</td>
                        <td>
                            <span class="badge rounded-pill {{ $statusClass }}">{{ $statusText }}</span>
                        </td>
                        <td class="td-actions">
                            <button class="btn btn-sm btn-primary-soft action-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEdit"
                                    data-id="{{ $course->maKH }}"
                                    data-name="{{ $course->tenKH }}"
                                    data-category="{{ $course->maDanhMuc }}"
                                    data-teacher="{{ $course->maND }}"
                                    data-fee="{{ $course->hocPhi }}"
                                    data-duration="{{ $course->thoiHanNgay }}"
                                    data-start="{{ $course->ngayBatDau }}"
                                    data-end="{{ $course->ngayKetThuc }}"
                                    data-desc="{{ $course->moTa }}"
                                    data-status="{{ $course->trangThai }}"
                                    data-image="{{ $course->hinhAnh }}">
                                <i class="bi bi-pencil me-1"></i>
                            </button>
                            <form action="{{ route('admin.courses.destroy', $course->maKH) }}" method="post" class="d-inline form-delete">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger-soft action-btn">
                                    <i class="bi bi-trash me-1"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Không có dữ liệu.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if ($courses->lastPage() > 1)
        <nav aria-label="Điều hướng trang" class="mt-4">
            <ul class="pagination justify-content-center">
                @if ($courses->currentPage() > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ $courses->url($courses->currentPage() - 1) }}">Trước</a>
                    </li>
                @endif
                @for ($i = 1; $i <= $courses->lastPage(); $i++)
                    <li class="page-item {{ $i === $courses->currentPage() ? 'active' : '' }}">
                        <a class="page-link" href="{{ $courses->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor
                @if ($courses->currentPage() < $courses->lastPage())
                    <li class="page-item">
                        <a class="page-link" href="{{ $courses->url($courses->currentPage() + 1) }}">Sau</a>
                    </li>
                @endif
            </ul>
        </nav>
    @endif
</div>

{{-- Modal: Thêm khóa học --}}
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form class="modal-content" action="{{ route('admin.courses.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Thêm khóa học mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="col-12">
                    <label class="form-label">Tên khóa học <span class="text-danger">*</span></label>
                    <input name="tenKH" class="form-control" value="{{ old('tenKH') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                    <select name="maDanhMuc" class="form-select" required>
                        <option value="">Chọn danh mục</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->maDanhMuc }}" {{ old('maDanhMuc') == $category->maDanhMuc ? 'selected' : '' }}>
                                {{ $category->tenDanhMuc }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Giảng viên <span class="text-danger">*</span></label>
                    <select name="maND" class="form-select" required>
                        <option value="">Chọn giảng viên</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->maND }}" {{ old('maND') == $teacher->maND ? 'selected' : '' }}>
                                {{ $teacher->hoTen }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Học phí <span class="text-danger">*</span></label>
                    <input type="number" name="hocPhi" class="form-control" value="{{ old('hocPhi') }}" required min="0">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Thời hạn (ngày) <span class="text-danger">*</span></label>
                    <input type="number" name="thoiHanNgay" class="form-control" value="{{ old('thoiHanNgay') }}" required min="1">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ngày bắt đầu</label>
                    <input type="date" name="ngayBatDau" class="form-control" value="{{ old('ngayBatDau') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ngày kết thúc</label>
                    <input type="date" name="ngayKetThuc" class="form-control" value="{{ old('ngayKetThuc') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Mô tả</label>
                    <textarea name="moTa" class="form-control" rows="3">{{ old('moTa') }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Hình ảnh</label>
                    <input type="file" name="hinhanh" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button class="btn btn-primary">Thêm mới</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Sửa khóa học --}}
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form id="formEdit" class="modal-content" method="post" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Sửa khóa học</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="col-12">
                    <label class="form-label">Tên khóa học <span class="text-danger">*</span></label>
                    <input id="e_name" name="tenKH" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                    <select id="e_category" name="maDanhMuc" class="form-select" required>
                        <option value="">Chọn danh mục</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->maDanhMuc }}">{{ $category->tenDanhMuc }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Giảng viên <span class="text-danger">*</span></label>
                    <select id="e_teacher" name="maND" class="form-select" required>
                        <option value="">Chọn giảng viên</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->maND }}">{{ $teacher->hoTen }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Học phí <span class="text-danger">*</span></label>
                    <input id="e_fee" type="number" name="hocPhi" class="form-control" required min="0">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Thời hạn (ngày) <span class="text-danger">*</span></label>
                    <input id="e_duration" type="number" name="thoiHanNgay" class="form-control" required min="1">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ngày bắt đầu</label>
                    <input id="e_start" type="date" name="ngayBatDau" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ngày kết thúc</label>
                    <input id="e_end" type="date" name="ngayKetThuc" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Mô tả</label>
                    <textarea id="e_desc" name="moTa" class="form-control" rows="3"></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Hình ảnh mới (để trống nếu không đổi)</label>
                    <input type="file" name="hinhanh" class="form-control" accept="image/*">
                    <small class="text-muted">Hình hiện tại sẽ được giữ nguyên</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Trạng thái</label>
                    <select id="e_status" name="trangThai" class="form-select">
                        <option value="DRAFT">Bản nháp</option>
                        <option value="PUBLISHED">Đã công bố</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button class="btn btn-primary">Cập nhật</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/Admin/admin-courses.js') }}"></script>
@endpush