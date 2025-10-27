@extends('layouts.teacher')

@section('title', 'Quản lý bài giảng')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Teacher/lectures.css') }}">
@endpush

@section('content')
    <!-- Header -->
    <section class="page-header">
        <span class="kicker">Giảng viên</span>
        <h1 class="title">Quản lý bài giảng</h1>
        <p class="muted">Tổ chức nội dung, tài liệu và bài tập cho học viên trong khóa học của bạn.</p>
    </section>

    @if($courses->isEmpty())
        <div class="alert alert-info border-0 shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-inboxes fs-3 text-primary"></i>
                <div>
                    <h5 class="mb-1">Chưa có khóa học được phân công</h5>
                    <p class="mb-0">Khi quản trị viên gán bạn vào khóa học, bạn sẽ quản lý nội dung tại đây.</p>
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
                                data-base-url="{{ route('teacher.lectures.index') }}">
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
            @php
                $totalLessons = $activeCourse->chapters->sum(fn ($chapter) => $chapter->lessons->count());
                $assignmentCount = $activeCourse->chapters->sum(fn ($chapter) => $chapter->lessons->where('loai', 'assignment')->count());
                $resourceCount = $activeCourse->chapters->sum(fn ($chapter) => $chapter->lessons->sum(fn ($lesson) => $lesson->materials->count()));
            @endphp

            <!-- Metrics -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="metric-pill">
                        <div class="icon"><i class="bi bi-collection-play"></i></div>
                        <div>
                            <div class="value">{{ $totalLessons }}</div>
                            <div class="label">Tổng bài giảng</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="metric-pill">
                        <div class="icon"><i class="bi bi-pencil-square"></i></div>
                        <div>
                            <div class="value">{{ $assignmentCount }}</div>
                            <div class="label">Bài tập</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="metric-pill">
                        <div class="icon"><i class="bi bi-paperclip"></i></div>
                        <div>
                            <div class="value">{{ $resourceCount }}</div>
                            <div class="label">Tài liệu đính kèm</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chapters Accordion -->
            <div class="accordion" id="chaptersAccordion">
                @foreach($activeCourse->chapters as $chapter)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="chapter-{{ $chapter->maChuong }}">
                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse-{{ $chapter->maChuong }}"
                                    aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                    aria-controls="collapse-{{ $chapter->maChuong }}">
                                <span class="me-2">{{ $chapter->thuTu }}.</span> {{ $chapter->tenChuong }}
                            </button>
                        </h2>
                        <div id="collapse-{{ $chapter->maChuong }}"
                             class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                             aria-labelledby="chapter-{{ $chapter->maChuong }}"
                             data-bs-parent="#chaptersAccordion">
                            <div class="accordion-body">
                                @if($chapter->lessons->isEmpty())
                                    <div class="alert alert-info mb-0">
                                        Chưa có bài giảng trong chương này.
                                        <button type="button" class="btn btn-primary btn-sm ms-2"
                                                data-bs-toggle="modal" data-bs-target="#createLessonModal"
                                                data-course="{{ $activeCourse->maKH }}"
                                                data-chapter="{{ $chapter->maChuong }}">
                                            <i class="bi bi-plus-circle me-1"></i> Thêm bài giảng
                                        </button>
                                    </div>
                                @else
                                    @foreach($chapter->lessons as $lesson)
                                        <div class="lesson-card mb-3" id="lesson-{{ $lesson->maBH }}">
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="lesson-icon">
                                                    @if(isset($lessonTypes[$lesson->loai]) && isset($lessonTypes[$lesson->loai]['icon']))
                                                        <i class="{{ $lessonTypes[$lesson->loai]['icon'] }}"></i>
                                                    @else
                                                        <i class="bi bi-question-circle text-warning"></i> <!-- Icon mặc định nếu không hợp lệ -->
                                                    @endif
                                                </span>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <h6 class="mb-1">{{ $lesson->thuTu }}. {{ $lesson->tieuDe }}</h6>
                                                            <p class="mb-0 text-muted small">{{ $lesson->moTa ?? 'Không có mô tả' }}</p>
                                                        </div>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <button type="button" class="btn btn-primary-soft btn-sm"
                                                                    data-bs-toggle="modal" data-bs-target="#editLessonModal"
                                                                    data-lesson="{{ json_encode([
                                                                        'id' => $lesson->maBH,
                                                                        'course' => $activeCourse->maKH,
                                                                        'chapter' => $chapter->maChuong,
                                                                        'title' => $lesson->tieuDe,
                                                                        'order' => $lesson->thuTu,
                                                                        'description' => $lesson->moTa,
                                                                        'type' => $lesson->loai
                                                                    ]) }}">
                                                                <i class="bi bi-pencil me-1"></i> Sửa
                                                            </button>
                                                            <form action="{{ route('teacher.lectures.destroy', $lesson->maBH) }}" method="POST">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="btn btn-danger-soft btn-sm">
                                                                    <i class="bi bi-trash me-1"></i> Xóa
                                                                </button>
                                                            </form>
                                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                                    data-bs-toggle="modal" data-bs-target="#createMaterialModal"
                                                                    data-lesson="{{ $lesson->maBH }}">
                                                                <i class="bi bi-paperclip me-1"></i> Thêm tài liệu
                                                            </button>
                                                        </div>
                                                    </div>
                                                    @if($lesson->materials->isNotEmpty())
                                                        <div class="mt-3">
                                                            <h6 class="small mb-2">Tài liệu đính kèm:</h6>
                                                            @foreach($lesson->materials as $material)
                                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                                    <i class="{{ $resourcePresets[$material->mime_type]['icon'] ?? 'bi bi-file-earmark' }} text-primary"></i>
                                                                    <a href="{{ $material->public_url }}" target="_blank" class="text-decoration-none">
                                                                        {{ $material->tenTL }} ({{ $material->kichThuoc ?? 'N/A' }})
                                                                    </a>
                                                                    <form action="{{ route('teacher.lectures.materials.destroy', $material->maTL) }}" method="POST">
                                                                        @csrf @method('DELETE')
                                                                        <button type="submit" class="btn btn-danger-soft btn-sm">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <button type="button" class="btn btn-primary btn-sm mt-2"
                                            data-bs-toggle="modal" data-bs-target="#createLessonModal"
                                            data-course="{{ $activeCourse->maKH }}"
                                            data-chapter="{{ $chapter->maChuong }}">
                                        <i class="bi bi-plus-circle me-1"></i> Thêm bài giảng
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    <!-- Modal: Thêm bài giảng -->
    <div class="modal fade" id="createLessonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('teacher.lectures.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Thêm bài giảng mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
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
                    <input type="hidden" name="course_id" id="createLessonCourse">
                    <div class="col-md-6">
                        <label class="form-label">Chọn chương <span class="text-danger">*</span></label>
                        <select class="form-select" name="chapter_id" id="createLessonChapter" required>
                            <option value="">Chọn chương</option>
                            @foreach($activeCourse?->chapters as $chapter)
                                <option value="{{ $chapter->maChuong }}">{{ $chapter->thuTu }}. {{ $chapter->tenChuong }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tên bài giảng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Thứ tự</label>
                        <input type="number" class="form-control" name="order" min="1">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Loại nội dung <span class="text-danger">*</span></label>
                        <select class="form-select" name="type" required>
                            @foreach($lessonTypes as $value => $meta)
                                <option value="{{ $value }}">{{ $meta['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm mới</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Sửa bài giảng -->
    <div class="modal fade" id="editLessonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <form id="editLessonForm" class="modal-content" method="POST">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Sửa bài giảng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
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
                    <input type="hidden" name="course_id" id="editLessonCourse">
                    <div class="col-md-6">
                        <label class="form-label">Chọn chương <span class="text-danger">*</span></label>
                        <select class="form-select" name="chapter_id" id="editLessonChapter" required>
                            <option value="">Chọn chương</option>
                            @foreach($activeCourse?->chapters as $chapter)
                                <option value="{{ $chapter->maChuong }}">{{ $chapter->thuTu }}. {{ $chapter->tenChuong }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tên bài giảng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" id="editLessonTitle" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Thứ tự</label>
                        <input type="number" class="form-control" name="order" id="editLessonOrder" min="1">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Loại nội dung <span class="text-danger">*</span></label>
                        <select class="form-select" name="type" id="editLessonType">
                            @foreach($lessonTypes as $value => $meta)
                                <option value="{{ $value }}">{{ $meta['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="description" id="editLessonDescription" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Thêm tài liệu -->
    <div class="modal fade" id="createMaterialModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <form class="modal-content" method="POST" id="createMaterialForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-paperclip me-2"></i>Thêm tài liệu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
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
                    <div class="col-md-8">
                        <label class="form-label">Tên hiển thị <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Loại tài liệu <span class="text-danger">*</span></label>
                        <select class="form-select" name="type" required>
                            @foreach($resourcePresets as $mime => $meta)
                                <option value="{{ $meta['default_type'] }}">{{ $meta['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Đường dẫn (hoặc tải file) <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" name="url" placeholder="https://...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tải file lên</label>
                        <input type="file" class="form-control" name="file" accept="video/mp4,application/pdf,application/zip,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,audio/mpeg">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Dung lượng hiển thị</label>
                        <input type="text" class="form-control" name="size" placeholder="350MB" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Chế độ</label>
                        <select class="form-select" name="visibility">
                            <option value="public">Công khai</option>
                            <option value="private">Riêng tư</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Mô tả ngắn</label>
                        <textarea class="form-control" name="summary" rows="2" placeholder="Nội dung chính, lưu ý khi học viên truy cập..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm tài liệu</button>
                </div>
            </form>
        </div>
    </div>

    <div id="teacherLecturesConfig" class="d-none"
         data-course-id="{{ $activeCourse?->maKH ?? '' }}"
         data-update-route="{{ route('teacher.lectures.update', ['lesson' => '__ID__']) }}"
         data-material-route="{{ route('teacher.lectures.materials.store', ['lesson' => '__ID__']) }}"
         data-presets="{{ base64_encode(json_encode($resourcePresets)) }}">
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/Teacher/lectures.js') }}"></script>
@endpush
