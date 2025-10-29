@extends('layouts.student')

@section('title', 'Kết quả - ' . $result->miniTest->title)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Student/minitests-result.css') }}">
@endpush

@section('content')
    <!-- Back Button -->
    <div class="container mb-3">
        <a href="{{ route('student.courses.show', $result->miniTest->chapter->course->slug) }}" 
           class="btn btn-link text-decoration-none p-0 d-inline-flex align-items-center gap-2"
           style="color: #667eea; font-weight: 600;">
            <i class="bi bi-arrow-left-circle fs-5"></i>
            <span>Quay lại khóa học</span>
        </a>
    </div>

    <!-- Result Header -->
    <div class="result-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-4 text-center">
                    <div class="header-icon-result">
                        <i class="bi bi-award"></i>
                    </div>
                    <div class="score-circle">
                        <span class="score-value">{{ number_format($result->diem ?? 0, 1) }}</span>
                        <span class="score-max">/ {{ $result->miniTest->max_score }}</span>
                        <span class="score-label">Điểm số</span>
                    </div>
                </div>
                <div class="col-lg-8 mt-4 mt-lg-0">
                    <div class="mb-3">
                        <span class="badge" style="background: rgba(255,255,255,0.3); padding: 8px 16px; font-size: 14px;">
                            <i class="bi bi-folder2-open me-1"></i>{{ $result->miniTest->chapter->tenChuong }}
                        </span>
                    </div>
                    <h2 class="mb-4" style="font-weight: 700;">{{ $result->miniTest->title }}</h2>
                    
                    <div class="mb-4">
                        @if($result->is_fully_graded)
                            <span class="status-badge badge-graded">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Đã chấm xong</span>
                            </span>
                        @else
                            <span class="status-badge badge-pending">
                                <i class="bi bi-hourglass-split"></i>
                                <span>Đang chờ giảng viên chấm điểm</span>
                            </span>
                        @endif
                    </div>

                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <div class="stats-card">
                                <span class="stats-value">{{ $result->attempt_no }}</span>
                                <span class="stats-label">Lần làm</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stats-card">
                                <span class="stats-value text-success">{{ $correctCount }}</span>
                                <span class="stats-label">Đúng</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stats-card">
                                <span class="stats-value text-danger">{{ $incorrectCount }}</span>
                                <span class="stats-label">Sai</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stats-card">
                                <span class="stats-value text-warning">{{ $essayCount }}</span>
                                <span class="stats-label">Tự luận</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Score Breakdown -->
        @if($result->auto_graded_score !== null || $result->essay_score !== null)
            <div class="alert border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #e7f3ff 0%, #d6ebff 100%); border-left: 5px solid #667eea !important; border-radius: 16px; padding: 25px;">
                <h5 class="mb-3" style="color: #667eea;">
                    <i class="bi bi-calculator-fill me-2"></i>Chi tiết điểm
                </h5>
                <div class="row g-3">
                    @if($result->auto_graded_score !== null)
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-3">
                                <i class="bi bi-robot fs-3 text-success"></i>
                                <div>
                                    <div class="small text-muted">Trắc nghiệm (tự động chấm)</div>
                                    <div class="fs-4 fw-bold text-success">{{ number_format($result->auto_graded_score, 1) }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($result->essay_score !== null)
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-3">
                                <i class="bi bi-person-check fs-3 text-primary"></i>
                                <div>
                                    <div class="small text-muted">Tự luận (giảng viên chấm)</div>
                                    <div class="fs-4 fw-bold text-primary">{{ number_format($result->essay_score, 1) }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Answers Review -->
        <div class="d-flex align-items-center gap-3 mb-4">
            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-list-check text-white fs-4"></i>
            </div>
            <h4 class="mb-0" style="font-weight: 700;">Chi tiết câu trả lời</h4>
        </div>

        @foreach($result->studentAnswers as $index => $answer)
            @php
                $question = $answer->question;
                $isEssay = $question->loai === 'essay';
                $isGraded = $answer->graded_at !== null;
                
                $cardClass = 'answer-card ';
                if ($isEssay) {
                    $cardClass .= $isGraded ? 'essay graded' : 'essay';
                } else {
                    $cardClass .= $answer->is_correct ? 'correct' : 'incorrect';
                }
            @endphp

            <div class="{{ $cardClass }}">
                <div class="d-flex align-items-start">
                    <span class="question-number">{{ $index + 1 }}</span>
                    <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="mb-2" style="font-weight: 600; font-size: 17px;">{!! nl2br(e($question->noiDungCauHoi)) !!}</h6>
                                <div class="d-flex gap-2 flex-wrap">
                                    <span class="badge" style="background: linear-gradient(135deg, #ffc107, #ff9800); color: white; padding: 6px 14px;">
                                        {{ $question->diem }} điểm
                                    </span>
                                    @if($isEssay)
                                        @if($isGraded)
                                            <span class="badge" style="background: linear-gradient(135deg, #17a2b8, #138496); color: white; padding: 6px 14px;">
                                                <i class="bi bi-check-circle-fill me-1"></i>
                                                Đã chấm: {{ number_format($answer->diem, 1) }}/{{ $question->diem }}
                                            </span>
                                        @else
                                            <span class="badge" style="background: linear-gradient(135deg, #ffc107, #ff9800); color: white; padding: 6px 14px;">
                                                <i class="bi bi-hourglass-split me-1"></i>Chưa chấm
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div>
                                @if(!$isEssay)
                                    @if($answer->is_correct)
                                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #28a745, #20c997); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-check-lg text-white fs-3"></i>
                                        </div>
                                    @else
                                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #dc3545, #c82333); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-x-lg text-white fs-3"></i>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <!-- Media Display -->
                        @if($question->audio_url)
                            <div class="media-container">
                                <label class="form-label mb-2">
                                    <i class="bi bi-volume-up me-2"></i>Audio:
                                </label>
                                <audio controls class="w-100" controlsList="nodownload">
                                    <source src="{{ $question->audio_url }}" type="audio/mpeg">
                                </audio>
                            </div>
                        @endif

                        @if($question->pdf_url)
                            <div class="media-container">
                                <label class="form-label mb-2">
                                    <i class="bi bi-file-pdf me-2"></i>Tài liệu:
                                </label>
                                <a href="{{ $question->pdf_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Xem tài liệu
                                </a>
                            </div>
                        @endif

                        @if($question->image_url)
                            <div class="media-container">
                                <img src="{{ $question->image_url }}" alt="Question Image" class="img-fluid rounded">
                            </div>
                        @endif

                        <!-- Answer Display -->
                        @if($isEssay)
                            <div class="answer-essay">
                                <strong><i class="bi bi-pencil me-2"></i>Câu trả lời của bạn:</strong>
                                <p class="mt-2 mb-0">{{ $answer->answer_text }}</p>
                            </div>

                            @if($isGraded && $answer->teacher_feedback)
                                <div class="teacher-feedback">
                                    <strong>
                                        <i class="bi bi-chat-square-text me-2"></i>
                                        Nhận xét của giảng viên:
                                    </strong>
                                    <p class="mt-2 mb-0">{{ $answer->teacher_feedback }}</p>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-calendar me-1"></i>
                                        Chấm lúc: {{ $answer->graded_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                            @endif
                        @else
                            <div class="{{ $answer->is_correct ? 'answer-correct' : 'answer-incorrect' }}">
                                <strong>
                                    <i class="bi bi-person me-2"></i>Câu trả lời của bạn:
                                </strong>
                                <span class="ms-2">{{ $answer->answer_choice }}. {{ $question->{'phuongAn' . $answer->answer_choice} }}</span>
                            </div>

                            @if(!$answer->is_correct)
                                <div class="answer-correct">
                                    <strong>
                                        <i class="bi bi-check-circle me-2"></i>Đáp án đúng:
                                    </strong>
                                    <span class="ms-2">{{ $question->dapAnDung }}. {{ $question->{'phuongAn' . $question->dapAnDung} }}</span>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Action Buttons -->
        <div class="row mt-5 g-3">
            <div class="col-md-6">
                <a href="{{ route('student.courses.show', $result->miniTest->chapter->course->slug) }}" 
                   class="btn btn-back action-btn w-100">
                    <i class="bi bi-arrow-left-circle fs-5"></i>
                    <span>Quay lại khóa học</span>
                </a>
            </div>
            <div class="col-md-6">
                <a href="{{ route('student.minitests.show', $result->miniTest->maMT) }}" 
                   class="btn btn-retry action-btn w-100">
                    <i class="bi bi-arrow-repeat fs-5"></i>
                    <span>Làm lại bài test</span>
                </a>
            </div>
        </div>

        <!-- Performance Note -->
        @if($result->is_fully_graded)
            @php
                $percentage = ($result->diem / $result->miniTest->max_score) * 100;
                $isExcellent = $percentage >= 70;
            @endphp
            <div class="alert border-0 shadow-sm mt-4" style="background: linear-gradient(135deg, {{ $isExcellent ? '#d4edda' : '#fff3cd' }} 0%, {{ $isExcellent ? '#c3e6cb' : '#ffe8a1' }} 100%); border-radius: 16px; padding: 25px;">
                <div class="d-flex align-items-center gap-4">
                    <div style="width: 70px; height: 70px; background: {{ $isExcellent ? 'linear-gradient(135deg, #28a745, #20c997)' : 'linear-gradient(135deg, #ffc107, #ff9800)' }}; border-radius: 16px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-{{ $isExcellent ? 'trophy' : 'lightbulb' }} text-white" style="font-size: 32px;"></i>
                    </div>
                    <div class="flex-grow-1">
                        @if($isExcellent)
                            <h5 class="mb-2 text-success" style="font-weight: 700;">🎉 Xuất sắc!</h5>
                            <p class="mb-0" style="color: #155724; font-size: 16px;">
                                Bạn đã đạt <strong>{{ number_format($percentage, 1) }}%</strong>. Thành tích tuyệt vời! Tiếp tục phát huy nhé! 💪
                            </p>
                        @else
                            <h5 class="mb-2 text-warning" style="font-weight: 700;">💡 Cần cố gắng thêm</h5>
                            <p class="mb-0" style="color: #856404; font-size: 16px;">
                                Bạn đạt <strong>{{ number_format($percentage, 1) }}%</strong>. Hãy xem lại bài học và thử lại. Bạn sẽ làm tốt hơn! 🚀
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
