@extends('layouts.teacher')

@section('title', 'Kỳ thi cuối khóa')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Teacher/exams.css') }}">
@endpush

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <span class="kicker">Giảng viên</span>
            <h1 class="title mb-0">Kỳ thi cuối khóa</h1>
            <p class="muted mb-0">Lập kế hoạch thi cuối khóa và quản lý tài liệu đính kèm cho học viên.</p>
        </div>
        @if($activeCourse)
            <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#createExamModal"
                data-course="{{ $activeCourse->maKH }}">
                <i class="bi bi-plus-circle me-1"></i> Tạo kỳ thi
            </button>
        @endif
    </div>

    @if($courses->isEmpty())
        <div class="alert alert-info border-0 shadow-sm">
            <i class="bi bi-info-circle me-2"></i>
            Chưa có khóa học nào được phân công. Vui lòng liên hệ quản trị viên.
        </div>
    @else
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body d-flex flex-wrap align-items-center gap-3">
                <div class="flex-grow-1">
                    <label class="form-label text-muted text-uppercase small mb-1">Khóa học</label>
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <select id="examCourseSelector" class="form-select form-select-lg w-auto"
                            data-base-url="{{ route('teacher.exams.index') }}">
                            @foreach($courses as $course)
                                <option value="{{ $course->maKH }}" @selected($activeCourse && $activeCourse->maKH === $course->maKH)>
                                    {{ $course->tenKH }}
                                </option>
                            @endforeach
                        </select>
                        <div class="badge bg-light text-dark border">
                            <i class="bi bi-clipboard-check me-1"></i> {{ $stats['tests'] ?? 0 }} kỳ thi
                        </div>
                        <div class="badge bg-light text-dark border">
                            <i class="bi bi-paperclip me-1"></i> {{ $stats['materials'] ?? 0 }} tài liệu
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($activeCourse)
            @if($activeCourse->finalTests->isEmpty())
                <div class="alert alert-light border">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-clipboard-check fs-4 text-muted"></i>
                        <div>
                            <h5 class="mb-1">Chưa có kỳ thi cuối khóa</h5>
                            <p class="mb-0 text-muted">Nhấn nút “Tạo kỳ thi” để tạo bài thi tổng kết đầu tiên cho lớp.</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="vstack gap-3">
                    @foreach($activeCourse->finalTests as $exam)
                        <div class="exam-card" id="exam-{{ $exam->maTest }}">
                            <div class="d-flex flex-wrap justify-content-between gap-3">
                                <div>
                                    <h4 class="mb-1">{{ $exam->title }}</h4>
                                    <div class="exam-meta">
                                        @if($exam->dotTest)
                                            <span class="pill"><i class="bi bi-calendar-event"></i> Đợt thi: {{ $exam->dotTest }}</span>
                                        @endif
                                        @if(!is_null($exam->time_limit_min))
                                            <span class="pill"><i class="bi bi-alarm"></i> Thời gian: {{ $exam->time_limit_min }} phút</span>
                                        @endif
                                        @if(!is_null($exam->total_questions))
                                            <span class="pill"><i class="bi bi-list-ol"></i> {{ $exam->total_questions }} câu hỏi</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    @php
                                        $examData = [
                                            'id' => $exam->maTest,
                                            'course' => $exam->maKH,
                                            'title' => $exam->title,
                                            'dotTest' => $exam->dotTest,
                                            'time_limit_min' => $exam->time_limit_min,
                                            'total_questions' => $exam->total_questions,
                                        ];
                                    @endphp
                                    <button class="btn btn-outline-secondary btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editExamModal"
                                            data-exam="@json($examData)">
                                        <i class="bi bi-pencil-square me-1"></i> Chỉnh sửa
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#createExamMaterialModal"
                                        data-exam="{{ $exam->maTest }}">
                                        <i class="bi bi-paperclip me-1"></i> Thêm tài liệu
                                    </button>
                                    <form action="{{ route('teacher.exams.destroy', $exam) }}" method="POST"
                                        onsubmit="return confirm('Xóa kỳ thi này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link text-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                            @if($exam->materials->isNotEmpty())
                                <div class="mt-3">
                                    <div class="text-muted text-uppercase small mb-2">Tài liệu kèm theo</div>
                                    <div class="vstack gap-2">
                                        @foreach($exam->materials as $material)
                                            <div class="exam-material">
                                                <div>
                                                    <a href="{{ $material->public_url }}" target="_blank" class="fw-semibold text-decoration-none">
                                                        {{ $material->tenTL }}
                                                    </a>
                                                    <div class="text-muted small">{{ $material->loai }} • {{ strtoupper($material->visibility) }}</div>
                                                </div>
                                                <form action="{{ route('teacher.exams.materials.destroy', $material) }}" method="POST"
                                                    onsubmit="return confirm('Xóa tài liệu này?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-link text-danger"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    @endif

    <div class="modal fade" id="createExamModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ route('teacher.exams.store') }}">
                @csrf
                <input type="hidden" name="course_id" id="createExamCourse">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tạo kỳ thi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Đợt thi</label>
                            <input type="text" class="form-control" name="dotTest" placeholder="Ví dụ: Tháng 12/2025">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Thời gian (phút)</label>
                            <input type="number" class="form-control" name="time_limit_min" min="0" max="600" placeholder="Ví dụ: 90">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tổng số câu</label>
                            <input type="number" class="form-control" name="total_questions" min="0" max="500" placeholder="Ví dụ: 100">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Tạo kỳ thi</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editExamModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form class="modal-content" method="POST" id="editExamForm">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Cập nhật kỳ thi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label">Tiêu đề</label>
                            <input type="text" class="form-control" name="title" id="editExamTitle" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Đợt thi</label>
                            <input type="text" class="form-control" name="dotTest" id="editExamDot">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Thời gian (phút)</label>
                            <input type="number" class="form-control" name="time_limit_min" id="editExamTime" min="0" max="600">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tổng số câu</label>
                            <input type="number" class="form-control" name="total_questions" id="editExamQuestions" min="0" max="500">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="createExamMaterialModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form class="modal-content" method="POST" id="createExamMaterialForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-paperclip me-2"></i>Thêm tài liệu kỳ thi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tên tài liệu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Loại <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="type" placeholder="PDF, ZIP, Video..." required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Đường dẫn <span class="text-danger">*</span></label>
                            <input type="url" class="form-control" name="url" placeholder="https://..." required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">MIME type</label>
                            <input type="text" class="form-control" name="mime_type" placeholder="application/pdf">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Chế độ</label>
                            <select class="form-select" name="visibility">
                                <option value="public">Công khai</option>
                                <option value="private">Riêng tư</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm tài liệu</button>
                </div>
            </form>
        </div>
    </div>

    <div id="teacherExamsConfig" class="d-none"
            data-update-route="{{ route('teacher.exams.update', ['exam' => '__ID__']) }}"
            data-material-route="{{ route('teacher.exams.materials.store', ['exam' => '__ID__']) }}">
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/Teacher/exams.js') }}"></script>
@endpush
