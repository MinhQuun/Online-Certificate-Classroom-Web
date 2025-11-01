@php
    use App\Models\MiniTest;
    use App\Models\MiniTestQuestion;
    use App\Models\MiniTestResult;

    $skillNames = [
        MiniTest::SKILL_LISTENING => 'Listening',
        MiniTest::SKILL_SPEAKING => 'Speaking',
        MiniTest::SKILL_READING => 'Reading',
        MiniTest::SKILL_WRITING => 'Writing',
    ];
@endphp
@extends('layouts.student')

@section('title')
    @if($type === 'attempt')
        Làm bài - {{ $result->miniTest->title }}
    @elseif($type === 'result')
        Kết quả - {{ $result->miniTest->title }}
    @endif
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Student/minitests.css') }}">
@endpush

@section('content')
    @if($type === 'attempt')
        <div id="studentMiniTestConfig"
             data-result-id="{{ $result->maKQDG }}"
             data-submit-url="{{ route('student.minitests.submit', $result->maKQDG) }}"
             data-countdown="{{ $remainingSeconds ?? '' }}"
             data-autosave-template="{{ route('student.minitests.answers.save', [$result->maKQDG, '__QUESTION__']) }}"
             data-upload-template="{{ route('student.minitests.answers.upload', [$result->maKQDG, '__QUESTION__']) }}">
        </div>

        <div class="minitests-attempt">
            <div class="attempt-header">
                <div>
                    <h1>{{ $result->miniTest->title }}</h1>
                    <p class="text-muted mb-0">
                        {{ $result->miniTest->chapter->tenChuong ?? 'Chương học' }} • Kỹ năng: {{ $result->miniTest->skill_type }}
                    </p>
                </div>
                <div class="timer-box">
                    <span class="label">Thời gian còn lại</span>
                    <span class="time" id="countdown">{{ $remainingSeconds ? gmdate('i:s', $remainingSeconds) : '∞' }}</span>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <form id="attemptForm" method="POST" action="{{ route('student.minitests.submit', $result->maKQDG) }}">
                        @csrf
                        <div class="question-list">
                            @foreach($result->miniTest->questions as $index => $question)
                                @php
                                    $answer = $answers[$question->maCauHoi] ?? null;
                                    $savedChoices = $answer && $answer->answer_choice ? array_filter(array_map('trim', explode(';', $answer->answer_choice))) : [];
                                    $isSpeaking = $result->miniTest->skill_type === MiniTest::SKILL_SPEAKING && $question->isEssay();
                                @endphp
                                <div class="question-card" data-question-id="{{ $question->maCauHoi }}" data-question-type="{{ $question->loai }}" data-speaking="{{ $isSpeaking ? '1' : '0' }}">
                                    <div class="question-header">
                                        <span class="number">Câu {{ $index + 1 }}</span>
                                        <span class="points">{{ number_format($question->diem, 1) }} điểm</span>
                                    </div>
                                    <div class="autosave-status text-muted small" data-status></div>
                                    <div class="question-body">
                                        <p class="question-text">{!! nl2br(e($question->noiDungCauHoi)) !!}</p>

                                        @if($question->audio_url)
                                            <audio controls class="mb-3 w-100">
                                                <source src="{{ $question->audio_url }}" type="audio/mpeg">
                                                Trình duyệt của bạn không hỗ trợ audio.
                                            </audio>
                                        @endif

                                        @if($question->image_url)
                                            <div class="mb-3">
                                                <img src="{{ $question->image_url }}" alt="Tài liệu minh họa" class="img-fluid rounded">
                                            </div>
                                        @endif

                                        @if($question->pdf_url && $result->miniTest->skill_type !== MiniTest::SKILL_SPEAKING)
                                            <div class="mb-3">
                                                <a href="{{ $question->pdf_url }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                                    <i class="bi bi-file-earmark-pdf me-1"></i>Xem tài liệu PDF
                                                </a>
                                            </div>
                                        @endif

                                        @if($question->isChoice())
                                            <div class="answers-list" data-choice-group>
                                                @foreach(['A', 'B', 'C', 'D'] as $option)
                                                    @php
                                                        $optionText = $question->{'phuongAn' . $option};
                                                    @endphp
                                                    @if($optionText)
                                                        <label class="answer-item">
                                                            <input
                                                                type="{{ $question->allowsMultipleSelections() ? 'checkbox' : 'radio' }}"
                                                                name="answer-choice-{{ $question->maCauHoi }}{{ $question->allowsMultipleSelections() ? '[]' : '' }}"
                                                                value="{{ $option }}"
                                                                class="answer-input"
                                                                data-question-id="{{ $question->maCauHoi }}"
                                                                @checked(in_array($option, $savedChoices, true))>
                                                            <span class="option-label">{{ $option }}</span>
                                                            <span class="option-text">{!! nl2br(e($optionText)) !!}</span>
                                                        </label>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @elseif($question->loai === MiniTestQuestion::TYPE_TRUE_FALSE)
                                            <div class="answers-list" data-true-false>
                                                @foreach(['TRUE' => 'Đúng (TRUE)', 'FALSE' => 'Sai (FALSE)'] as $value => $label)
                                                    <label class="answer-item">
                                                        <input type="radio"
                                                               name="answer-choice-{{ $question->maCauHoi }}"
                                                               value="{{ $value }}"
                                                               class="answer-input"
                                                               data-question-id="{{ $question->maCauHoi }}"
                                                               @checked(in_array($value, $savedChoices, true))>
                                                        <span class="option-text">{{ $label }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="essay-answer" data-essay>
                                                @if(!$isSpeaking)
                                                    <textarea
                                                        name="answer-text-{{ $question->maCauHoi }}"
                                                        class="form-control answer-text"
                                                        rows="6"
                                                        data-question-id="{{ $question->maCauHoi }}"
                                                        placeholder="Nhập câu trả lời tại đây...">{{ $answer->answer_text ?? '' }}</textarea>
                                                @endif
                                                @if($isSpeaking)
                                                    <div class="speaking-upload mt-3">
                                                        <label class="form-label">Tải lên file ghi âm (.mp3, tối đa 10MB)</label>
                                                        <input type="file"
                                                               accept="audio/mp3,audio/mpeg"
                                                               class="form-control speaking-file-input"
                                                               data-question-id="{{ $question->maCauHoi }}">
                                                        @if($answer && $answer->answer_audio_url)
                                                            <div class="mt-2">
                                                                <audio controls class="w-100">
                                                                    <source src="{{ $answer->answer_audio_url }}" type="audio/mpeg">
                                                                    Trình duyệt của bạn không hỗ trợ audio.
                                                                </audio>
                                                            </div>
                                                        @endif
                                                        <small class="text-muted">Hệ thống tự động lưu sau khi tải lên thành công.</small>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="attempt-actions">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitAttemptBtn">
                                <i class="bi bi-check-circle me-2"></i>Nộp bài
                            </button>
                            <p class="text-muted small mt-2 mb-0">Bài của bạn sẽ được tự động lưu sau mỗi thao tác.</p>
                        </div>
                    </form>
                </div>

                <div class="col-lg-4">
                    <div class="attempt-sidebar">
                        <div class="summary-card">
                            <h5><i class="bi bi-clipboard-check me-2"></i>Tóm tắt</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Số câu hỏi</span>
                                <strong>{{ $result->miniTest->questions->count() }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Điểm tối đa</span>
                                <strong>{{ number_format($result->miniTest->max_score, 1) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Thời gian</span>
                                <strong>{{ $result->miniTest->time_limit_min ?: 'Không giới hạn' }} phút</strong>
                            </div>
                            <hr>
                            <p class="small text-muted mb-0">Nếu bạn thoát giữa chừng, hệ thống sẽ lưu và bạn có thể quay lại trong thời gian cho phép.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif($type === 'result')
        <div class="minitests-result">
            <div class="result-header">
                <div>
                    <h1>Kết quả - {{ $result->miniTest->title }}</h1>
                    <p class="text-muted mb-0">Lượt làm #{{ $result->attempt_no }} • {{ $result->miniTest->chapter->tenChuong ?? 'Chương học' }}</p>
                </div>
                <div class="score-pill">
                    <span class="label">Điểm tự động</span>
                    <span class="value">{{ number_format($result->auto_graded_score ?? 0, 1) }}</span>
                    @if(!$result->is_fully_graded)
                        <span class="sub">Đang chờ chấm tự luận</span>
                    @else
                        <span class="sub">Tổng điểm: {{ number_format($result->diem ?? 0, 1) }}</span>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="result-summary">
                        <h5><i class="bi bi-clipboard-data me-2"></i>Tổng quan</h5>
                        <ul class="list-unstyled mb-0">
                            <li><strong>Trạng thái:</strong> {{ $result->status === App\Models\MiniTestResult::STATUS_EXPIRED ? 'Quá thời gian' : 'Đã nộp' }}</li>
                            <li><strong>Nộp lúc:</strong> {{ optional($result->nop_luc)->format('d/m/Y H:i') ?? 'Chưa có' }}</li>
                            <li><strong>Thời gian làm:</strong> {{ $result->time_spent_sec ? gmdate('i \p\h\ú\t s \g\i\â\y', $result->time_spent_sec) : 'Không xác định' }}</li>
                            <li><strong>Số câu đúng:</strong> {{ $correctCount }} / {{ $result->miniTest->questions->count() }}</li>
                            <li><strong>Câu sai:</strong> {{ $incorrectCount }}</li>
                            <li><strong>Câu cần chấm:</strong> {{ $essayCount }}</li>
                            <li><strong>Lượt làm còn lại:</strong> {{ $attemptsLeft }}</li>
                        </ul>
                        @if($attemptsLeft > 0)
                            <form method="POST" action="{{ route('student.minitests.start', $result->miniTest->maMT) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100 mt-3">
                                    <i class="bi bi-arrow-repeat me-2"></i>Làm lại mini-test
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="result-questions">
                        @foreach($result->miniTest->questions as $index => $question)
                            @php
                                $answer = $result->studentAnswers->firstWhere('maCauHoi', $question->maCauHoi);
                                $isChoice = $question->isChoice();
                                $correctAnswers = $question->correctAnswers();
                                $studentAnswers = $answer && $answer->answer_choice ? array_filter(array_map('trim', explode(';', $answer->answer_choice))) : [];
                            @endphp
                            <div class="result-question-card">
                                <div class="question-header">
                                    <span class="number">Câu {{ $index + 1 }}</span>
                                    <span class="points">{{ number_format($question->diem, 1) }} điểm</span>
                                </div>
                                <div class="question-body">
                                    <p class="question-text">{!! nl2br(e($question->noiDungCauHoi)) !!}</p>

                                    @if($question->audio_url)
                                        <audio controls class="mb-3 w-100">
                                            <source src="{{ $question->audio_url }}" type="audio/mpeg">
                                        </audio>
                                    @endif

                                    @if($question->image_url)
                                        <div class="mb-3">
                                            <img src="{{ $question->image_url }}" alt="Tài liệu" class="img-fluid rounded">
                                        </div>
                                    @endif

                                    @if($isChoice)
                                        <div class="answers-reviewed">
                                            @foreach(['A', 'B', 'C', 'D'] as $option)
                                                @php
                                                    $optionText = $question->{'phuongAn' . $option};
                                                    $isCorrect = in_array($option, $correctAnswers, true);
                                                    $isSelected = in_array($option, $studentAnswers, true);
                                                @endphp
                                                @if($optionText)
                                                    <div class="answer-reviewed {{ $isSelected ? ($isCorrect ? 'correct' : 'incorrect') : ($isCorrect ? 'correct' : '') }}">
                                                        <span class="option-label">{{ $option }}</span>
                                                        <span class="option-text">{!! nl2br(e($optionText)) !!}</span>
                                                        @if($isSelected)
                                                            <span class="badge bg-{{ $isCorrect ? 'success' : 'danger' }} ms-2">{{ $isCorrect ? 'Bạn chọn - Đúng' : 'Bạn chọn - Sai' }}</span>
                                                        @elseif($isCorrect)
                                                            <span class="badge bg-success ms-2">Đáp án đúng</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @elseif($question->loai === MiniTestQuestion::TYPE_TRUE_FALSE)
                                        <div class="answers-reviewed">
                                            @foreach(['TRUE' => 'Đúng (TRUE)', 'FALSE' => 'Sai (FALSE)'] as $value => $label)
                                                @php
                                                    $isCorrect = in_array($value, $correctAnswers, true);
                                                    $isSelected = in_array($value, $studentAnswers, true);
                                                @endphp
                                                <div class="answer-reviewed {{ $isSelected ? ($isCorrect ? 'correct' : 'incorrect') : ($isCorrect ? 'correct' : '') }}">
                                                    <span class="option-text">{{ $label }}</span>
                                                    @if($isSelected)
                                                        <span class="badge bg-{{ $isCorrect ? 'success' : 'danger' }} ms-2">{{ $isCorrect ? 'Bạn chọn - Đúng' : 'Bạn chọn - Sai' }}</span>
                                                    @elseif($isCorrect)
                                                        <span class="badge bg-success ms-2">Đáp án đúng</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="essay-reviewed">
                                            <h6>Câu trả lời của bạn</h6>
                                            @if($result->miniTest->skill_type === MiniTest::SKILL_SPEAKING)
                                                @if($answer && $answer->answer_audio_url)
                                                    <audio controls class="w-100 mb-2">
                                                        <source src="{{ $answer->answer_audio_url }}" type="audio/mpeg">
                                                    </audio>
                                                    <p class="text-muted small">Dung lượng: {{ $answer->audio_size_kb ? number_format($answer->audio_size_kb) . ' KB' : '—' }}</p>
                                                @else
                                                    <p class="text-muted">Chưa nộp file ghi âm.</p>
                                                @endif
                                            @else
                                                <div class="answer-text">{!! nl2br(e($answer->answer_text ?? 'Chưa có câu trả lời.')) !!}</div>
                                            @endif
                                            @if($answer && $answer->teacher_feedback)
                                                <div class="feedback-box">
                                                    <strong>Phản hồi từ giáo viên:</strong>
                                                    <p class="mb-0">{!! nl2br(e($answer->teacher_feedback)) !!}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script src="{{ asset('js/Student/minitests.js') }}"></script>
@endpush







