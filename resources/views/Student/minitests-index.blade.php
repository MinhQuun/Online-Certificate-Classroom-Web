@extends('layouts.student')

@section('title', 'Mini-Tests - ' . $chapter->tenChuong)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/Student/minitests-index.css') }}">
@endpush

@section('content')
    <!-- Chapter Header -->
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
            <!-- Mini-Tests List -->
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

        <!-- Back Button -->
        <div class="text-center mt-4">
            <a href="{{ route('student.courses.show', $chapter->course->slug) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i> Quay lại khóa học
            </a>
        </div>
    </div>
@endsection
