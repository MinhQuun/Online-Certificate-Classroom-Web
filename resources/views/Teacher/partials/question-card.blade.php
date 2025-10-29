{{-- Partial: Existing Question Card --}}
<div class="question-card" data-question-index="{{ $index }}">
    <div class="question-header">
        <span class="question-number">Câu {{ $index + 1 }}</span>
        <span class="badge bg-info">{{ ucfirst($question->loai) }}</span>
        <button type="button" class="btn btn-sm btn-outline-danger delete-question-btn">
            <i class="bi bi-trash"></i>
        </button>
    </div>

    <div class="question-body">
        <!-- Question Type -->
        @if($skillType !== 'WRITING')
            <div class="form-group-gform mb-3">
                <label class="form-label-gform">Loại câu hỏi</label>
                <select name="questions[{{ $index }}][question_type]" class="form-control-gform question-type-select">
                    <option value="single_choice" {{ $question->loai === 'single_choice' ? 'selected' : '' }}>Một đáp án đúng</option>
                    <option value="multiple_choice" {{ $question->loai === 'multiple_choice' ? 'selected' : '' }}>Nhiều đáp án đúng</option>
                    <option value="true_false" {{ $question->loai === 'true_false' ? 'selected' : '' }}>Đúng/Sai</option>
                </select>
            </div>
        @else
            <input type="hidden" name="questions[{{ $index }}][question_type]" value="essay">
        @endif

        <!-- Question Text -->
        <div class="form-group-gform">
            <label class="form-label-gform">Câu hỏi *</label>
            <textarea name="questions[{{ $index }}][question_text]" 
                      class="form-control-gform" 
                      rows="3" 
                      placeholder="Nhập nội dung câu hỏi..." 
                      required>{{ $question->noiDungCauHoi }}</textarea>
        </div>

        <!-- Media Section -->
        <div class="media-section">
            <div class="media-preview-container">
                @if($question->audio_url)
                    <div class="media-preview">
                        <audio controls src="{{ $question->audio_url }}" class="w-100"></audio>
                        <button type="button" class="btn btn-sm btn-danger mt-1 remove-media" data-type="audio">
                            <i class="bi bi-x"></i> Xóa audio
                        </button>
                    </div>
                @endif
                @if($question->pdf_url)
                    <div class="media-preview">
                        <a href="{{ $question->pdf_url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-file-pdf"></i> Xem PDF
                        </a>
                        <button type="button" class="btn btn-sm btn-danger mt-1 remove-media" data-type="pdf">
                            <i class="bi bi-x"></i> Xóa PDF
                        </button>
                    </div>
                @endif
                @if($question->image_url)
                    <div class="media-preview">
                        <img src="{{ $question->image_url }}" alt="Question Image" style="max-width: 100%; max-height: 200px;">
                        <button type="button" class="btn btn-sm btn-danger mt-1 remove-media" data-type="image">
                            <i class="bi bi-x"></i> Xóa hình
                        </button>
                    </div>
                @endif
            </div>
            <div class="media-buttons">
                @if(in_array($skillType, ['LISTENING', 'SPEAKING']))
                    <label class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-music-note me-1"></i> {{ $question->audio_url ? 'Đổi' : 'Thêm' }} Audio
                        <input type="file" name="questions[{{ $index }}][audio]" 
                               accept="audio/mp3,audio/mpeg,audio/wav" class="d-none audio-input">
                    </label>
                @endif
                
                @if($skillType === 'READING')
                    <label class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-file-pdf me-1"></i> {{ $question->pdf_url ? 'Đổi' : 'Thêm' }} PDF
                        <input type="file" name="questions[{{ $index }}][pdf]" 
                               accept="application/pdf" class="d-none pdf-input">
                    </label>
                @endif

                <label class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-image me-1"></i> {{ $question->image_url ? 'Đổi' : 'Thêm' }} Hình ảnh
                    <input type="file" name="questions[{{ $index }}][image]" 
                           accept="image/*" class="d-none image-input">
                </label>
            </div>
        </div>

        <!-- Answers Section (for non-essay questions) -->
        @if($skillType !== 'WRITING' && $question->loai !== 'essay')
            <div class="answers-section">
                <label class="form-label-gform">Đáp án *</label>
                <div class="answers-container">
                    @foreach(['A' => 'phuongAnA', 'B' => 'phuongAnB', 'C' => 'phuongAnC', 'D' => 'phuongAnD'] as $letter => $field)
                        <div class="answer-item">
                            <input type="radio" 
                                   name="questions[{{ $index }}][correct_answer]" 
                                   value="{{ $letter }}" 
                                   id="q{{ $index }}_{{ $letter }}"
                                   {{ $question->dapAnDung === $letter ? 'checked' : '' }} 
                                   required>
                            <label for="q{{ $index }}_{{ $letter }}" class="answer-label">{{ $letter }}</label>
                            <input type="text" 
                                   name="questions[{{ $index }}][option_{{ strtolower($letter) }}]" 
                                   class="form-control-gform" 
                                   placeholder="Nhập đáp án {{ $letter }}" 
                                   value="{{ $question->$field }}"
                                   required>
                        </div>
                    @endforeach
                </div>

                <!-- Explanation (optional) -->
                <div class="form-group-gform mt-3">
                    <label class="form-label-gform">Giải thích (tùy chọn)</label>
                    <textarea name="questions[{{ $index }}][explanation]" 
                              class="form-control-gform" 
                              rows="2" 
                              placeholder="Giải thích đáp án đúng...">{{ $question->giaiThich }}</textarea>
                </div>
            </div>
        @else
            {{-- Essay Question - No answer options --}}
            <div class="essay-section">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Câu tự luận:</strong> Học viên sẽ nhập câu trả lời. Giảng viên sẽ chấm điểm sau.
            </div>
        @endif

        <!-- Points -->
        <div class="form-group-gform mt-3">
            <label class="form-label-gform">Điểm *</label>
            <input type="number" 
                   name="questions[{{ $index }}][points]" 
                   class="form-control-gform question-points" 
                   value="{{ $question->diem }}" 
                   step="0.5" 
                   min="0" 
                   placeholder="1.0"
                   required>
        </div>
    </div>
</div>
