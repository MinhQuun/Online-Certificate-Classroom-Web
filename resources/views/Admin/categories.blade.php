@extends('layouts.admin')

@section('title', 'Quản lý danh mục')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Admin/categories.css') }}">
@endpush

@section('content')
    <div id="flash-data"
        data-success="{{ session('success') }}"
        data-error="{{ session('error') }}"
        data-info="{{ session('info') }}"
        data-warning="{{ session('warning') }}">
    </div>

    @if ($errors->any())
        <div class="alert alert-danger validation-errors" role="alert" id="validation-errors">
            <h6 class="mb-2">Vui lòng kiểm tra lại:</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="page-header">
        <span class="kicker">Quản trị</span>
        <h1 class="title">Quản lý danh mục</h1>
        <p class="muted">Tạo, chỉnh sửa và theo dõi danh mục khóa học.</p>
    </section>

    @php
        $totalCategories  = (int) ($summary['totalCategories'] ?? 0);
        $totalCourses     = (int) ($summary['totalCourses'] ?? 0);
        $publishedCourses = (int) ($summary['publishedCourses'] ?? 0);
    @endphp

    <section class="stats-grid mb-4">
        <article class="stats-card">
            <span class="stats-label">Tổng danh mục</span>
            <span class="stats-value">{{ number_format($totalCategories) }}</span>
        </article>
        <article class="stats-card">
            <span class="stats-label">Tất cả khóa học</span>
            <span class="stats-value">{{ number_format($totalCourses) }}</span>
        </article>
        <article class="stats-card">
            <span class="stats-label">Khóa học đã công bố</span>
            <span class="stats-value">{{ number_format($publishedCourses) }}</span>
        </article>
    </section>

    <div class="card categories-filter mb-3">
        <div class="card-body">
            <form class="row g-2 align-items-center" method="get" action="{{ route('admin.categories.index') }}">
                <div class="col-lg-6">
                    <input
                        class="form-control"
                        name="q"
                        value="{{ $q }}"
                        placeholder="Tìm theo tên danh mục hoặc slug">
                </div>
                <div class="col-lg-3 ms-auto d-flex gap-2 justify-content-lg-end">
                    <button class="btn btn-outline-primary">Lọc</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.categories.index') }}">Xóa lọc</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="m-0">Danh sách danh mục</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
                <i class="bi bi-plus-circle me-1"></i> Thêm mới
            </button>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover categories-table table-fixed">
                <colgroup>
                    <col style="width:90px;">
                    <col style="width:32%;">
                    <col style="width:20%;">
                    <col style="width:18%;">
                    <col style="width:18%;">
                    <col style="width:12%;">
                </colgroup>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên danh mục</th>
                    <th>Slug</th>
                    <th>Tổng khóa học</th>
                    <th>Khóa học đã công bố</th>
                    <th class="text-end">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse($categories as $category)
                    @php
                        $hasCourses = (int) $category->courses_count > 0;
                    @endphp
                    <tr>
                        <td>{{ $category->maDanhMuc }}</td>
                        <td class="category-cell">
                            <div class="category-name" title="{{ $category->tenDanhMuc }}">
                                <strong>{{ $category->tenDanhMuc }}</strong>
                            </div>
                            @if ($category->icon)
                                <div class="category-icon" title="{{ $category->icon }}">
                                    <i class="{{ $category->icon }}"></i>
                                    <span>{{ $category->icon }}</span>
                                </div>
                            @endif
                        </td>
                        <td class="text-truncate" title="{{ $category->slug }}">
                            <code>{{ $category->slug }}</code>
                        </td>
                        <td>
                            <span class="badge bg-soft-primary">
                                {{ number_format($category->courses_count) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-soft-success">
                                {{ number_format($category->published_courses_count) }}
                            </span>
                        </td>
                        <td class="text-end td-actions">
                            <button
                                class="btn btn-light btn-sm action-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEdit"
                                data-id="{{ $category->maDanhMuc }}"
                                data-name="{{ $category->tenDanhMuc }}"
                                data-slug="{{ $category->slug }}"
                                data-icon="{{ $category->icon }}"
                                data-action="{{ route('admin.categories.update', $category) }}"
                            >
                                <i class="bi bi-pencil-square"></i> Sửa
                            </button>
                            <form
                                action="{{ route('admin.categories.destroy', $category) }}"
                                method="post"
                                class="d-inline"
                                data-confirm="delete"
                                data-has-courses="{{ $hasCourses ? 'true' : 'false' }}"
                                data-course-count="{{ (int) $category->courses_count }}"
                            >
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn btn-danger-soft btn-sm action-btn">
                                    <i class="bi bi-trash"></i> Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            Chưa có danh mục nào. Bấm "Thêm mới" để bắt đầu.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($categories->hasPages())
            <div class="card-footer">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    {{-- Modal: Tạo danh mục --}}
    <div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form id="formCreate"
                    class="modal-content"
                    method="post"
                    action="{{ route('admin.categories.store') }}"
                    data-slug-form>
                @csrf
                <input type="hidden" name="_context" value="create">

                <div class="modal-header">
                    <h5 class="modal-title">Thêm danh mục</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Tên danh mục</label>
                        <input
                            type="text"
                            name="tenDanhMuc"
                            class="form-control"
                            value="{{ old('tenDanhMuc') }}"
                            placeholder="Ví dụ: Chứng chỉ IELTS"
                            required
                            autofocus
                            data-slug-source>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Slug</label>
                        <input
                            type="text"
                            name="slug"
                            class="form-control"
                            value="{{ old('slug') }}"
                            placeholder="vi-du-chung-chi-ielts"
                            data-slug-target
                            autocomplete="off">
                        <div class="form-text">
                            Để trống để tự động tạo slug từ tên danh mục.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Icon (tùy chọn)</label>
                        <input
                            type="text"
                            name="icon"
                            class="form-control"
                            value="{{ old('icon') }}"
                            placeholder="ví dụ: fa-solid fa-book-open">
                        <div class="form-text">
                            Hỗ trợ lớp icon Font Awesome hoặc tên icon tùy chọn.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button class="btn btn-primary" type="submit">Lưu danh mục</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Sửa danh mục --}}
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form id="formEdit"
                    class="modal-content"
                    method="post"
                    data-slug-form>
                @csrf
                @method('put')
                <input type="hidden" name="_context" value="edit">

                <div class="modal-header">
                    <h5 class="modal-title">Chỉnh sửa danh mục</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Tên danh mục</label>
                        <input id="e_name"
                                name="tenDanhMuc"
                                class="form-control"
                                required
                                data-slug-source>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Slug</label>
                        <input id="e_slug"
                                name="slug"
                                class="form-control"
                                placeholder="vi-du-van-ban"
                                data-slug-target
                                autocomplete="off">
                        <div class="form-text">
                            Điều chỉnh slug nếu cần giữ cấu trúc URL nhất quán.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Icon (tùy chọn)</label>
                        <input id="e_icon"
                                name="icon"
                                class="form-control"
                                placeholder="ví dụ: fa-solid fa-book-open">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button class="btn btn-primary" type="submit">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/Admin/categories.js') }}"></script>
@endpush