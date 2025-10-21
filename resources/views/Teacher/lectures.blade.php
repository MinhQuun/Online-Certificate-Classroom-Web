@extends('layouts.teacher')

@section('title', 'Quản lý bài giảng')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Teacher/lectures.css') }}">
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
                @forelse($activeCourse->chapters as $chapterIndex => $chapter)
                    <div class="accordion-item mb-3 border-0 shadow-sm">
                        <h2 class="accordion-header" id="heading-{{ $chapter->maChuong }}">
                            <button class="accordion-button {{ $chapterIndex === 0 ? '' : 'collapsed' }}" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#collapse-{{ $chapter->maChuong }}"
                                    aria-expanded="{{ $chapterIndex === 0 ? 'true' : 'false' }}"
                                    aria-controls="collapse-{{ $chapter->maChuong }}">
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">Chương {{ $chapter->thuTu }} • {{ $chapter->tenChuong }}</span>
                                    @if($chapter->moTa)
                                        <small class="text-muted">{{ $chapter->moTa }}</small>
                                    @endif
                                </div>
                            </button>
                        </h2>
                        <div id="collapse-{{ $chapter->maChuong }}" class="accordion-collapse collapse {{ $chapterIndex === 0 ? 'show' : '' }}"
                             aria-labelledby="heading-{{ $chapter->maChuong }}" data-bs-parent="#chaptersAccordion">
                            <div class="accordion-body">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                                    <h5 class="mb-0">Danh sách bài giảng</h5>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#createLessonModal"
                                            data-chapter="{{ $chapter->maChuong }}"
                                            data-course="{{ $activeCourse->maKH }}">
                                        <i class="bi bi-plus-circle"></i> Thêm bài giảng
                                    </button>
                                </div>

                                @forelse($chapter->lessons as $lesson)
                                    <div class="lesson-card mb-3" id="lesson-{{ $lesson->maBH }}">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="lesson-icon">
                                                <i class="bi {{ $lessonTypes[$lesson->loai]['icon'] ?? 'bi-journal-text' }}"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                    <div>
                                                        <h5 class="mb-1">{{ $lesson->tieuDe }}</h5>
                                                        <div class="d-flex flex-wrap gap-2 align-items-center">
                                                            <span class="lesson-chip">
                                                                <i class="bi {{ $lessonTypes[$lesson->loai]['icon'] ?? 'bi-journal-text' }}"></i>
                                                                {{ $lessonTypes[$lesson->loai]['label'] ?? 'Bài giảng' }}
                                                            </span>
                                                            <span class="lesson-chip">
                                                                <i class="bi bi-hash"></i> Thứ tự: {{ $lesson->thuTu }}
                                                            </span>
                                                            <span class="lesson-chip text-muted bg-transparent border-0">
                                                                <i class="bi bi-clock"></i> Cập nhật {{ optional($lesson->updated_at)->diffForHumans() ?? 'gần đây' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @php
                                                            $lessonData = [
                                                                "id" => $lesson->maBH,
                                                                "title" => $lesson->tieuDe,
                                                                "description" => $lesson->moTa,
                                                                "order" => $lesson->thuTu,
                                                                "type" => $lesson->loai,
                                                                "course" => $activeCourse->maKH,
                                                                "chapter" => $chapter->maChuong,
                                                            ];
                                                        @endphp
                                                        <button class="btn btn-outline-secondary btn-sm"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editLessonModal"
                                                                data-lesson='@json($lessonData)'>
                                                            <i class="bi bi-pencil-square me-1"></i> Chỉnh sửa
                                                        </button>
                                                        <button class="btn btn-outline-primary btn-sm"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#createMaterialModal"
                                                                data-lesson="{{ $lesson->maBH }}"
                                                                data-course="{{ $activeCourse->maKH }}">
                                                            <i class="bi bi-paperclip me-1"></i> Thêm tài liệu
                                                        </button>
                                                        <form action="{{ route('teacher.lectures.destroy', $lesson) }}" method="POST"
                                                              class="d-inline form-delete">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-danger-soft action-btn">
                                                                <i class="bi bi-trash me-1"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                                @if($lesson->moTa)
                                                    <p class="text-muted mt-2 mb-0">{{ $lesson->moTa }}</p>
                                                @endif

                                                @if($lesson->materials->isNotEmpty())
                                                    <div class="mt-3">
                                                        <div class="text-muted small text-uppercase mb-2">Tài liệu đính kèm</div>
                                                        <div class="vstack gap-2">
                                                            @foreach($lesson->materials as $material)
                                                                <div class="material-item">
                                                                    <div class="d-flex flex-column gap-1">
                                                                        <div class="d-flex align-items-center gap-2">
                                                                            <i class="bi bi-link-45deg text-primary"></i>
                                                                            <a href="{{ $material->public_url }}" target="_blank" class="fw-semibold text-decoration-none">
                                                                                {{ $material->tenTL }}
                                                                            </a>
                                                                        </div>
                                                                        <div class="d-flex flex-wrap gap-2 small text-muted">
                                                                            <span><i class="bi bi-tags"></i> {{ $material->loai }}</span>
                                                                            @if($material->kichThuoc)
                                                                                <span><i class="bi bi-hdd-stack"></i> {{ $material->kichThuoc }}</span>
                                                                            @endif
                                                                            <span><i class="bi bi-eye"></i> {{ strtoupper($material->visibility) }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <form action="{{ route('teacher.lectures.materials.destroy', $material) }}" method="POST"
                                                                          class="d-inline form-delete">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button class="btn btn-sm btn-danger-soft action-btn">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="alert alert-light border">
                                        Chương này chưa có bài giảng. Nhấn <strong>Thêm bài giảng</strong> để bắt đầu.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-warning">
                        Khóa học chưa có chương nào. Vui lòng liên hệ quản trị viên để tạo chương trước.
                    </div>
                @endforelse
            </div>
        @endif
    @endif

    <!-- Modal: Tạo bài giảng -->
    <div class="modal fade" id="createLessonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('teacher.lectures.store') }}">
                @csrf
                <input type="hidden" name="course_id" id="createLessonCourse">
                <input type="hidden" name="chapter_id" id="createLessonChapter">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Thêm bài giảng</h5>
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
                        <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Thứ tự</label>
                        <input type="number" class="form-control" name="order" min="1" placeholder="Tự động nếu để trống">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Tóm tắt mục tiêu, nội dung..."></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Loại nội dung <span class="text-danger">*</span></label>
                        <select class="form-select" name="type" required id="createLessonType">
                            @foreach($lessonTypes as $value => $meta)
                                <option value="{{ $value }}">{{ $meta['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kiểu tài liệu mặc định</label>
                        <select class="form-select" name="resource[type]" id="createResourcePreset">
                            <option value="">-- Không đính kèm --</option>
                            @foreach($resourcePresets as $mime => $meta)
                                <option value="{{ $meta['default_type'] }}" data-mime="{{ $mime }}">{{ $meta['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Tên tài liệu</label>
                        <input type="text" class="form-control" name="resource[name]" placeholder="Ví dụ: Video bài giảng 01">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Dung lượng hiển thị</label>
                        <input type="text" class="form-control" name="resource[size]" placeholder="Ví dụ: 350MB">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Đường dẫn</label>
                        <input type="url" class="form-control" name="resource[url]" placeholder="https://...">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Ghi chú tài liệu</label>
                        <textarea class="form-control" name="resource[summary]" rows="2" placeholder="Nội dung chính, hướng dẫn sử dụng..."></textarea>
                    </div>
                    <input type="hidden" name="resource[mime]" id="createResourceMime">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Tạo bài giảng</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Cập nhật bài giảng -->
    <div class="modal fade" id="editLessonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <form class="modal-content" method="POST" id="editLessonForm">
                @csrf
                @method('PATCH')
                <input type="hidden" name="course_id" id="editLessonCourse">
                <input type="hidden" name="chapter_id" id="editLessonChapter">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Cập nhật bài giảng</h5>
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
                        <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" id="editLessonTitle" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Thứ tự</label>
                        <input type="number" class="form-control" name="order" id="editLessonOrder" min="1">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="description" id="editLessonDescription" rows="3"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Loại nội dung</label>
                        <select class="form-select" name="type" id="editLessonType">
                            @foreach($lessonTypes as $value => $meta)
                                <option value="{{ $value }}">{{ $meta['label'] }}</option>
                            @endforeach
                        </select>
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
            <form class="modal-content" method="POST" id="createMaterialForm">
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
                        <input type="text" class="form-control" name="type" placeholder="Video, PDF, Link..." required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Đường dẫn <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" name="url" required placeholder="https://...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">MIME type</label>
                        <input type="text" class="form-control" name="mime_type" placeholder="application/pdf">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Dung lượng hiển thị</label>
                        <input type="text" class="form-control" name="size" placeholder="350MB">
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