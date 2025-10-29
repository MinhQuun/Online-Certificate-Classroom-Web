@extends('layouts.teacher')

@section('title', 'Quản lý câu hỏi Mini-Test')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Teacher/minitest-questions.css') }}">
    <style>
        .question-type-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .question-type-btn {
            flex: 1;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
        }
        .question-type-btn:hover {
            border-color: #4285f4;
            background: #f8f9fa;
        }
        .question-type-btn.active {
            border-color: #4285f4;
            background: #e8f0fe;
        }
        .essay-section {
            background: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .skill-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .skill-LISTENING { background: #e3f2fd; color: #1976d2; }
        .skill-SPEAKING { background: #f3e5f5; color: #7b1fa2; }
        .skill-READING { background: #e8f5e9; color: #388e3c; }
        .skill-WRITING { background: #fff3e0; color: #f57c00; }
    </style>
@endpush

@section('content')
    <!-- Header -->
    <section class="page-header">
        <div class="d-flex align-items-center gap-3 mb-3">
            <a href="{{ route('teacher.minitests.index', ['course' => $miniTest->maKH]) }}" 
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i> Quay lại
            </a>
            <div>
                <span class="kicker">{{ $miniTest->course->tenKH }} / {{ $miniTest->chapter->tenChuong }}</span>
                <h1 class="title mb-0">{{ $miniTest->title }}</h1>
                <div class="mt-2">
                    @php
                        $skillIcons = [
                            'LISTENING' => '🎧',
                            'SPEAKING' => '🗣️',
                            'READING' => '📖',
                            'WRITING' => '✍️'
                        ];
                        $skillNames = [
                            'LISTENING' => 'Nghe (Listening)',
                            'SPEAKING' => 'Nói (Speaking)',
                            'READING' => 'Đọc (Reading)',
                            'WRITING' => 'Viết (Writing)'
                        ];
                    @endphp
                    <span class="skill-badge skill-{{ $miniTest->skill_type }}">
                        {{ $skillIcons[$miniTest->skill_type] ?? '' }} 
                        {{ $skillNames[$miniTest->skill_type] ?? $miniTest->skill_type }}
                    </span>
                </div>
            </div>
        </div>

        @if($miniTest->skill_type === 'WRITING')
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Kỹ năng Viết:</strong> Tạo câu hỏi tự luận. Học viên sẽ nhập câu trả lời và giảng viên sẽ chấm điểm thủ công.
            </div>
        @endif
    </section>

    <div class="row">
        <!-- Main Content - Questions -->
        <div class="col-lg-8">
            <form id="questionsForm" enctype="multipart/form-data">
                @csrf
                
                <!-- Questions Container -->
                <div id="questionsContainer">
                    @if($miniTest->questions->isNotEmpty())
                        @foreach($miniTest->questions as $question)
                            @include('Teacher.partials.question-card', [
                                'question' => $question,
                                'index' => $loop->index,
                                'skillType' => $miniTest->skill_type
                            ])
                        @endforeach
                    @else
                        @include('Teacher.partials.question-card-new', [
                            'index' => 0,
                            'skillType' => $miniTest->skill_type
                        ])
                    @endif
                </div>

                <!-- Add Question Button -->
                <div class="text-center my-4">
                    <button type="button" id="addQuestionBtn" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-plus-circle me-2"></i> Thêm câu hỏi
                    </button>
                </div>

                <!-- Submit Button -->
                <div class="text-center my-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-circle me-2"></i> Lưu tất cả câu hỏi
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar - Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-info-circle me-2"></i> Hướng dẫn
                    </h5>

                    @if($miniTest->skill_type === 'LISTENING')
                        <div class="alert alert-primary">
                            <strong>🎧 Kỹ năng Nghe</strong>
                            <ul class="mt-2 mb-0">
                                <li>Upload file audio cho câu hỏi</li>
                                <li>Tạo câu hỏi trắc nghiệm</li>
                                <li>Hệ thống tự động chấm điểm</li>
                            </ul>
                        </div>
                    @elseif($miniTest->skill_type === 'SPEAKING')
                        <div class="alert alert-primary">
                            <strong>🗣️ Kỹ năng Nói</strong>
                            <ul class="mt-2 mb-0">
                                <li>Upload file audio mẫu</li>
                                <li>Tạo câu hỏi trắc nghiệm</li>
                                <li>Hệ thống tự động chấm điểm</li>
                            </ul>
                        </div>
                    @elseif($miniTest->skill_type === 'READING')
                        <div class="alert alert-primary">
                            <strong>📖 Kỹ năng Đọc</strong>
                            <ul class="mt-2 mb-0">
                                <li>Upload hình ảnh hoặc PDF đoạn văn</li>
                                <li>Tạo câu hỏi trắc nghiệm</li>
                                <li>Hệ thống tự động chấm điểm</li>
                            </ul>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <strong>✍️ Kỹ năng Viết</strong>
                            <ul class="mt-2 mb-0">
                                <li>Tạo câu hỏi tự luận</li>
                                <li>KHÔNG cần nhập đáp án</li>
                                <li>Học viên sẽ viết câu trả lời</li>
                                <li>Giảng viên chấm điểm thủ công</li>
                            </ul>
                        </div>
                    @endif

                    <hr>

                    <div class="mb-3">
                        <strong>Số câu hỏi hiện tại:</strong>
                        <span class="badge bg-primary" id="questionCount">{{ $miniTest->questions->count() }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Tổng điểm:</strong>
                        <span class="badge bg-success" id="totalPoints">{{ $miniTest->questions->sum('diem') }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Điểm tối đa test:</strong>
                        <span class="badge bg-info">{{ $miniTest->max_score }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden config for JS -->
    <div id="miniTestConfig" class="d-none"
         data-minitest-id="{{ $miniTest->maMT }}"
         data-skill-type="{{ $miniTest->skill_type }}"
         data-csrf="{{ csrf_token() }}"
         data-save-route="{{ route('teacher.minitests.questions.store', $miniTest->maMT) }}">
    </div>

    @push('scripts')
        <script src="{{ asset('js/Teacher/minitest-questions.js') }}"></script>
    @endpush
@endsection
