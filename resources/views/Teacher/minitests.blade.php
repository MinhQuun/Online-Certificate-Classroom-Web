@extends('layouts.teacher')

@section('title', 'Quản lý Mini-Test')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Teacher/minitests.css') }}">
@endpush

@section('content')
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
                                <span class="me-2">{{ $chapter->thuTu }}.</span> {{ $chapter->tenChuong }}
                                <span class="badge bg-primary ms-auto me-3">{{ $chapter->miniTests->count() }} test</span>
                            </button>
                        </h2>
                        <div id="collapse-{{ $chapter->maChuong }}"
                             class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                             aria-labelledby="chapter-{{ $chapter->maChuong }}"
                             data-bs-parent="#chaptersAccordion">
                            <div class="accordion-body">
                                @if($chapter->miniTests->isEmpty())
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-1"></i>
                                        <p class="mt-2">Chưa có mini-test nào cho chương này</p>
                                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createMiniTestModal"
                                                data-chapter-id="{{ $chapter->maChuong }}"
                                                data-chapter-name="{{ $chapter->tenChuong }}">
                                            <i class="bi bi-plus-circle me-2"></i> Tạo Mini-Test Đầu Tiên
                                        </button>
                                    </div>
                                @else
                                    @foreach($chapter->miniTests as $miniTest)
                                        <div class="minitest-card" id="minitest-{{ $miniTest->maMT }}">
                                            <div class="minitest-header">
                                                <div class="d-flex align-items-start justify-content-between">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center gap-2 mb-2">
                                                            <span class="badge bg-secondary">Test {{ $miniTest->thuTu }}</span>
                                                            @php
                                                                $skillIcons = [
                                                                    'LISTENING' => '🎧',
                                                                    'SPEAKING' => '🗣️',
                                                                    'READING' => '📖',
                                                                    'WRITING' => '✍️'
                                                                ];
                                                                $skillNames = [
                                                                    'LISTENING' => 'Nghe',
                                                                    'SPEAKING' => 'Nói',
                                                                    'READING' => 'Đọc',
                                                                    'WRITING' => 'Viết'
                                                                ];
                                                            @endphp
                                                            <span class="badge bg-info">
                                                                {{ $skillIcons[$miniTest->skill_type] ?? '' }} 
                                                                {{ $skillNames[$miniTest->skill_type] ?? $miniTest->skill_type }}
                                                            </span>
                                                            @if($miniTest->is_published)
                                                                <span class="badge bg-success">
                                                                    <i class="bi bi-check-circle me-1"></i> Đã công bố
                                                                </span>
                                                            @else
                                                                <span class="badge bg-warning text-dark">
                                                                    <i class="bi bi-clock me-1"></i> Nháp
                                                                </span>
                                                            @endif
                                                            @if($miniTest->is_active)
                                                                <span class="badge bg-success">Đang hoạt động</span>
                                                            @else
                                                                <span class="badge bg-secondary">Đã tắt</span>
                                                            @endif
                                                        </div>
                                                        <h5 class="minitest-title">{{ $miniTest->title }}</h5>
                                                        <div class="minitest-meta">
                                                            <span><i class="bi bi-question-circle me-1"></i> {{ $miniTest->questions->count() }} câu hỏi</span>
                                                            <span><i class="bi bi-clock me-1"></i> {{ $miniTest->time_limit_min }} phút</span>
                                                            <span><i class="bi bi-trophy me-1"></i> {{ $miniTest->max_score }} điểm</span>
                                                            <span><i class="bi bi-arrow-repeat me-1"></i> {{ $miniTest->attempts_allowed }} lần thử</span>
                                                        </div>
                                                    </div>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-light" type="button"
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-three-dots-vertical"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a class="dropdown-item"
                                                                   href="{{ route('teacher.minitests.questions.form', $miniTest->maMT) }}">
                                                                    <i class="bi bi-list-check me-2"></i> Quản lý câu hỏi
                                                                </a>
                                                            </li>
                                                            @if($miniTest->is_published)
                                                                <li>
                                                                    <form action="{{ route('teacher.minitests.unpublish', $miniTest->maMT) }}" method="POST">
                                                                        @csrf
                                                                        <button type="submit" class="dropdown-item">
                                                                            <i class="bi bi-x-circle me-2"></i> Hủy công bố
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @else
                                                                <li>
                                                                    <form action="{{ route('teacher.minitests.publish', $miniTest->maMT) }}" method="POST"
                                                                          onsubmit="return confirm('Công bố mini-test này? Học viên sẽ có thể xem và làm bài.')">
                                                                        @csrf
                                                                        <button type="submit" class="dropdown-item text-success">
                                                                            <i class="bi bi-check-circle me-2"></i> Công bố mini-test
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @endif
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a class="dropdown-item edit-minitest-btn"
                                                                   href="#"
                                                                   data-minitest-id="{{ $miniTest->maMT }}"
                                                                   data-course-id="{{ $activeCourse->maKH }}"
                                                                   data-chapter-id="{{ $chapter->maChuong }}"
                                                                   data-title="{{ $miniTest->title }}"
                                                                   data-skill-type="{{ $miniTest->skill_type }}"
                                                                   data-order="{{ $miniTest->thuTu }}"
                                                                   data-max-score="{{ $miniTest->max_score }}"
                                                                   data-weight="{{ $miniTest->trongSo }}"
                                                                   data-time-limit="{{ $miniTest->time_limit_min }}"
                                                                   data-attempts="{{ $miniTest->attempts_allowed }}"
                                                                   data-is-active="{{ $miniTest->is_active ? '1' : '0' }}">
                                                                    <i class="bi bi-pencil me-2"></i> Chỉnh sửa
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item add-material-btn"
                                                                   href="#"
                                                                   data-minitest-id="{{ $miniTest->maMT }}"
                                                                   data-minitest-title="{{ $miniTest->title }}">
                                                                    <i class="bi bi-paperclip me-2"></i> Thêm tài liệu
                                                                </a>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form action="{{ route('teacher.minitests.destroy', $miniTest->maMT) }}" method="POST" 
                                                                      onsubmit="return confirm('Xác nhận xóa mini-test này?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item text-danger">
                                                                        <i class="bi bi-trash me-2"></i> Xóa
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            @if($miniTest->materials->isNotEmpty())
                                                <div class="minitest-materials mt-3">
                                                    <h6 class="text-muted mb-2">
                                                        <i class="bi bi-paperclip me-1"></i> Tài liệu đính kèm
                                                    </h6>
                                                    <div class="materials-grid">
                                                        @foreach($miniTest->materials as $material)
                                                            <div class="material-item">
                                                                <div class="material-icon">
                                                                    @if(str_contains($material->mime_type, 'audio'))
                                                                        <i class="bi bi-music-note-beamed"></i>
                                                                    @elseif(str_contains($material->mime_type, 'pdf'))
                                                                        <i class="bi bi-file-pdf"></i>
                                                                    @elseif(str_contains($material->mime_type, 'image'))
                                                                        <i class="bi bi-image"></i>
                                                                    @else
                                                                        <i class="bi bi-file-earmark"></i>
                                                                    @endif
                                                                </div>
                                                                <div class="material-info">
                                                                    <div class="material-name">{{ $material->tenTL }}</div>
                                                                    <div class="material-type">{{ $material->loai }}</div>
                                                                </div>
                                                                <div class="material-actions">
                                                                    <a href="{{ $material->public_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                        <i class="bi bi-download"></i>
                                                                    </a>
                                                                    <form action="{{ route('teacher.minitests.materials.destroy', $material->id) }}" method="POST" 
                                                                          onsubmit="return confirm('Xác nhận xóa tài liệu này?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    <!-- Modal: Tạo Mini-Test -->
    <div class="modal fade" id="createMiniTestModal" tabindex="-1" aria-labelledby="createMiniTestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <form class="modal-content google-form-style" method="POST" action="{{ route('teacher.minitests.store') }}">
                @csrf
                <div class="modal-header border-0">
                    <div>
                        <h4 class="modal-title" id="createMiniTestModalLabel">Tạo Mini-Test Mới</h4>
                        <p class="text-muted small mb-0">Điền thông tin để tạo bài kiểm tra mini</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="course_id" id="create_course_id" value="{{ $activeCourse?->maKH }}">

                    <div class="form-section">
                        <div class="form-group-gform">
                            <label for="create_chapter_id" class="form-label-gform">Chương học *</label>
                            <select name="chapter_id" id="create_chapter_id" class="form-control-gform" required>
                                <option value="">-- Chọn chương --</option>
                                @if($activeCourse)
                                    @foreach($activeCourse->chapters as $chapter)
                                        <option value="{{ $chapter->maChuong }}">{{ $chapter->thuTu }}. {{ $chapter->tenChuong }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="form-group-gform">
                            <label for="create_title" class="form-label-gform">Tiêu đề mini-test *</label>
                            <input type="text" name="title" id="create_title" class="form-control-gform" 
                                   placeholder="VD: Mini-test 1 - Photographs" required>
                        </div>

                        <div class="form-group-gform">
                            <label for="create_skill_type" class="form-label-gform">Kỹ năng *</label>
                            <select name="skill_type" id="create_skill_type" class="form-control-gform" required>
                                <option value="">-- Chọn kỹ năng --</option>
                                <option value="LISTENING">🎧 Nghe (Listening)</option>
                                <option value="SPEAKING">🗣️ Nói (Speaking)</option>
                                <option value="READING">📖 Đọc (Reading)</option>
                                <option value="WRITING">✍️ Viết (Writing)</option>
                            </select>
                            <small class="text-muted">
                                <strong>Lưu ý:</strong> Kỹ năng Viết sẽ cần giảng viên chấm điểm thủ công
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-gform">
                                    <label for="create_order" class="form-label-gform">Thứ tự</label>
                                    <input type="number" name="order" id="create_order" class="form-control-gform" 
                                           placeholder="1" min="1">
                                    <small class="text-muted">Để trống để tự động thêm vào cuối</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-gform">
                                    <label for="create_max_score" class="form-label-gform">Điểm tối đa</label>
                                    <input type="number" name="max_score" id="create_max_score" class="form-control-gform" 
                                           value="10" step="0.5" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-gform">
                                    <label for="create_time_limit" class="form-label-gform">Thời gian (phút)</label>
                                    <input type="number" name="time_limit" id="create_time_limit" class="form-control-gform" 
                                           value="10" min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-gform">
                                    <label for="create_attempts" class="form-label-gform">Số lần thử</label>
                                    <input type="number" name="attempts" id="create_attempts" class="form-control-gform" 
                                           value="1" min="1">
                                </div>
                            </div>
                        </div>

                        <div class="form-group-gform">
                            <label for="create_weight" class="form-label-gform">Trọng số (%)</label>
                            <input type="number" name="weight" id="create_weight" class="form-control-gform" 
                                   value="0" step="0.5" min="0" max="100">
                            <small class="text-muted">Trọng số trong tính điểm tổng kết</small>
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="create_is_active" checked value="1">
                            <label class="form-check-label" for="create_is_active">
                                Kích hoạt ngay (học viên có thể làm bài)
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i> Tạo Mini-Test
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Chỉnh sửa Mini-Test -->
    <div class="modal fade" id="editMiniTestModal" tabindex="-1" aria-labelledby="editMiniTestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <form class="modal-content google-form-style" method="POST" id="editMiniTestForm">
                @csrf
                @method('PATCH')
                <div class="modal-header border-0">
                    <div>
                        <h4 class="modal-title" id="editMiniTestModalLabel">Chỉnh sửa Mini-Test</h4>
                        <p class="text-muted small mb-0">Cập nhật thông tin bài kiểm tra</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="course_id" id="edit_course_id">

                    <div class="form-section">
                        <div class="form-group-gform">
                            <label for="edit_chapter_id" class="form-label-gform">Chương học *</label>
                            <select name="chapter_id" id="edit_chapter_id" class="form-control-gform" required>
                                <option value="">-- Chọn chương --</option>
                                @if($activeCourse)
                                    @foreach($activeCourse->chapters as $chapter)
                                        <option value="{{ $chapter->maChuong }}">{{ $chapter->thuTu }}. {{ $chapter->tenChuong }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="form-group-gform">
                            <label for="edit_title" class="form-label-gform">Tiêu đề mini-test *</label>
                            <input type="text" name="title" id="edit_title" class="form-control-gform" required>
                        </div>

                        <div class="form-group-gform">
                            <label for="edit_skill_type" class="form-label-gform">Kỹ năng *</label>
                            <select name="skill_type" id="edit_skill_type" class="form-control-gform" required>
                                <option value="">-- Chọn kỹ năng --</option>
                                <option value="LISTENING">🎧 Nghe (Listening)</option>
                                <option value="SPEAKING">🗣️ Nói (Speaking)</option>
                                <option value="READING">📖 Đọc (Reading)</option>
                                <option value="WRITING">✍️ Viết (Writing)</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-gform">
                                    <label for="edit_order" class="form-label-gform">Thứ tự</label>
                                    <input type="number" name="order" id="edit_order" class="form-control-gform" min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-gform">
                                    <label for="edit_max_score" class="form-label-gform">Điểm tối đa</label>
                                    <input type="number" name="max_score" id="edit_max_score" class="form-control-gform" step="0.5" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-gform">
                                    <label for="edit_time_limit" class="form-label-gform">Thời gian (phút)</label>
                                    <input type="number" name="time_limit" id="edit_time_limit" class="form-control-gform" min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-gform">
                                    <label for="edit_attempts" class="form-label-gform">Số lần thử</label>
                                    <input type="number" name="attempts" id="edit_attempts" class="form-control-gform" min="1">
                                </div>
                            </div>
                        </div>

                        <div class="form-group-gform">
                            <label for="edit_weight" class="form-label-gform">Trọng số (%)</label>
                            <input type="number" name="weight" id="edit_weight" class="form-control-gform" step="0.5" min="0" max="100">
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1">
                            <label class="form-check-label" for="edit_is_active">
                                Kích hoạt (học viên có thể làm bài)
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i> Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Thêm tài liệu -->
    <div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form class="modal-content google-form-style" id="addMaterialForm">
                @csrf
                <div class="modal-header border-0">
                    <div>
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

    <!-- Hidden config for JS -->
    <div id="teacherMinitestsConfig" class="d-none"
         data-csrf="{{ csrf_token() }}"
         data-update-route="{{ route('teacher.minitests.update', ['miniTest' => '__ID__']) }}"
         data-material-route="{{ route('teacher.minitests.materials.store', ['miniTest' => '__ID__']) }}"
         data-course-id="{{ $activeCourse?->maKH ?? '' }}">
    </div>

    @push('scripts')
        <script src="{{ asset('js/Teacher/minitests.js') }}"></script>
    @endpush
@endsection
