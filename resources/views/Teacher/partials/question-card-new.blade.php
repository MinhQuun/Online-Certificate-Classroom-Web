{{-- Partial: New Question Card Template --}}
<div class="question-card" data-question-index="{{ $index }}">
    <div class="question-header">
        <span class="question-number">Câu {{ $index + 1 }}</span>
        <button type="button" class="btn btn-sm btn-outline-danger delete-question-btn">
            <i class="bi bi-trash"></i>
        </button>
    </div>

    <div class="question-body">
        <!-- Question Type Selector (for multiple choice skills) -->
        @if($skillType !== 'WRITING')
            <div class="form-group-gform mb-3">
                <label class="form-label-gform">Loại câu hỏi</label>
                <select name="questions[{{ $index }}][question_type]" class="form-control-gform question-type-select">
                    <option value="single_choice">Một đáp án đúng</option>
                    <option value="multiple_choice">Nhiều đáp án đúng</option>
                    <option value="true_false">Đúng/Sai</option>
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
                      required></textarea>
        </div>

        <!-- Media Section -->
        <div class="media-section">
            <div class="media-preview-container"></div>
            <div class="media-buttons">
                @if(in_array($skillType, ['LISTENING', 'SPEAKING']))
                    <label class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-music-note me-1"></i> Thêm Audio
                        <input type="file" name="questions[{{ $index }}][audio]" 
                               accept="audio/mp3,audio/mpeg,audio/wav" class="d-none audio-input">
                    </label>
                @endif
                
                @if($skillType === 'READING')
                    <label class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-file-pdf me-1"></i> Thêm PDF
                        <input type="file" name="questions[{{ $index }}][pdf]" 
                               accept="application/pdf" class="d-none pdf-input">
                    </label>
                @endif

                <label class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-image me-1"></i> Thêm Hình ảnh
                    <input type="file" name="questions[{{ $index }}][image]" 
                           accept="image/*" class="d-none image-input">
                </label>
            </div>
        </div>

        <!-- Answers Section (for non-essay questions) -->
        @if($skillType !== 'WRITING')
            <div class="answers-section">
                <label class="form-label-gform">Đáp án *</label>
                <div class="answers-container">
                    @foreach(['A', 'B', 'C', 'D'] as $idx => $letter)
                        <div class="answer-item">
                            <input type="radio" 
                                   name="questions[{{ $index }}][correct_answer]" 
                                   value="{{ $letter }}" 
                                   id="q{{ $index }}_{{ $letter }}"
                                   {{ $idx === 0 ? 'checked' : '' }} 
                                   required>
                            <label for="q{{ $index }}_{{ $letter }}" class="answer-label">{{ $letter }}</label>
                            <input type="text" 
                                   name="questions[{{ $index }}][option_{{ strtolower($letter) }}]" 
                                   class="form-control-gform" 
                                   placeholder="Nhập đáp án {{ $letter }}" 
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
                              placeholder="Giải thích đáp án đúng..."></textarea>
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
                   value="1.0" 
                   step="0.5" 
                   min="0" 
                   placeholder="1.0"
                   required>
        </div>
    </div>
</div>
