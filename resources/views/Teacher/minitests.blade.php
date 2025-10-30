@extends('layouts.teacher')

@section('title')
    @if($type == 'index')
        Quản lý Mini-Test
    @elseif($type == 'questions')
        Quản Lý Câu Hỏi - {{ $miniTest->title ?? 'Mini-Test' }}
    @endif
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Teacher/minitests.css') }}">
@endpush

@section('content')
    @if($type == 'index')
        <!-- Header -->
        <section class="page-header">
            <span class="kicker">Giảng viên</span>
            <h1 class="title">Quản lý Mini-Test</h1>
            <p class="muted">Tạo và quản lý bài kiểm tra mini cho từng chương học.</p>
        </section>

        @if($courses->isEmpty())
            <div class="alert alert-info border-0 shadow-sm">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-inboxes fs-3 text-primary"></i>
                    <div>
                        <h5 class="mb-1">Chưa có khóa học được phân công</h5>
                        <p class="mb-0">Khi quản trị viên gán bạn vào khóa học, bạn sẽ quản lý mini-test tại đây.</p>
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
                                    data-base-url="{{ route('teacher.minitests.index') }}">
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
                    <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#createMiniTestModal">
                        <i class="bi bi-plus-circle me-2"></i> Tạo Mini-Test Mới
                    </button>
                </div>
            </div>

            @if($activeCourse)
                @php
                    $totalTests = $activeCourse->chapters->sum(fn ($chapter) => $chapter->miniTests->count());
                    $activeTests = $activeCourse->chapters->sum(fn ($chapter) => $chapter->miniTests->where('is_active', true)->count());
                @endphp

                <!-- Metrics -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="metric-pill">
                            <div class="icon"><i class="bi bi-file-earmark-text"></i></div>
                            <div>
                                <div class="value">{{ $totalTests }}</div>
                                <div class="label">Tổng Mini-Test</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="metric-pill">
                            <div class="icon"><i class="bi bi-check-circle"></i></div>
                            <div>
                                <div class="value">{{ $activeTests }}</div>
                                <div class="label">Đang hoạt động</div>
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
                                    <div class="d-flex align-items-center gap-3 flex-grow-1">
                                        <div class="chapter-icon">
                                            <i class="bi bi-journal-bookmark-fill"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1">{{ $chapter->tenChuong }}</h5>
                                            <small class="text-muted">
                                                {{ $chapter->miniTests->count() }} mini-test
                                            </small>
                                        </div>
                                    </div>
                                </button>
                            </h2>

                            <div id="collapse-{{ $chapter->maChuong }}"
                                 class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                 aria-labelledby="chapter-{{ $chapter->maChuong }}">
                                <div class="accordion-body p-0">
                                    @if($chapter->miniTests->isEmpty())
                                        <div class="p-4 text-center text-muted">
                                            <i class="bi bi-inbox fs-3 mb-2 d-block"></i>
                                            Chưa có mini-test nào cho chương này.
                                            <button class="btn btn-link p-0"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#createMiniTestModal"
                                                    onclick="setChapterForCreate({{ $chapter->maChuong }}, '{{ $chapter->tenChuong }}')">
                                                Tạo mới?
                                            </button>
                                        </div>
                                    @else
                                        <div class="list-group list-group-flush">
                                            @foreach($chapter->miniTests as $miniTest)
                                                <div class="list-group-item minitest-item" id="minitest-{{ $miniTest->maMT }}">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="minitest-icon skill-{{ $miniTest->skill_type }}">
                                                            @php
                                                                $skillIcons = [
                                                                    'LISTENING' => '🎧',
                                                                    'SPEAKING' => '🗣️',
                                                                    'READING' => '📖',
                                                                    'WRITING' => '✍️'
                                                                ];
                                                            @endphp
                                                            {{ $skillIcons[$miniTest->skill_type] ?? '📝' }}
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">{{ $miniTest->title }}</h6>
                                                            <small class="text-muted">
                                                                {{ $miniTest->questions->count() }} câu hỏi • {{ $miniTest->time_limit_min }} phút
                                                            </small>
                                                        </div>
                                                        <div class="d-flex gap-2">
                                                            <a href="{{ route('teacher.minitests.questions', $miniTest->maMT) }}"
                                                               class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-pencil-square me-1"></i> Chỉnh sửa câu hỏi
                                                            </a>
                                                            <button class="btn btn-sm btn-outline-secondary edit-minitest-btn"
                                                                    data-minitest-id="{{ $miniTest->maMT }}"
                                                                    data-course-id="{{ $miniTest->maKH }}"
                                                                    data-chapter-id="{{ $chapter->maChuong }}"
                                                                    data-title="{{ $miniTest->title }}"
                                                                    data-skill-type="{{ $miniTest->skill_type }}"
                                                                    data-time-limit="{{ $miniTest->time_limit_min }}"
                                                                    data-attempts="{{ $miniTest->attempts_allowed }}"
                                                                    data-max-score="{{ $miniTest->max_score }}"
                                                                    data-weight="{{ $miniTest->weight }}"
                                                                    data-is-active="{{ $miniTest->is_active ? 1 : 0 }}">
                                                                <i class="bi bi-gear me-1"></i> Sửa
                                                            </button>
                                                            <form action="{{ route('teacher.minitests.destroy', $miniTest->maMT) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn chắc chắn muốn xóa mini-test này?')">
                                                                    <i class="bi bi-trash me-1"></i> Xóa
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    <!-- Materials -->
                                                    <div class="materials-section mt-3">
                                                        <h6 class="mb-2">
                                                            <i class="bi bi-folder2-open me-2"></i>Tài liệu ({{ $miniTest->materials->count() }})
                                                        </h6>
                                                        @if($miniTest->materials->isEmpty())
                                                            <p class="text-muted small mb-0">Chưa có tài liệu nào.</p>
                                                        @else
                                                            <div class="d-flex flex-wrap gap-2">
                                                                @foreach($miniTest->materials as $material)
                                                                    <div class="material-item">
                                                                        <i class="{{ $material->getIcon() }} me-1"></i>
                                                                        <a href="{{ $material->public_url }}" target="_blank" class="text-decoration-none">
                                                                            {{ Str::limit($material->name, 20) }}
                                                                        </a>
                                                                        <form action="{{ route('teacher.minitests.materials.destroy', $material->id) }}" method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-link text-danger p-0 ms-1" onclick="return confirm('Xóa tài liệu này?')">
                                                                                <i class="bi bi-x-circle"></i>
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                        <button class="btn btn-sm btn-outline-secondary mt-2 add-material-btn"
                                                                data-minitest-id="{{ $miniTest->maMT }}"
                                                                data-minitest-title="{{ $miniTest->title }}">
                                                            <i class="bi bi-plus-circle me-1"></i> Thêm tài liệu
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif

        <!-- Create Mini-Test Modal -->
        <div class="modal fade" id="createMiniTestModal" tabindex="-1" aria-labelledby="createMiniTestModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="createMiniTestModalLabel">Tạo Mini-Test Mới</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('teacher.minitests.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Khóa học *</label>
                                    <select name="course_id" id="create_course_id" class="form-select" required>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->maKH }}" @selected($activeCourse && $activeCourse->maKH === $course->maKH)>
                                                {{ $course->tenKH }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Chương học *</label>
                                    <select name="chapter_id" id="create_chapter_id" class="form-select" required>
                                        @foreach($activeCourse?->chapters ?? [] as $chapter)
                                            <option value="{{ $chapter->maChuong }}">{{ $chapter->tenChuong }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tiêu đề Mini-Test *</label>
                                <input type="text" name="title" class="form-control" required
                                       placeholder="VD: Kiểm tra kỹ năng nghe Part 1">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kỹ năng *</label>
                                    <select name="skill_type" class="form-select" required>
                                        <option value="LISTENING">🎧 Nghe (Listening)</option>
                                        <option value="SPEAKING">🗣️ Nói (Speaking)</option>
                                        <option value="READING">📖 Đọc (Reading)</option>
                                        <option value="WRITING">✍️ Viết (Writing)</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Thứ tự hiển thị</label>
                                    <input type="number" name="order" class="form-control" min="1"
                                           placeholder="Tự động nếu để trống">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Thời gian (phút)</label>
                                    <input type="number" name="time_limit" class="form-control" min="1" value="30">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số lần thử</label>
                                    <input type="number" name="attempts" class="form-control" min="1" value="3">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Điểm tối đa</label>
                                    <input type="number" name="max_score" class="form-control" step="0.01" min="0" value="10">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Trọng số (%)</label>
                                    <input type="number" name="weight" class="form-control" step="0.01" min="0" max="100" value="100">
                                </div>
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" name="is_active" class="form-check-input" id="create_is_active" value="1" checked>
                                <label class="form-check-label" for="create_is_active">
                                    Kích hoạt ngay
                                </label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Tạo Mini-Test
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Mini-Test Modal -->
        <div class="modal fade" id="editMiniTestModal" tabindex="-1" aria-labelledby="editMiniTestModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="editMiniTestModalLabel">Chỉnh Sửa Mini-Test</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editMiniTestForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <input type="hidden" name="course_id" id="edit_course_id">
                            <input type="hidden" name="chapter_id" id="edit_chapter_id">

                            <div class="mb-3">
                                <label class="form-label">Tiêu đề *</label>
                                <input type="text" name="title" id="edit_title" class="form-control" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kỹ năng *</label>
                                    <select name="skill_type" id="edit_skill_type" class="form-select" required>
                                        <option value="LISTENING">🎧 Nghe (Listening)</option>
                                        <option value="SPEAKING">🗣️ Nói (Speaking)</option>
                                        <option value="READING">📖 Đọc (Reading)</option>
                                        <option value="WRITING">✍️ Viết (Writing)</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Thứ tự</label>
                                    <input type="number" name="order" id="edit_order" class="form-control" min="1">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Thời gian (phút)</label>
                                    <input type="number" name="time_limit" id="edit_time_limit" class="form-control" min="1">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số lần thử</label>
                                    <input type="number" name="attempts" id="edit_attempts" class="form-control" min="1">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Điểm tối đa</label>
                                    <input type="number" name="max_score" id="edit_max_score" class="form-control" step="0.01" min="0">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Trọng số (%)</label>
                                    <input type="number" name="weight" id="edit_weight" class="form-control" step="0.01" min="0" max="100">
                                </div>
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" name="is_active" class="form-check-input" id="edit_is_active" value="1">
                                <label class="form-check-label" for="edit_is_active">
                                    Kích hoạt
                                </label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Add Material Modal -->
        <div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form id="addMaterialForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h4 class="modal-title" id="addMaterialModalLabel">Thêm tài liệu cho Mini-Test</h4>
                            <p class="text-muted small mb-0" id="materialMiniTestTitle"></p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-section">
                            <div class="form-group-gform">
                                <label for="material_name" class="form-label-gform">Tên tài liệu *</label>
                                <input type="text" name="name" id="material_name" class="form-control-gform"
                                       placeholder="VD: Câu hỏi Part 1, File audio câu 1" required>
                            </div>

                            <div class="form-group-gform">
                                <label for="material_type" class="form-label-gform">Loại tài liệu *</label>
                                <select name="type" id="material_type" class="form-control-gform" required>
                                    <option value="audio/mpeg">Audio (MP3)</option>
                                    <option value="application/pdf">PDF</option>
                                    <option value="image/jpeg">Hình ảnh</option>
                                    <option value="video/mp4">Video</option>
                                </select>
                            </div>

                            <div class="form-group-gform">
                                <label class="form-label-gform">Nguồn tài liệu *</label>
                                <div class="btn-group w-100 mb-3" role="group">
                                    <input type="radio" class="btn-check" name="source_type" id="source_file" value="file" checked>
                                    <label class="btn btn-outline-primary" for="source_file">
                                        <i class="bi bi-upload me-2"></i> Upload File
                                    </label>

                                    <input type="radio" class="btn-check" name="source_type" id="source_url" value="url">
                                    <label class="btn btn-outline-primary" for="source_url">
                                        <i class="bi bi-link-45deg me-2"></i> Nhập URL
                                    </label>
                                </div>

                                <div id="file_upload_section">
                                    <input type="file" name="file" id="material_file" class="form-control-gform"
                                           accept=".mp3,.pdf,.jpg,.jpeg,.png,.mp4">
                                    <small class="text-muted">Tối đa 100MB</small>
                                </div>

                                <div id="url_input_section" style="display: none;">
                                    <input type="url" name="url" id="material_url" class="form-control-gform"
                                           placeholder="https://example.com/file.mp3">
                                </div>
                            </div>

                            <div class="form-group-gform">
                                <label for="material_visibility" class="form-label-gform">Quyền truy cập</label>
                                <select name="visibility" id="material_visibility" class="form-control-gform">
                                    <option value="public">Công khai</option>
                                    <option value="private">Riêng tư</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i> Thêm tài liệu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @elseif($type == 'questions')
        <!-- Header -->
        <div class="page-header">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h3 class="mb-2">
                        <i class="bi bi-list-check me-2"></i>{{ $miniTest->title }}
                    </h3>
                    <p class="mb-0 opacity-90">{{ $miniTest->course->tenKH }} - {{ $miniTest->chapter->tenChuong }}</p>
                </div>
                <span class="skill-badge skill-{{ $miniTest->skill_type }}">
                    @php
                        $skillIcons = ['LISTENING' => '🎧', 'SPEAKING' => '🗣️', 'READING' => '📖', 'WRITING' => '✍️'];
                        $skillNames = ['LISTENING' => 'Nghe', 'SPEAKING' => 'Nói', 'READING' => 'Đọc', 'WRITING' => 'Viết'];
                    @endphp
                    {{ $skillIcons[$miniTest->skill_type] ?? '' }} {{ $skillNames[$miniTest->skill_type] ?? $miniTest->skill_type }}
                </span>
            </div>
            <a href="{{ route('teacher.minitests.index', ['course' => $miniTest->maKH]) }}" class="btn btn-light">
                <i class="bi bi-arrow-left me-2"></i>Quay lại danh sách
            </a>
        </div>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                @if($miniTest->skill_type === 'WRITING')
                    <div class="info-box">
                        <h6><i class="bi bi-info-circle me-2"></i>Lưu ý với kỹ năng Viết</h6>
                        <p class="mb-0">
                            - Chỉ tạo câu hỏi tự luận cho kỹ năng Viết.
                            - Học viên sẽ viết essay và bạn cần chấm thủ công.
                        </p>
                    </div>
                @endif

                <form id="questionsForm" action="{{ route('teacher.minitests.questions.store', $miniTest->maMT) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Existing Questions -->
                    @foreach($miniTest->questions as $index => $question)
                        <div class="question-card" data-question-index="{{ $index }}">
                            <div class="question-header">
                                <span class="question-number">Câu {{ $index + 1 }}</span>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-question">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nội dung câu hỏi *</label>
                                <textarea name="questions[{{ $index }}][content]" class="form-control" rows="3" required>{{ $question->noiDungCauHoi }}</textarea>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Loại câu hỏi *</label>
                                    <select name="questions[{{ $index }}][type]" class="form-select question-type" required>
                                        <option value="multiple_choice" {{ $question->loai === 'multiple_choice' ? 'selected' : '' }}>Trắc nghiệm</option>
                                        <option value="essay" {{ $question->loai === 'essay' ? 'selected' : '' }}>Tự luận</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Điểm *</label>
                                    <input type="number" name="questions[{{ $index }}][points]" class="form-control" step="0.1" min="0" value="{{ $question->diem }}" required>
                                </div>
                            </div>

                            <!-- Multiple Choice Options -->
                            <div class="multiple-choice-section" style="{{ $question->loai === 'essay' ? 'display: none;' : '' }}">
                                <h6 class="mb-3">Phương án trả lời</h6>
                                @foreach(['A', 'B', 'C', 'D'] as $option)
                                    <div class="answer-item mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text">{{ $option }}</span>
                                            <input type="text" name="questions[{{ $index }}][options][{{ $option }}]" class="form-control"
                                                   value="{{ $question->{'phuongAn' . $option} }}" placeholder="Nhập phương án {{ $option }}">
                                        </div>
                                        <div class="form-check mt-2">
                                            <input type="radio" name="questions[{{ $index }}][correct]" value="{{ $option }}" class="form-check-input"
                                                   {{ $question->dapAnDung === $option ? 'checked' : '' }}>
                                            <label class="form-check-label">Đáp án đúng</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Media Upload -->
                            <div class="media-upload-section">
                                <h6 class="mb-3">Tài liệu đính kèm (nếu có)</h6>
                                <div class="d-flex flex-wrap gap-2 mb-3 media-buttons">
                                    <button type="button" class="btn btn-outline-primary btn-sm upload-btn" data-type="audio">
                                        <i class="bi bi-volume-up me-1"></i> Thêm Audio
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm upload-btn" data-type="pdf">
                                        <i class="bi bi-file-pdf me-1"></i> Thêm PDF
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm upload-btn" data-type="image">
                                        <i class="bi bi-image me-1"></i> Thêm Hình ảnh
                                    </button>
                                </div>

                                <!-- Audio -->
                                @if($question->audio_url)
                                    <div class="mb-3">
                                        <label class="form-label">Audio hiện tại</label>
                                        <audio controls src="{{ $question->audio_url }}" class="w-100"></audio>
                                        <button type="button" class="btn btn-sm btn-danger mt-2 remove-media" data-type="audio">Xóa Audio</button>
                                    </div>
                                @else
                                    <input type="file" name="questions[{{ $index }}][audio]" accept="audio/*" class="d-none">
                                @endif

                                <!-- PDF -->
                                @if($question->pdf_url)
                                    <div class="mb-3">
                                        <label class="form-label">PDF hiện tại</label>
                                        <a href="{{ $question->pdf_url }}" target="_blank" class="btn btn-sm btn-info">Xem PDF</a>
                                        <button type="button" class="btn btn-sm btn-danger mt-2 remove-media" data-type="pdf">Xóa PDF</button>
                                    </div>
                                @else
                                    <input type="file" name="questions[{{ $index }}][pdf]" accept=".pdf" class="d-none">
                                @endif

                                <!-- Image -->
                                @if($question->image_url)
                                    <div class="mb-3">
                                        <label class="form-label">Hình ảnh hiện tại</label>
                                        <img src="{{ $question->image_url }}" alt="Question Image" class="img-fluid rounded" style="max-height: 200px;">
                                        <button type="button" class="btn btn-sm btn-danger mt-2 remove-media" data-type="image">Xóa Hình ảnh</button>
                                    </div>
                                @else
                                    <input type="file" name="questions[{{ $index }}][image]" accept="image/*" class="d-none">
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <!-- Add Question Button -->
                    <div class="text-center mb-4">
                        <button type="button" id="addQuestionBtn" class="btn btn-primary btn-lg">
                            <i class="bi bi-plus-circle me-2"></i> Thêm Câu Hỏi Mới
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">Hoàn tất?</h5>
                                <p class="text-muted mb-0">Lưu tất cả câu hỏi và quay về danh sách mini-test.</p>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg" id="saveBtn">
                                <i class="bi bi-check-circle me-2"></i> Lưu tất cả câu hỏi
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sticky-sidebar">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="mb-3"><i class="bi bi-bar-chart-fill me-2"></i> Tiến độ</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Số câu hỏi</span>
                                <strong id="questionCount">{{ $miniTest->questions->count() }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Điểm tổng</span>
                                <strong>{{ number_format($miniTest->questions->sum('diem'), 1) }}</strong>
                            </div>
                            <hr>
                            <h6 class="mb-3"><i class="bi bi-info-circle me-2"></i> Lưu ý</h6>
                            <ul class="small text-muted mb-0">
                                <li>Trắc nghiệm: Chọn đáp án đúng</li>
                                <li>Tự luận: Để trống phương án</li>
                                <li>Tài liệu: Tùy chọn cho mỗi câu</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Question Template -->
        <template id="questionTemplate">
            <div class="question-card" data-question-index="">
                <div class="question-header">
                    <span class="question-number">Câu </span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-question">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nội dung câu hỏi *</label>
                    <textarea name="questions[__INDEX__][content]" class="form-control" rows="3" required></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Loại câu hỏi *</label>
                        <select name="questions[__INDEX__][type]" class="form-select question-type" required>
                            <option value="multiple_choice">Trắc nghiệm</option>
                            <option value="essay">Tự luận</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Điểm *</label>
                        <input type="number" name="questions[__INDEX__][points]" class="form-control" step="0.1" min="0" value="1" required>
                    </div>
                </div>

                <!-- Multiple Choice Options -->
                <div class="multiple-choice-section">
                    <h6 class="mb-3">Phương án trả lời</h6>
                    @foreach(['A', 'B', 'C', 'D'] as $option)
                        <div class="answer-item mb-3">
                            <div class="input-group">
                                <span class="input-group-text">{{ $option }}</span>
                                <input type="text" name="questions[__INDEX__][options][{{ $option }}]" class="form-control"
                                       placeholder="Nhập phương án {{ $option }}">
                            </div>
                            <div class="form-check mt-2">
                                <input type="radio" name="questions[__INDEX__][correct]" value="{{ $option }}" class="form-check-input">
                                <label class="form-check-label">Đáp án đúng</label>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Media Upload -->
                <div class="media-upload-section">
                    <h6 class="mb-3">Tài liệu đính kèm (nếu có)</h6>
                    <div class="d-flex flex-wrap gap-2 mb-3 media-buttons">
                        <button type="button" class="btn btn-outline-primary btn-sm upload-btn" data-type="audio">
                            <i class="bi bi-volume-up me-1"></i> Thêm Audio
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm upload-btn" data-type="pdf">
                            <i class="bi bi-file-pdf me-1"></i> Thêm PDF
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm upload-btn" data-type="image">
                            <i class="bi bi-image me-1"></i> Thêm Hình ảnh
                        </button>
                    </div>

                    <input type="file" name="questions[__INDEX__][audio]" accept="audio/*" class="d-none">
                    <input type="file" name="questions[__INDEX__][pdf]" accept=".pdf" class="d-none">
                    <input type="file" name="questions[__INDEX__][image]" accept="image/*" class="d-none">
                </div>
            </div>
        </template>
    @endif
@endsection

@push('scripts')
<script src="{{ asset('js/Teacher/minitests.js') }}"></script>
@endpush
