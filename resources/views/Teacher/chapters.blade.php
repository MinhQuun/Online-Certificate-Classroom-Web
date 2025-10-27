@extends('layouts.teacher')

@section('title', 'Quản lý chương')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Teacher/chapters.css') }}">
@endpush

@section('content')
    <!-- Header -->
    <section class="page-header">
        <span class="kicker">Giảng viên</span>
        <h1 class="title">Quản lý chương</h1>
        <p class="muted">Tổ chức và quản lý các chương trong khóa học của bạn.</p>
    </section>

    @if($courses->isEmpty())
        <div class="alert alert-info border-0 shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-inboxes fs-3 text-primary"></i>
                <div>
                    <h5 class="mb-1">Chưa có khóa học được phân công</h5>
                    <p class="mb-0">Khi quản trị viên gán bạn vào khóa học, bạn sẽ quản lý chương tại đây.</p>
                </div>
            </div>
        </div>
    @else
        <!-- Course Selector -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body d-flex flex-wrap align-items-center gap-3">
                <div class="flex-grow-1">
                    <label for="courseSelector" class="form-label text-muted text-uppercase small mb-1">Khóa học</label>
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <select id="courseSelector" class="form-select form-select-lg w-auto"
                                data-base-url="{{ route('teacher.chapters.index') }}">
                            @foreach($courses as $course)
                                <option value="{{ $course->maKH }}" @selected($activeCourse && $activeCourse->maKH === $course->maKH)>
                                    {{ $course->tenKH }}
                                </option>
                            @endforeach
                        </select>
                        <span class="badge bg-light text-dark border">
                            <i class="bi bi-people-fill me-1"></i> {{ number_format($activeCourse?->students_count ?? 0) }} học viên
                        </span>
                        <span class="badge bg-light text-dark border">
                            <i class="bi bi-list-nested me-1"></i> {{ number_format($activeCourse?->chapters->count() ?? 0) }} chương
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if($activeCourse)
            <!-- Chapters Accordion -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="m-0">Danh sách chương</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate"
                            data-course="{{ $activeCourse->maKH }}">
                        <i class="bi bi-plus-circle me-1"></i> Thêm mới
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover chapters-table table-fixed">
                        <colgroup>
                            <col style="width:80px;">
                            <col style="width:22%;">
                            <col style="width:18%;">
                            <col style="width:16%;">
                            <col style="width:12%;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Mã chương</th>
                                <th>Tên chương</th>
                                <th>Khóa học</th>
                                <th>Thứ tự</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeCourse->chapters as $chapter)
                                <tr>
                                    <td><strong>{{ $chapter->maChuong }}</strong></td>
                                    <td class="text-truncate" title="{{ $chapter->tenChuong }}">{{ $chapter->tenChuong }}</td>
                                    <td class="text-truncate" title="{{ $chapter->course->tenKH }}">{{ $chapter->course->tenKH }}</td>
                                    <td>{{ $chapter->thuTu }}</td>
                                    <td class="td-actions">
                                        <button class="btn btn-sm btn-primary-soft action-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEdit"
                                                data-id="{{ $chapter->maChuong }}"
                                                data-name="{{ $chapter->tenChuong }}"
                                                data-course="{{ $chapter->maKH }}"
                                                data-order="{{ $chapter->thuTu }}"
                                                data-desc="{{ $chapter->moTa }}">
                                            <i class="bi bi-pencil me-1"></i>
                                        </button>
                                        <form action="{{ route('teacher.chapters.destroy', $chapter->maChuong) }}" method="post" class="d-inline form-delete">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger-soft action-btn">
                                                <i class="bi bi-trash me-1"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Không có dữ liệu.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif

    {{-- Modal: Thêm chương --}}
    <div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <form class="modal-content" action="{{ route('teacher.chapters.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="course_id" id="createCourseId">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm chương mới</h5>
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

                    <div class="col-md-6">
                        <label class="form-label">Tên chương <span class="text-danger">*</span></label>
                        <input name="title" class="form-control" value="{{ old('title') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Thứ tự</label>
                        <input type="number" name="order" class="form-control" value="{{ old('order') }}" min="1">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button class="btn btn-primary">Thêm mới</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Sửa chương --}}
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <form id="formEdit" class="modal-content" method="post" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Sửa chương</h5>
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

                    <div class="col-md-6">
                        <label class="form-label">Tên chương <span class="text-danger">*</span></label>
                        <input id="e_name" name="title" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Thứ tự</label>
                        <input id="e_order" type="number" name="order" class="form-control" min="1">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Mô tả</label>
                        <textarea id="e_desc" name="description" class="form-control" rows="3"></textarea>
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
    <script src="{{ asset('js/Teacher/chapters.js') }}"></script>
@endpush
