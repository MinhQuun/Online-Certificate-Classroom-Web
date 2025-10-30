{{-- resources/views/Student/minitests.blade.php --}}

@extends('layouts.student')

@section('title')
    @if($type == 'index')
        Mini-Tests - {{ $chapter->tenChuong }}
    @elseif($type == 'show')
        {{ $miniTest->title }}
    @elseif($type == 'result')
        Kết quả - {{ $result->miniTest->title }}
    @endif
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Student/minitests.css') }}">
@endpush

@section('content')
    @if($type == 'index')
        <div class="minitests-index">
            <div class="chapter-header">
                <div class="container">
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('student.courses.index') }}" class="text-white">Khóa học</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('student.courses.show', $chapter->course->slug) }}" class="text-white">{{ $chapter->course->tenKH }}</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">{{ $chapter->tenChuong }}</li>
                        </ol>
                    </nav>
                    <h1 class="mb-2">{{ $chapter->tenChuong }}</h1>
                    <p class="mb-0 opacity-90">Mini-Tests kiểm tra kỹ năng</p>
                </div>
            </div>

            <div class="container">
                @if($miniTests->isEmpty())
                    <div class="alert alert-info border-0 shadow-sm">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-info-circle fs-3"></i>
                            <div>
                                <h5 class="mb-1">Chưa có bài kiểm tra</h5>
                                <p class="mb-0">Chương này chưa có bài mini-test nào. Vui lòng quay lại sau.</p>
                            </div>
                        </div>
                    </div>
                @else
                    @foreach($miniTests as $miniTest)
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

                            // Lấy kết quả của học viên cho minitest này
                            $studentResults = $results->get($miniTest->maMT) ?? collect();
                            $bestResult = $studentResults->sortByDesc('diem')->first();
                            $attemptsUsed = $studentResults->count();
                            $attemptsLeft = $miniTest->attempts_allowed - $attemptsUsed;
                        @endphp

                        <div class="minitest-card skill-{{ $miniTest->skill_type }}">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-start gap-3 mb-3">
                                        <div class="fs-1">{{ $skillIcons[$miniTest->skill_type] ?? '📝' }}</div>
                                        <div class="flex-grow-1">
                                            <h4 class="mb-2">{{ $miniTest->title }}</h4>
                                            <span class="skill-badge skill-{{ $miniTest->skill_type }}">
                                                {{ $skillNames[$miniTest->skill_type] ?? $miniTest->skill_type }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="row g-2 mb-3">
                                        <div class="col-6 col-md-3">
                                            <div class="test-info-item">
                                                <i class="bi bi-question-circle"></i>
                                                <span>{{ $miniTest->questions->count() }} câu hỏi</span>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="test-info-item">
                                                <i class="bi bi-clock"></i>
                                                <span>{{ $miniTest->time_limit_min }} phút</span>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="test-info-item">
                                                <i class="bi bi-trophy"></i>
                                                <span>{{ $miniTest->max_score }} điểm</span>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="test-info-item">
                                                <i class="bi bi-arrow-repeat"></i>
                                                <span>{{ $attemptsLeft }}/{{ $miniTest->attempts_allowed }} lần</span>
                                            </div>
                                        </div>
                                    </div>

                                    @if($bestResult)
                                        <div class="attempt-badge">
                                            <strong>Điểm cao nhất:</strong>
                                            <span class="badge bg-success ms-2">{{ number_format($bestResult->diem ?? 0, 2) }}/{{ $miniTest->max_score }}</span>
                                            @if(!$bestResult->is_fully_graded)
                                                <span class="badge bg-warning text-dark ms-2">
                                                    <i class="bi bi-clock"></i> Đang chấm điểm
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    @if($attemptsLeft > 0)
                                        <a href="{{ route('student.minitests.show', $miniTest->maMT) }}"
                                           class="btn btn-primary btn-lg w-100">
                                            <i class="bi bi-pencil-square me-2"></i>
                                            @if($attemptsUsed > 0)
                                                Làm lại
                                            @else
                                                Bắt đầu làm bài
                                            @endif
                                        </a>
                                    @else
                                        <button class="btn btn-secondary btn-lg w-100" disabled>
                                            <i class="bi bi-x-circle me-2"></i> Hết lượt làm bài
                                        </button>
                                    @endif

                                    @if($bestResult)
                                        <a href="{{ route('student.minitests.result', $bestResult->maKQDG) }}"
                                           class="btn btn-outline-primary w-100 mt-2">
                                            <i class="bi bi-eye me-2"></i> Xem kết quả
                                        </a>
                                    @endif
                                </div>
                            </div>

                            @if($studentResults->count() > 1)
                                <hr>
                                <div class="mt-3">
                                    <h6 class="mb-2">
                                        <i class="bi bi-clock-history me-2"></i> Lịch sử làm bài ({{ $studentResults->count() }} lần)
                                    </h6>
                                    <div class="row g-2">
                                        @foreach($studentResults as $result)
                                            <div class="col-6 col-md-3">
                                                <div class="card border">
                                                    <div class="card-body p-2 text-center">
                                                        <small class="text-muted d-block">Lần {{ $result->attempt_no }}</small>
                                                        <strong class="d-block {{ $result->is_fully_graded ? 'text-success' : 'text-warning' }}">
                                                            {{ $result->is_fully_graded ? number_format($result->diem, 2) : 'Chấm...' }}
                                                        </strong>
                                                        <small class="text-muted">{{ $result->nop_luc->format('d/m H:i') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif

                <div class="text-center mt-4">
                    <a href="{{ route('student.courses.show', $chapter->course->slug) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i> Quay lại khóa học
                    </a>
                </div>
            </div>
        </div>
    @elseif($type == 'show')
        <div class="minitests-show">
            <div class="container-fluid mb-3">
                <a href="{{ route('student.courses.show', $miniTest->chapter->course->slug) }}"
                   class="btn btn-link text-decoration-none p-0 d-inline-flex align-items-center gap-2"
                   style="color: #667eea; font-weight: 600; font-size: 15px;">
                    <i class="bi bi-arrow-left-circle-fill fs-5"></i>
                    <span>Quay lại khóa học</span>
                </a>
            </div>

            <div class="test-header">
                <div class="header-top-bar">
                    <div class="container-fluid">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="test-badge">
                                <i class="bi bi-folder2-open"></i>
                                <span>{{ $miniTest->chapter->tenChuong }}</span>
                            </div>
                            <div class="test-badge">
                                <i class="bi bi-hash"></i>
                                <span>Lần thử {{ $attemptNo }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="header-content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="test-title-section">
                                    <div class="title-icon">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h1 class="test-title">{{ $miniTest->title }}</h1>
                                    </div>
                                </div>

                                <div class="info-stats">
                                    <div class="info-item">
                                        <i class="bi bi-question-circle-fill"></i>
                                        <span>{{ $miniTest->questions->count() }} Câu hỏi</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-trophy-fill"></i>
                                        <span>{{ $miniTest->max_score }} Điểm</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-clock-history"></i>
                                        <span>{{ $miniTest->time_limit_min }} Phút</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="timer-section">
                                    <div class="timer-header">
                                        <div class="timer-icon-badge">
                                            <i class="bi bi-stopwatch-fill"></i>
                                        </div>
                                        <span class="timer-label">Thời gian làm bài</span>
                                    </div>
                                    <div class="timer-display" id="timer">{{ $miniTest->time_limit_min }}:00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <form id="testForm" action="{{ route('student.minitests.submit', $miniTest->maMT) }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-lg-8">
                            @if($miniTest->questions->isEmpty())
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    Bài kiểm tra này chưa có câu hỏi. Vui lòng liên hệ giảng viên.
                                </div>
                            @else
                            @foreach($miniTest->questions as $index => $question)
                                <div class="question-card" id="question-{{ $question->maCauHoi }}">
                                    <div class="d-flex align-items-start">
                                        <span class="question-number">{{ $index + 1 }}</span>
                                        <div class="flex-grow-1">
                                            <div class="question-text">
                                                {!! nl2br(e($question->noiDungCauHoi)) !!}
                                                <span class="badge-points ms-2">{{ $question->diem }} điểm</span>
                                            </div>

                                            @if($question->audio_url)
                                                <div class="media-container">
                                                    <label class="form-label fw-bold mb-3">
                                                        <i class="bi bi-volume-up fs-5 me-2"></i>Nghe audio:
                                                    </label>
                                                    <audio controls class="audio-player" controlsList="nodownload">
                                                        <source src="{{ $question->audio_url }}" type="audio/mpeg">
                                                        Trình duyệt của bạn không hỗ trợ audio.
                                                    </audio>
                                                </div>
                                            @endif

                                            @if($question->pdf_url)
                                                <div class="media-container">
                                                    <label class="form-label fw-bold mb-3">
                                                        <i class="bi bi-file-pdf fs-5 me-2"></i>Đọc tài liệu:
                                                    </label>
                                                    <div class="d-flex gap-2 mb-3">
                                                        <a href="{{ $question->pdf_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-box-arrow-up-right me-1"></i>Mở trong tab mới
                                                        </a>
                                                    </div>
                                                    <iframe src="{{ $question->pdf_url }}" class="pdf-viewer"></iframe>
                                                </div>
                                            @endif

                                            @if($question->image_url)
                                                <div class="media-container">
                                                    <label class="form-label fw-bold mb-3">
                                                        <i class="bi bi-image fs-5 me-2"></i>Hình ảnh:
                                                    </label>
                                                    <img src="{{ $question->image_url }}" alt="Question Image" class="img-fluid rounded">
                                                </div>
                                            @endif

                                            @if($question->loai === 'essay')
                                                <div class="mt-4">
                                                    <label class="form-label fw-bold">
                                                        <i class="bi bi-pencil fs-5 me-2"></i>Câu trả lời của bạn:
                                                    </label>
                                                    <textarea
                                                        name="answers[{{ $question->maCauHoi }}]"
                                                        class="essay-textarea question-input"
                                                        data-question="{{ $question->maCauHoi }}"
                                                        placeholder="Nhập câu trả lời của bạn tại đây... (Tối thiểu 50 từ)"
                                                        required></textarea>
                                                    <small class="text-muted">
                                                        <i class="bi bi-info-circle me-1"></i>
                                                        Câu hỏi tự luận sẽ được giảng viên chấm điểm.
                                                    </small>
                                                </div>
                                            @else
                                                <div class="mt-4">
                                                    <label class="form-label fw-bold mb-3">Chọn đáp án:</label>
                                                    @foreach(['A', 'B', 'C', 'D'] as $option)
                                                        @php
                                                            $optionField = 'phuongAn' . $option;
                                                            $optionText = $question->$optionField;
                                                        @endphp
                                                        @if($optionText)
                                                            <label class="option-label">
                                                                <input
                                                                    type="radio"
                                                                    name="answers[{{ $question->maCauHoi }}]"
                                                                    value="{{ $option }}"
                                                                    class="question-input"
                                                                    data-question="{{ $question->maCauHoi }}"
                                                                    required>
                                                                <div class="option-content">
                                                                    <strong>{{ $option }}.</strong> {{ $optionText }}
                                                                </div>
                                                            </label>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            @endif

                            @if($miniTest->questions->isNotEmpty())
                            <div class="submit-section">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="mb-2">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>Hoàn thành bài thi?
                                        </h5>
                                        <p class="mb-0 text-muted">
                                            Hãy kiểm tra kỹ các câu trả lời trước khi nộp bài. Bạn có thể làm lại bài test nhiều lần.
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                        <button type="button" class="btn btn-submit w-100" id="submitBtn">
                                            <i class="bi bi-send-fill me-2"></i>Nộp bài
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="col-lg-4">
                            @if($miniTest->questions->isNotEmpty())
                            <div class="progress-sidebar">
                                <div class="progress-card">
                                    <h5 class="mb-4">
                                        <i class="bi bi-bar-chart-fill me-2"></i>Tiến độ làm bài
                                    </h5>

                                    <div class="row g-3 mb-4">
                                        <div class="col-6">
                                            <div class="stat-box">
                                                <div class="stat-value" id="answeredCount">0</div>
                                                <div class="stat-label">Đã trả lời</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stat-box">
                                                <div class="stat-value">{{ $miniTest->questions->count() }}</div>
                                                <div class="stat-label">Tổng câu hỏi</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="progress-bar-custom">
                                        <div class="progress-fill" id="progressBar" style="width: 0%"></div>
                                    </div>
                                    <div class="text-center small text-muted mt-2">
                                        <span id="progressPercent">0</span>% hoàn thành
                                    </div>

                                    <hr class="my-4">
                                    <h6 class="mb-3">Điều hướng nhanh</h6>
                                    <div class="question-nav-grid" id="questionNav">
                                        @foreach($miniTest->questions as $index => $question)
                                            <a href="#question-{{ $question->maCauHoi }}"
                                               class="question-nav-item"
                                               data-question="{{ $question->maCauHoi }}">
                                                {{ $index + 1 }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @elseif($type == 'result')
        <div class="minitests-result">
            <div class="container mb-3">
                <a href="{{ route('student.courses.show', $result->miniTest->chapter->course->slug) }}"
                   class="btn btn-link text-decoration-none p-0 d-inline-flex align-items-center gap-2"
                   style="color: #667eea; font-weight: 600;">
                    <i class="bi bi-arrow-left-circle fs-5"></i>
                    <span>Quay lại khóa học</span>
                </a>
            </div>

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
        </div>
    @endif
@endsection

@push('scripts')
    @if($type == 'show')
        <script src="{{ asset('js/Student/minitests.js') }}"></script>

        <script>
            // Gọi hàm init từ file minitests.js và truyền các biến Blade vào
            // Chỉ gọi khi có câu hỏi
            @if($miniTest->questions->isNotEmpty())
                initMiniTest(
                    {{ $miniTest->time_limit_min }},
                    {{ $miniTest->questions->count() }}
                );
            @endif
        </script>
    @endif
@endpush
