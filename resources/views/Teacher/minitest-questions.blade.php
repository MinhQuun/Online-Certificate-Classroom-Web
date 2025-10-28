@extends('layouts.teacher')

@section('title', 'Tạo câu hỏi Mini-Test')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Teacher/minitest-questions.css') }}">
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
                <span class="kicker">{{ $miniTest->course->tenKH }}</span>
                <h1 class="title mb-0">{{ $miniTest->title }}</h1>
                <p class="text-muted mb-0">{{ $miniTest->chapter->tenChuong }}</p>
            </div>
        </div>
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
                            <div class="question-card" data-question-index="{{ $loop->index }}">
                                <div class="question-header">
                                    <span class="question-number">Câu {{ $loop->iteration }}</span>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-question-btn">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <div class="question-body">
                                    <div class="form-group-gform">
                                        <label class="form-label-gform">Câu hỏi *</label>
                                        <textarea name="questions[{{ $loop->index }}][question_text]" 
                                                  class="form-control-gform" 
                                                  rows="3" 
                                                  placeholder="Nhập nội dung câu hỏi..." 
                                                  required>{{ $question->noiDung }}</textarea>
                                    </div>

                                    <!-- Media Section -->
                                    <div class="media-section">
                                        @if($question->image_url)
                                            <div class="media-preview">
                                                <img src="{{ $question->image_url }}" alt="Question Image">
                                            </div>
                                        @endif
                                        @if($question->audio_url)
                                            <div class="media-preview">
                                                <audio controls src="{{ $question->audio_url }}"></audio>
                                            </div>
                                        @endif

                                        <div class="media-buttons">
                                            <label class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-image me-1"></i> Thêm hình ảnh
                                                <input type="file" name="questions[{{ $loop->index }}][image]" 
                                                       accept="image/*" class="d-none image-input">
                                            </label>
                                            <label class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-music-note me-1"></i> Thêm audio
                                                <input type="file" name="questions[{{ $loop->index }}][audio]" 
                                                       accept="audio/*" class="d-none audio-input">
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Answers -->
                                    <div class="answers-section">
                                        <label class="form-label-gform">Đáp án *</label>
                                        @foreach($question->answers as $answer)
                                            <div class="answer-item">
                                                <input type="radio" 
                                                       name="questions[{{ $loop->parent->index }}][correct_answer]" 
                                                       value="{{ $answer->thuTu }}" 
                                                       id="q{{ $loop->parent->index }}_{{ $answer->thuTu }}"
                                                       {{ $answer->isDung ? 'checked' : '' }} 
                                                       required>
                                                <label for="q{{ $loop->parent->index }}_{{ $answer->thuTu }}" class="answer-label">
                                                    {{ $answer->thuTu }}
                                                </label>
                                                <input type="hidden" 
                                                       name="questions[{{ $loop->parent->index }}][answers][{{ $loop->index }}][order]" 
                                                       value="{{ $answer->thuTu }}">
                                                <input type="text" 
                                                       name="questions[{{ $loop->parent->index }}][answers][{{ $loop->index }}][text]" 
                                                       class="form-control-gform" 
                                                       placeholder="Nhập đáp án {{ $answer->thuTu }}"
                                                       value="{{ $answer->noiDung }}" 
                                                       required>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Points -->
                                    <div class="form-group-gform mt-3">
                                        <label class="form-label-gform">Điểm</label>
                                        <input type="number" 
                                               name="questions[{{ $loop->index }}][points]" 
                                               class="form-control-gform" 
                                               value="{{ $question->diem }}" 
                                               step="0.5" 
                                               min="0" 
                                               placeholder="1.0">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Default First Question -->
                        <div class="question-card" data-question-index="0">
                            <div class="question-header">
                                <span class="question-number">Câu 1</span>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-question-btn">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            <div class="question-body">
                                <div class="form-group-gform">
                                    <label class="form-label-gform">Câu hỏi *</label>
                                    <textarea name="questions[0][question_text]" 
                                              class="form-control-gform" 
                                              rows="3" 
                                              placeholder="Nhập nội dung câu hỏi..." 
                                              required></textarea>
                                </div>

                                <!-- Media Section -->
                                <div class="media-section">
                                    <div class="media-buttons">
                                        <label class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-image me-1"></i> Thêm hình ảnh
                                            <input type="file" name="questions[0][image]" 
                                                   accept="image/*" class="d-none image-input">
                                        </label>
                                        <label class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-music-note me-1"></i> Thêm audio
                                            <input type="file" name="questions[0][audio]" 
                                                   accept="audio/*" class="d-none audio-input">
                                        </label>
                                    </div>
                                </div>

                                <!-- Answers -->
                                <div class="answers-section">
                                    <label class="form-label-gform">Đáp án *</label>
                                    @foreach(['A', 'B', 'C', 'D'] as $index => $letter)
                                        <div class="answer-item">
                                            <input type="radio" 
                                                   name="questions[0][correct_answer]" 
                                                   value="{{ $letter }}" 
                                                   id="q0_{{ $letter }}"
                                                   {{ $index === 0 ? 'checked' : '' }} 
                                                   required>
                                            <label for="q0_{{ $letter }}" class="answer-label">{{ $letter }}</label>
                                            <input type="hidden" 
                                                   name="questions[0][answers][{{ $index }}][order]" 
                                                   value="{{ $letter }}">
                                            <input type="text" 
                                                   name="questions[0][answers][{{ $index }}][text]" 
                                                   class="form-control-gform" 
                                                   placeholder="Nhập đáp án {{ $letter }}" 
                                                   required>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Points -->
                                <div class="form-group-gform mt-3">
                                    <label class="form-label-gform">Điểm</label>
                                    <input type="number" 
                                           name="questions[0][points]" 
                                           class="form-control-gform" 
                                           value="1.0" 
                                           step="0.5" 
                                           min="0" 
                                           placeholder="1.0">
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Add Question Button -->
                <div class="text-center my-4">
                    <button type="button" id="addQuestionBtn" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-plus-circle me-2"></i> Thêm câu hỏi
                    </button>
                </div>

                <!-- Submit Button -->
                <div class="sticky-footer">
                    <div class="container-lg">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                <span id="questionCount">{{ $miniTest->questions->count() ?: 1 }}</span> câu hỏi
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="bi bi-check-circle me-2"></i> Lưu tất cả câu hỏi
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar - Info -->
        <div class="col-lg-4">
            <div class="info-card sticky-top">
                <h5><i class="bi bi-info-circle me-2"></i> Hướng dẫn</h5>
                <ul class="info-list">
                    <li><i class="bi bi-check2 text-success"></i> Mỗi câu hỏi phải có từ 2-4 đáp án</li>
                    <li><i class="bi bi-check2 text-success"></i> Chọn đáp án đúng bằng cách click vào radio button</li>
                    <li><i class="bi bi-check2 text-success"></i> Có thể thêm hình ảnh hoặc audio cho câu hỏi</li>
                    <li><i class="bi bi-check2 text-success"></i> Điểm mặc định cho mỗi câu là 1.0</li>
                </ul>

                <hr>

                <h6 class="text-muted">Thông tin Mini-Test</h6>
                <div class="mini-info">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Thời gian:</span>
                        <strong>{{ $miniTest->time_limit_min }} phút</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Điểm tối đa:</span>
                        <strong>{{ $miniTest->max_score }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Số lần thử:</span>
                        <strong>{{ $miniTest->attempts_allowed }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Trạng thái:</span>
                        <strong>
                            @if($miniTest->is_active)
                                <span class="badge bg-success">Đang hoạt động</span>
                            @else
                                <span class="badge bg-secondary">Đã tắt</span>
                            @endif
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Question Template (Hidden) -->
    <template id="questionTemplate">
        <div class="question-card" data-question-index="__INDEX__">
            <div class="question-header">
                <span class="question-number">Câu __NUMBER__</span>
                <button type="button" class="btn btn-sm btn-outline-danger delete-question-btn">
                    <i class="bi bi-trash"></i>
                </button>
            </div>

            <div class="question-body">
                <div class="form-group-gform">
                    <label class="form-label-gform">Câu hỏi *</label>
                    <textarea name="questions[__INDEX__][question_text]" 
                              class="form-control-gform" 
                              rows="3" 
                              placeholder="Nhập nội dung câu hỏi..." 
                              required></textarea>
                </div>

                <!-- Media Section -->
                <div class="media-section">
                    <div class="media-buttons">
                        <label class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-image me-1"></i> Thêm hình ảnh
                            <input type="file" name="questions[__INDEX__][image]" 
                                   accept="image/*" class="d-none image-input">
                        </label>
                        <label class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-music-note me-1"></i> Thêm audio
                            <input type="file" name="questions[__INDEX__][audio]" 
                                   accept="audio/*" class="d-none audio-input">
                        </label>
                    </div>
                </div>

                <!-- Answers -->
                <div class="answers-section">
                    <label class="form-label-gform">Đáp án *</label>
                    __ANSWERS__
                </div>

                <!-- Points -->
                <div class="form-group-gform mt-3">
                    <label class="form-label-gform">Điểm</label>
                    <input type="number" 
                           name="questions[__INDEX__][points]" 
                           class="form-control-gform" 
                           value="1.0" 
                           step="0.5" 
                           min="0" 
                           placeholder="1.0">
                </div>
            </div>
        </div>
    </template>

    @push('scripts')
        <script src="{{ asset('js/Teacher/minitest-questions.js') }}"></script>
    @endpush
@endsection
