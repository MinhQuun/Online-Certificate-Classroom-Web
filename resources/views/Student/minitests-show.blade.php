@extends('layouts.student')

@section('title', $miniTest->title)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Student/minitests-show.css') }}">
@endpush

@section('content')
    <!-- Back Button -->
    <div class="container-fluid mb-3">
        <a href="{{ route('student.courses.show', $miniTest->chapter->course->slug) }}" 
           class="btn btn-link text-decoration-none p-0 d-inline-flex align-items-center gap-2"
           style="color: #667eea; font-weight: 600; font-size: 15px;">
            <i class="bi bi-arrow-left-circle-fill fs-5"></i>
            <span>Quay lại khóa học</span>
        </a>
    </div>

    <!-- Test Header with Timer -->
    <div class="test-header">
        <!-- Top Bar -->
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

        <!-- Main Content -->
        <div class="header-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Title Section -->
                        <div class="test-title-section">
                            <div class="title-icon">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h1 class="test-title">{{ $miniTest->title }}</h1>
                            </div>
                        </div>

                        <!-- Info Stats -->
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

                    <!-- Timer Section -->
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
                <!-- Questions Column -->
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

                                    <!-- Media Display -->
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

                                    <!-- Answer Options -->
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

                    <!-- Submit Section -->
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

                <!-- Progress Sidebar -->
                <div class="col-lg-4">
                    @if($miniTest->questions->isNotEmpty())
                    <div class="progress-sidebar">
                        <div class="progress-card">
                            <h5 class="mb-4">
                                <i class="bi bi-bar-chart-fill me-2"></i>Tiến độ làm bài
                            </h5>
                            
                            <!-- Stats -->
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

                            <!-- Progress Bar -->
                            <div class="progress-bar-custom">
                                <div class="progress-fill" id="progressBar" style="width: 0%"></div>
                            </div>
                            <div class="text-center small text-muted mt-2">
                                <span id="progressPercent">0</span>% hoàn thành
                            </div>

                            <!-- Question Navigation -->
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
@endsection

@push('scripts')
    <script>
        // Timer functionality
        const timeLimitMin = {{ $miniTest->time_limit_min }};
        let timeRemaining = timeLimitMin * 60; // seconds
        const timerDisplay = document.getElementById('timer');

        function updateTimer() {
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            timerDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

            // Color warnings
            timerDisplay.classList.remove('timer-warning', 'timer-danger');
            if (timeRemaining <= 60) {
                timerDisplay.classList.add('timer-danger');
            } else if (timeRemaining <= 300) {
                timerDisplay.classList.add('timer-warning');
            }

            if (timeRemaining <= 0) {
                clearInterval(timerInterval);
                alert('Hết giờ! Bài làm sẽ được tự động nộp.');
                document.getElementById('testForm').submit();
            }

            timeRemaining--;
        }

        const timerInterval = setInterval(updateTimer, 1000);
        updateTimer();

        // Progress tracking
        const totalQuestions = {{ $miniTest->questions->count() }};
        const questionInputs = document.querySelectorAll('.question-input');
        const answeredCount = document.getElementById('answeredCount');
        const progressBar = document.getElementById('progressBar');
        const questionNavItems = document.querySelectorAll('.question-nav-item');

        function updateProgress() {
            let answered = 0;
            const answeredQuestions = new Set();

            questionInputs.forEach(input => {
                if (input.type === 'radio' && input.checked) {
                    answeredQuestions.add(input.dataset.question);
                } else if (input.type === 'textarea' && input.value.trim() !== '') {
                    answeredQuestions.add(input.dataset.question);
                }
            });

            answered = answeredQuestions.size;
            answeredCount.textContent = answered;
            const percentage = Math.round((answered / totalQuestions) * 100);
            progressBar.style.width = percentage + '%';
            
            // Update progress percent text
            const progressPercentEl = document.getElementById('progressPercent');
            if (progressPercentEl) {
                progressPercentEl.textContent = percentage;
            }

            // Update nav items
            questionNavItems.forEach(item => {
                const questionId = item.dataset.question;
                if (answeredQuestions.has(questionId)) {
                    item.classList.add('answered');
                } else {
                    item.classList.remove('answered');
                }
            });
        }

        questionInputs.forEach(input => {
            input.addEventListener('change', updateProgress);
            if (input.type === 'textarea') {
                input.addEventListener('input', updateProgress);
            }
        });

        // Submit confirmation
        document.getElementById('submitBtn').addEventListener('click', function() {
            const answeredSet = new Set();
            questionInputs.forEach(input => {
                if (input.type === 'radio' && input.checked) {
                    answeredSet.add(input.dataset.question);
                } else if (input.type === 'textarea' && input.value.trim() !== '') {
                    answeredSet.add(input.dataset.question);
                }
            });

            const answered = answeredSet.size;
            const unanswered = totalQuestions - answered;

            let message = 'Bạn có chắc chắn muốn nộp bài?';
            if (unanswered > 0) {
                message += `\n\nBạn còn ${unanswered} câu chưa trả lời.`;
            }

            if (confirm(message)) {
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang nộp bài...';
                document.getElementById('testForm').submit();
            }
        });

        // Smooth scroll for question navigation
        document.querySelectorAll('.question-nav-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const offset = 120; // Account for sticky header
                    const targetPosition = target.offsetTop - offset;
                    window.scrollTo({ 
                        top: targetPosition, 
                        behavior: 'smooth' 
                    });
                }
            });
        });

        // Prevent accidental page leave
        window.addEventListener('beforeunload', function(e) {
            if (timeRemaining > 0) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    </script>
@endpush
