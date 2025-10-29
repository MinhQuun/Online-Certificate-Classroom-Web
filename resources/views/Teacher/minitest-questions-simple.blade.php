@extends('layouts.teacher')

@section('title', 'Quản lý câu hỏi - ' . $miniTest->title)

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 16px;
        margin-bottom: 30px;
    }
    .skill-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
    }
    .skill-LISTENING { background: #e3f2fd; color: #1976d2; }
    .skill-SPEAKING { background: #f3e5f5; color: #7b1fa2; }
    .skill-READING { background: #e8f5e9; color: #388e3c; }
    .skill-WRITING { background: #fff3e0; color: #f57c00; }
    .question-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #4285f4;
    }
    .question-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .question-number {
        font-size: 18px;
        font-weight: bold;
        color: #4285f4;
    }
    .answer-option {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
    }
    .answer-option input[type="radio"] {
        width: 20px;
        height: 20px;
    }
    .answer-option input[type="text"] {
        flex: 1;
    }
    .media-upload-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin: 15px 0;
    }
    .upload-btn {
        margin-right: 10px;
        margin-bottom: 10px;
    }
    .info-box {
        background: #e7f3ff;
        border-left: 4px solid #4285f4;
        padding: 15px;
        border-radius: 8px;
        margin: 20px 0;
    }
    .sticky-sidebar {
        position: sticky;
        top: 20px;
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="page-header">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h3 class="mb-2">
                    <i class="bi bi-list-check me-2"></i>{{ $miniTest->title }}
                </h3>
                <p class="mb-0 opacity-90">{{ $miniTest->course->tenKH }} - {{ $miniTest->chapter->tenChuong }}</p>
            </div>
            <span class="skill-badge skill-{{ $miniTest->skill_type }}">
                @php
                    $skillIcons = ['LISTENING' => '🎧', 'SPEAKING' => '🗣️', 'READING' => '📖', 'WRITING' => '✍️'];
                    $skillNames = ['LISTENING' => 'Nghe', 'SPEAKING' => 'Nói', 'READING' => 'Đọc', 'WRITING' => 'Viết'];
                @endphp
                {{ $skillIcons[$miniTest->skill_type] ?? '' }} {{ $skillNames[$miniTest->skill_type] ?? $miniTest->skill_type }}
            </span>
        </div>
        <a href="{{ route('teacher.minitests.index', ['course' => $miniTest->maKH]) }}" class="btn btn-light">
            <i class="bi bi-arrow-left me-2"></i>Quay lại danh sách
        </a>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            @if($miniTest->skill_type === 'WRITING')
                <div class="info-box">
                    <h6><i class="bi bi-info-circle me-2"></i>Lưu ý với kỹ năng Viết</h6>
                    <p class="mb-0">
                        - Chỉ tạo câu hỏi tự luận (essay)<br>
                        - KHÔNG cần nhập đáp án A, B, C, D<br>
                        - Học viên sẽ viết câu trả lời tự do<br>
                        - Giảng viên chấm điểm thủ công sau
                    </p>
                </div>
            @endif

            <form id="questionsForm" action="{{ route('teacher.minitests.questions.store', $miniTest->maMT) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div id="questionsContainer">
                    <!-- Existing questions -->
                    @foreach($miniTest->questions as $index => $question)
                        <div class="question-card" data-index="{{ $index }}">
                            <div class="question-header">
                                <span class="question-number">Câu {{ $index + 1 }}</span>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-question" 
                                        onclick="deleteQuestion(this)">
                                    <i class="bi bi-trash"></i> Xóa
                                </button>
                            </div>

                            <input type="hidden" name="questions[{{ $index }}][question_type]" 
                                   value="{{ $miniTest->skill_type === 'WRITING' ? 'essay' : 'single_choice' }}">

                            <div class="mb-3">
                                <label class="form-label fw-bold">Nội dung câu hỏi <span class="text-danger">*</span></label>
                                <textarea name="questions[{{ $index }}][question_text]" 
                                          class="form-control" rows="3" 
                                          placeholder="Nhập nội dung câu hỏi..." required>{{ $question->noiDungCauHoi }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Điểm</label>
                                <input type="number" name="questions[{{ $index }}][points]" 
                                       class="form-control" value="{{ $question->diem }}" 
                                       step="0.5" min="0" required style="width: 150px;">
                            </div>

                            <!-- Media uploads -->
                            <div class="media-upload-section">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-paperclip me-1"></i>Tệp đính kèm
                                </label>
                                <div>
                                    @if(in_array($miniTest->skill_type, ['LISTENING', 'SPEAKING']))
                                        <label class="btn btn-outline-primary btn-sm upload-btn">
                                            <i class="bi bi-music-note me-1"></i> Upload Audio
                                            <input type="file" name="questions[{{ $index }}][audio]" 
                                                   accept="audio/*" class="d-none" onchange="previewFile(this, 'audio')">
                                        </label>
                                        @if($question->audio_url)
                                            <div class="mt-2">
                                                <audio controls src="{{ $question->audio_url }}" style="max-width: 100%;"></audio>
                                            </div>
                                        @endif
                                    @endif

                                    @if($miniTest->skill_type === 'READING')
                                        <label class="btn btn-outline-danger btn-sm upload-btn">
                                            <i class="bi bi-file-pdf me-1"></i> Upload PDF
                                            <input type="file" name="questions[{{ $index }}][pdf]" 
                                                   accept=".pdf" class="d-none" onchange="previewFile(this, 'pdf')">
                                        </label>
                                        @if($question->pdf_url)
                                            <div class="mt-2">
                                                <a href="{{ $question->pdf_url }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye me-1"></i> Xem PDF hiện tại
                                                </a>
                                            </div>
                                        @endif
                                    @endif

                                    <label class="btn btn-outline-success btn-sm upload-btn">
                                        <i class="bi bi-image me-1"></i> Upload Hình ảnh
                                        <input type="file" name="questions[{{ $index }}][image]" 
                                               accept="image/*" class="d-none" onchange="previewFile(this, 'image')">
                                    </label>
                                    @if($question->image_url)
                                        <div class="mt-2">
                                            <img src="{{ $question->image_url }}" alt="Image" style="max-width: 300px; border-radius: 8px;">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Answer options (not for WRITING skill) -->
                            @if($miniTest->skill_type !== 'WRITING')
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Các đáp án <span class="text-danger">*</span></label>
                                    @foreach(['A', 'B', 'C', 'D'] as $letter)
                                        <div class="answer-option">
                                            <input type="radio" 
                                                   name="questions[{{ $index }}][correct_answer]" 
                                                   value="{{ $letter }}"
                                                   {{ $question->dapAnDung === $letter ? 'checked' : '' }} 
                                                   required>
                                            <strong>{{ $letter }}.</strong>
                                            <input type="text" 
                                                   name="questions[{{ $index }}][option_{{ strtolower($letter) }}]" 
                                                   class="form-control" 
                                                   placeholder="Nhập đáp án {{ $letter }}"
                                                   value="{{ $question->{'phuongAn' . $letter} }}" 
                                                   required>
                                        </div>
                                    @endforeach
                                    <small class="text-muted">Chọn đáp án đúng bằng cách click vào radio button</small>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Add Question Button -->
                <div class="text-center my-4">
                    <button type="button" class="btn btn-outline-primary btn-lg" onclick="addQuestion()">
                        <i class="bi bi-plus-circle me-2"></i> Thêm câu hỏi
                    </button>
                </div>

                <!-- Submit Button -->
                <div class="text-center my-4">
                    <button type="submit" class="btn btn-success btn-lg" id="saveBtn">
                        <i class="bi bi-check-circle me-2"></i> Lưu tất cả câu hỏi
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="sticky-sidebar">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-info-circle me-2"></i>Thông tin
                        </h5>
                        
                        <div class="mb-3">
                            <strong>Số câu hỏi:</strong>
                            <span class="badge bg-primary ms-2" id="questionCount">{{ $miniTest->questions->count() }}</span>
                        </div>

                        <div class="mb-3">
                            <strong>Điểm tối đa:</strong>
                            <span class="badge bg-success ms-2">{{ $miniTest->max_score }}</span>
                        </div>

                        <div class="mb-3">
                            <strong>Thời gian:</strong>
                            <span class="badge bg-info ms-2">{{ $miniTest->time_limit_min }} phút</span>
                        </div>

                        <hr>

                        @if($miniTest->skill_type === 'LISTENING')
                            <div class="alert alert-primary small">
                                <strong>🎧 Kỹ năng Nghe</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Upload file audio</li>
                                    <li>Tạo câu hỏi trắc nghiệm</li>
                                    <li>Tự động chấm điểm</li>
                                </ul>
                            </div>
                        @elseif($miniTest->skill_type === 'SPEAKING')
                            <div class="alert alert-primary small">
                                <strong>🗣️ Kỹ năng Nói</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Upload file audio mẫu</li>
                                    <li>Tạo câu hỏi trắc nghiệm</li>
                                    <li>Tự động chấm điểm</li>
                                </ul>
                            </div>
                        @elseif($miniTest->skill_type === 'READING')
                            <div class="alert alert-primary small">
                                <strong>📖 Kỹ năng Đọc</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Upload PDF hoặc hình ảnh</li>
                                    <li>Tạo câu hỏi trắc nghiệm</li>
                                    <li>Tự động chấm điểm</li>
                                </ul>
                            </div>
                        @else
                            <div class="alert alert-warning small">
                                <strong>✍️ Kỹ năng Viết</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Câu hỏi tự luận</li>
                                    <li>Không cần đáp án A,B,C,D</li>
                                    <li>Giảng viên chấm thủ công</li>
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const skillType = '{{ $miniTest->skill_type }}';
    const isWriting = skillType === 'WRITING';
    let questionIndex = {{ $miniTest->questions->count() }};

    // Add new question
    function addQuestion() {
        const container = document.getElementById('questionsContainer');
        const questionHtml = createQuestionHTML(questionIndex);
        container.insertAdjacentHTML('beforeend', questionHtml);
        questionIndex++;
        updateQuestionCount();
        updateQuestionNumbers();
    }

    // Create question HTML
    function createQuestionHTML(index) {
        const questionType = isWriting ? 'essay' : 'single_choice';
        let answersHTML = '';
        
        if (!isWriting) {
            ['A', 'B', 'C', 'D'].forEach((letter, i) => {
                answersHTML += `
                    <div class="answer-option">
                        <input type="radio" name="questions[${index}][correct_answer]" value="${letter}" ${i === 0 ? 'checked' : ''} required>
                        <strong>${letter}.</strong>
                        <input type="text" name="questions[${index}][option_${letter.toLowerCase()}]" 
                               class="form-control" placeholder="Nhập đáp án ${letter}" required>
                    </div>
                `;
            });
        }

        let mediaHTML = '<div class="media-upload-section"><label class="form-label fw-bold"><i class="bi bi-paperclip me-1"></i>Tệp đính kèm</label><div>';
        
        if (['LISTENING', 'SPEAKING'].includes(skillType)) {
            mediaHTML += `
                <label class="btn btn-outline-primary btn-sm upload-btn">
                    <i class="bi bi-music-note me-1"></i> Upload Audio
                    <input type="file" name="questions[${index}][audio]" accept="audio/*" class="d-none" onchange="previewFile(this, 'audio')">
                </label>
            `;
        }
        
        if (skillType === 'READING') {
            mediaHTML += `
                <label class="btn btn-outline-danger btn-sm upload-btn">
                    <i class="bi bi-file-pdf me-1"></i> Upload PDF
                    <input type="file" name="questions[${index}][pdf]" accept=".pdf" class="d-none" onchange="previewFile(this, 'pdf')">
                </label>
            `;
        }
        
        mediaHTML += `
            <label class="btn btn-outline-success btn-sm upload-btn">
                <i class="bi bi-image me-1"></i> Upload Hình ảnh
                <input type="file" name="questions[${index}][image]" accept="image/*" class="d-none" onchange="previewFile(this, 'image')">
            </label>
        </div></div>`;

        return `
            <div class="question-card" data-index="${index}">
                <div class="question-header">
                    <span class="question-number">Câu ${index + 1}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger delete-question" onclick="deleteQuestion(this)">
                        <i class="bi bi-trash"></i> Xóa
                    </button>
                </div>

                <input type="hidden" name="questions[${index}][question_type]" value="${questionType}">

                <div class="mb-3">
                    <label class="form-label fw-bold">Nội dung câu hỏi <span class="text-danger">*</span></label>
                    <textarea name="questions[${index}][question_text]" class="form-control" rows="3" 
                              placeholder="Nhập nội dung câu hỏi..." required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Điểm</label>
                    <input type="number" name="questions[${index}][points]" class="form-control" 
                           value="1" step="0.5" min="0" required style="width: 150px;">
                </div>

                ${mediaHTML}

                ${!isWriting ? `
                    <div class="mb-3">
                        <label class="form-label fw-bold">Các đáp án <span class="text-danger">*</span></label>
                        ${answersHTML}
                        <small class="text-muted">Chọn đáp án đúng bằng cách click vào radio button</small>
                    </div>
                ` : ''}
            </div>
        `;
    }

    // Delete question
    function deleteQuestion(btn) {
        const container = document.getElementById('questionsContainer');
        const cards = container.querySelectorAll('.question-card');
        
        if (cards.length <= 1) {
            alert('Phải có ít nhất 1 câu hỏi!');
            return;
        }
        
        if (confirm('Xác nhận xóa câu hỏi này?')) {
            btn.closest('.question-card').remove();
            reindexQuestions();
            updateQuestionNumbers();
            updateQuestionCount();
        }
    }

    // Reindex questions
    function reindexQuestions() {
        const cards = document.querySelectorAll('.question-card');
        cards.forEach((card, newIndex) => {
            card.dataset.index = newIndex;
            
            // Update all input names
            card.querySelectorAll('[name]').forEach(input => {
                const name = input.getAttribute('name');
                input.setAttribute('name', name.replace(/questions\[\d+\]/, `questions[${newIndex}]`));
            });
        });
        
        questionIndex = cards.length;
    }

    // Update question numbers
    function updateQuestionNumbers() {
        document.querySelectorAll('.question-number').forEach((span, index) => {
            span.textContent = `Câu ${index + 1}`;
        });
    }

    // Update question count
    function updateQuestionCount() {
        const count = document.querySelectorAll('.question-card').length;
        document.getElementById('questionCount').textContent = count;
    }

    // Preview file
    function previewFile(input, type) {
        const file = input.files[0];
        if (!file) return;
        
        const mediaSection = input.closest('.media-upload-section');
        const fileName = file.name;
        
        // Show file name
        let preview = mediaSection.querySelector('.file-preview');
        if (!preview) {
            preview = document.createElement('div');
            preview.className = 'file-preview mt-2 alert alert-info small';
            mediaSection.appendChild(preview);
        }
        
        preview.innerHTML = `<i class="bi bi-check-circle me-1"></i> Đã chọn: <strong>${fileName}</strong>`;
    }

    // Form submission with AJAX
    document.getElementById('questionsForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const saveBtn = document.getElementById('saveBtn');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';

        const formData = new FormData(this);

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const result = await response.json();

            if (result.success) {
                // Show success message
                showAlert('success', 'Thành công!', result.message);
                
                // Redirect after 1 second
                setTimeout(() => {
                    window.location.href = '{{ route("teacher.minitests.index", ["course" => $miniTest->maKH]) }}';
                }, 1000);
            } else {
                showAlert('danger', 'Lỗi!', result.error || 'Không thể lưu câu hỏi');
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i> Lưu tất cả câu hỏi';
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('danger', 'Lỗi!', 'Có lỗi xảy ra khi lưu câu hỏi. Vui lòng thử lại.');
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i> Lưu tất cả câu hỏi';
        }
    });

    // Show alert function
    function showAlert(type, title, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
        alertDiv.style.zIndex = '9999';
        alertDiv.style.minWidth = '300px';
        alertDiv.innerHTML = `
            <strong>${title}</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);

        // Auto remove after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
</script>
@endpush
