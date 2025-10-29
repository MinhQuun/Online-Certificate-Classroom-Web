@extends('layouts.teacher')

@section('title', 'Chấm điểm bài làm')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Teacher/grading-show.css') }}">
@endpush

@section('content')
    <!-- Header -->
    <section class="page-header">
        <div class="d-flex align-items-center gap-3 mb-3">
            <a href="{{ route('teacher.grading.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i> Quay lại
            </a>
            <div>
                <span class="kicker">Chấm điểm</span>
                <h1 class="title mb-0">{{ $result->miniTest->title }}</h1>
                <p class="text-muted mb-0">{{ $result->miniTest->course->tenKH }} / {{ $result->miniTest->chapter->tenChuong }}</p>
            </div>
        </div>
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

    <form action="{{ route('teacher.grading.grade', $result->maKQDG) }}" method="POST">
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
                        <a href="{{ route('teacher.grading.index') }}" class="btn btn-outline-secondary me-2">
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

@endsection
