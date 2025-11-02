{{-- resources/views/Teacher/grading-writing.blade.php --}}

@extends('layouts.teacher')

@section('title')
    @if($type == 'index')
        Chấm điểm mini-test Writing
    @elseif($type == 'show')
        Chấm điểm bài làm
    @endif
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Teacher/grading.css') }}">
@endpush

@section('content')
    @if($type == 'index')
        <!-- Header -->
        <section class="page-header">
            <span class="kicker">Giảng viên</span>
            <h1 class="title">Chấm điểm Writing</h1>
            <p class="muted">Chấm điểm các câu trả lời tự luận Writing của học viên.</p>
        </section>
        <div class="btn-group btn-group-sm mb-4">
            <a href="{{ route('teacher.grading.writing.index') }}" class="btn btn-primary">Writing</a>
            <a href="{{ route('teacher.grading.speaking.index') }}" class="btn btn-outline-secondary">Speaking</a>
        </div>

        <!-- Course Selector -->
        <div class="card border-0 shadow-sm mb-4 grading-filter">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <label for="courseFilter" class="form-label text-muted text-uppercase small mb-1">Lọc theo khóa học</label>
                        <select id="courseFilter" class="form-select form-select-lg">
                            <option value="">Tất cả khóa học</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->maKH }}" {{ $selectedCourseId == $course->maKH ? 'selected' : '' }}>
                                    {{ $course->tenKH }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="row g-2 mt-2 mt-md-0">
                            <div class="col-6">
                                <div class="metric-box">
                                    <div class="metric-value">{{ $results->total() }}</div>
                                    <div class="metric-label">Bài cần chấm</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="metric-box">
                                    <div class="metric-value">{{ $results->count() }}</div>
                                    <div class="metric-label">Trang này</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($results->isEmpty())
            <div class="alert alert-info border-0 shadow-sm">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-check-circle fs-3 text-success"></i>
                    <div>
                        <h5 class="mb-1">Không có bài cần chấm</h5>
                        <p class="mb-0">Tất cả bài test đã được chấm điểm hoặc chưa có học viên nộp bài.</p>
                    </div>
                </div>
            </div>
        @else
            <!-- Results List -->
            @foreach($results as $result)
                <div class="grading-card">
                    <div class="student-info">
                        <div class="student-avatar">
                            {{ strtoupper(substr($result->student->user->hoTen, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">{{ $result->student->user->hoTen }}</h5>
                            <p class="text-muted mb-0">
                                <i class="bi bi-envelope me-1"></i> {{ $result->student->user->email }}
                            </p>
                        </div>
                        <span class="pending-badge">
                            <i class="bi bi-clock me-1"></i> Chờ chấm
                        </span>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <strong>Khóa học:</strong><br>
                            {{ $result->miniTest->course->tenKH }}
                        </div>
                        <div class="col-md-4">
                            <strong>Mini-Test:</strong><br>
                            {{ $result->miniTest->title }}
                        </div>
                        <div class="col-md-4">
                            <strong>Nộp lúc:</strong><br>
                            {{ $result->nop_luc->format('d/m/Y H:i') }}
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- <div class="col-md-4">
                            <strong>Điểm trắc nghiệm:</strong>
                            <span class="badge bg-success">{{ number_format($result->auto_graded_score ?? 0, 2) }}</span>
                        </div> -->
                        <div class="col-md-4">
                            <strong>Số câu tự luận:</strong>
                            <span class="badge bg-info">{{ $result->studentAnswers->count() }}</span>
                        </div>
                        <div class="col-md-4">
                            <strong>Lần thử:</strong>
                            <span class="badge bg-secondary">{{ $result->attempt_no }}</span>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route($routePrefix . '.show', $result->maKQDG) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-2"></i> Chấm điểm
                        </a>
                        <a href="{{ route('teacher.minitests.index', ['course' => $result->miniTest->maKH]) }}"
                           class="btn btn-outline-secondary">
                            <i class="bi bi-eye me-2"></i> Xem Mini-Test
                        </a>
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $results->links() }}
            </div>
        @endif
    @elseif($type == 'show')
        <!-- Header -->
        <section class="page-header page-header--has-action">
            <div class="page-header-actions">
                <a href="{{ route($routePrefix . '.index') }}" class="btn btn-ghost back-link">
                    <i class="bi bi-arrow-left me-2"></i> Quay lại
                </a>
            </div>
            <span class="kicker">Chấm điểm</span>
            <h1 class="title">{{ $result->miniTest->title }}</h1>
            <p class="muted">{{ $result->miniTest->course->tenKH }} / {{ $result->miniTest->chapter->tenChuong }}</p>
        </section>

        <!-- Student Info -->
        <div class="info-box">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-person-circle me-2"></i> Thông tin học viên</h5>
                    <p class="mb-1"><strong>Họ tên:</strong> {{ $result->student->user->hoTen }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $result->student->user->email }}</p>
                    <p class="mb-0"><strong>Nộp bài:</strong> {{ $result->nop_luc->format('d/m/Y H:i') }}</p>
                </div>
                <div class="col-md-6">
                    <h5><i class="bi bi-clipboard-check me-2"></i> Thông tin bài làm</h5>
                    <p class="mb-1"><strong>Lần thử:</strong> {{ $result->attempt_no }}</p>
                    <p class="mb-1"><strong>Điểm trắc nghiệm:</strong>
                        <span class="badge bg-success">{{ number_format($result->auto_graded_score ?? 0, 2) }}</span>
                    </p>
                    <p class="mb-0"><strong>Điểm tối đa:</strong> {{ $result->miniTest->max_score }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route($routePrefix . '.grade', $result->maKQDG) }}" method="POST">
            @csrf

            @foreach($result->studentAnswers as $answer)
                @if($answer->question->loai === 'essay' && !$answer->isGraded())
                    <div class="answer-card">
                        <div class="question-header">
                            <h5 class="mb-2">
                                <span class="badge bg-primary me-2">Câu {{ $loop->iteration }}</span>
                                Điểm tối đa: {{ $answer->question->diem }}
                            </h5>
                            <p class="mb-0">{{ $answer->question->noiDungCauHoi }}</p>
                        </div>

                        @if($answer->question->image_url)
                            <div class="mb-3">
                                <img src="{{ $answer->question->image_url }}" alt="Question" style="max-width: 100%; max-height: 300px; border-radius: 8px;">
                            </div>
                        @endif

                        @if($answer->question->pdf_url)
                            <div class="mb-3">
                                <a href="{{ $answer->question->pdf_url }}" target="_blank" class="btn btn-outline-primary">
                                    <i class="bi bi-file-pdf me-2"></i> Xem tài liệu PDF
                                </a>
                            </div>
                        @endif

                        <div class="student-answer">
                            <h6 class="mb-2"><i class="bi bi-chat-quote me-2"></i> Câu trả lời của học viên:</h6>
                            <p class="mb-0" style="white-space: pre-wrap;">{{ $answer->answer_text }}</p>
                        </div>

                        <div class="scoring-section">
                            <input type="hidden" name="grades[{{ $loop->index }}][answer_id]" value="{{ $answer->id }}">

                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label"><strong>Điểm *</strong></label>
                                    <input type="number"
                                           name="grades[{{ $loop->index }}][score]"
                                           class="form-control score-input"
                                           min="0"
                                           max="{{ $answer->question->diem }}"
                                           step="0.5"
                                           placeholder="0.0"
                                           required>
                                    <small class="text-muted">Tối đa: {{ $answer->question->diem }}</small>
                                </div>
                                <div class="col-md-9">
                                    <label class="form-label"><strong>Phản hồi cho học viên</strong></label>
                                    <textarea name="grades[{{ $loop->index }}][feedback]"
                                              class="form-control"
                                              rows="3"
                                              placeholder="Nhập nhận xét, góp ý cho học viên..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

            <!-- Submit Buttons -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-1">Hoàn tất chấm điểm</h6>
                            <p class="text-muted small mb-0">Điểm sẽ được lưu và thông báo cho học viên</p>
                        </div>
                        <div class="col-md-6 text-md-end mt-3 mt-md-0">
                            <a href="{{ route($routePrefix . '.index') }}" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-x-circle me-2"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle me-2"></i> Lưu điểm
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif
@endsection

@push('scripts')
    @if($type == 'index')
        <script src="{{ asset('js/Teacher/grading.js') }}"></script>
    @endif
@endpush
