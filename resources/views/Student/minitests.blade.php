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
        <div
            id="studentMiniTestConfig"
            data-result-id="{{ $result->maKQDG }}"
            data-submit-url="{{ route('student.minitests.submit', $result->maKQDG) }}"
            data-countdown="{{ $remainingSeconds ?? '' }}"
            data-autosave-template="{{ route('student.minitests.answers.save', [$result->maKQDG, '__QUESTION__']) }}"
            data-upload-template="{{ route('student.minitests.answers.upload', [$result->maKQDG, '__QUESTION__']) }}">
        </div>

        @php
            /** @var MiniTest $miniTest */
            $miniTest = $result->miniTest;
            $chapterName = $miniTest->chapter->tenChuong ?? 'Chương học';
            $skillLabel = $skillNames[$miniTest->skill_type] ?? $miniTest->skill_type;
            $questionCount = $miniTest->questions->count();
            $timeLimitLabel = $miniTest->time_limit_min ? $miniTest->time_limit_min . ' phút' : 'Không giới hạn';
        @endphp

        <section class="minitests minitests--attempt" data-skill="{{ $miniTest->skill_type }}">
            <div class="minitests-inner">
                <header class="minitests-hero">
                    <div class="minitests-hero__main">
                        <div class="minitests-hero__meta">
                            <span class="skill-pill skill-pill--{{ strtolower($miniTest->skill_type) }}">{{ $skillLabel }}</span>
                            <span class="hero-meta__chapter">
                                <i class="bi bi-journal-text me-1"></i>{{ $chapterName }}
                            </span>
                        </div>
                        <h1 class="minitests-hero__title">{{ $miniTest->title }}</h1>
                        <ul class="minitests-hero__stats">
                            <li>
                                <i class="bi bi-clipboard-check me-1"></i>{{ $questionCount }} câu hỏi
                            </li>
                            <li>
                                <i class="bi bi-hourglass-split me-1"></i>{{ $timeLimitLabel }}
                            </li>
                            <li>
                                <i class="bi bi-award me-1"></i>{{ number_format($miniTest->max_score, 1) }} điểm tối đa
                            </li>
                        </ul>
                    </div>
                    <div class="minitests-hero__aside">
                        <div class="countdown-card">
                            <span class="countdown-card__label">Thời gian còn lại</span>
                            <span class="countdown-card__value" id="countdown">{{ $remainingSeconds ? gmdate('i:s', $remainingSeconds) : '--:--' }}</span>
                        </div>
                    </div>
                </header>

                <div class="minitests-layout">
                    <aside class="minitests-sidebar">
                        <div class="sidebar-card sidebar-card--progress">
                            <div class="sidebar-card__head">
                                <h5>Trạng thái câu hỏi</h5>
                            </div>
                            <nav class="question-progress" data-question-progress>
                                @foreach($miniTest->questions as $index => $question)
                                    @php
                                        $answer = $answers[$question->maCauHoi] ?? null;
                                        $rawChoices = $answer && $answer->answer_choice
                                            ? array_filter(array_map('trim', explode(';', $answer->answer_choice)))
                                            : [];
                                        $hasEssayText = $answer && isset($answer->answer_text) && trim($answer->answer_text) !== '';
                                        $hasSpeakingAudio = $answer && $answer->answer_audio_url;
                                        $isAnswered = $question->isChoice() || $question->loai === MiniTestQuestion::TYPE_TRUE_FALSE
                                            ? !empty($rawChoices)
                                            : ($miniTest->skill_type === MiniTest::SKILL_SPEAKING && $question->isEssay()
                                                ? (bool) $hasSpeakingAudio
                                                : (bool) $hasEssayText);
                                    @endphp
                                    <a
                                        href="#question-{{ $question->maCauHoi }}"
                                        class="question-progress__item {{ $isAnswered ? 'is-answered' : '' }}"
                                        data-question-link="{{ $question->maCauHoi }}">
                                        <span class="question-progress__number">{{ $index + 1 }}</span>
                                    </a>
                                @endforeach
                            </nav>
                            <ul class="question-progress__legend">
                                <li>
                                    <span class="legend-dot legend-dot--answered"></span>Đã trả lời
                                </li>
                                <li>
                                    <span class="legend-dot legend-dot--pending"></span>Chưa trả lời
                                </li>
                            </ul>
                        </div>
                        <div class="sidebar-card sidebar-card--note">
                            <div class="sidebar-card__head">
                                <h5>Hướng dẫn</h5>
                            </div>
                            <p>
                                Hệ thống tự động lưu mọi thao tác của bạn. Kiểm tra kỹ trước khi bấm nộp bài để tránh thiếu sót.
                            </p>
                        </div>
                    </aside>

                    <div class="minitests-main">
                        <form id="attemptForm" method="POST" action="{{ route('student.minitests.submit', $result->maKQDG) }}">
                            @csrf
                            <div class="question-stack">
                                @foreach($miniTest->questions as $index => $question)
                                    @php
                                        $answer = $answers[$question->maCauHoi] ?? null;
                                        $savedChoices = $answer && $answer->answer_choice
                                            ? array_filter(array_map('trim', explode(';', $answer->answer_choice)))
                                            : [];
                                        $isSpeaking = $miniTest->skill_type === MiniTest::SKILL_SPEAKING && $question->isEssay();
                                    @endphp
                                    <article
                                        class="question-card"
                                        id="question-{{ $question->maCauHoi }}"
                                        data-question-id="{{ $question->maCauHoi }}"
                                        data-question-type="{{ $question->loai }}"
                                        data-speaking="{{ $isSpeaking ? '1' : '0' }}">
                                        <header class="question-card__header">
                                            <div class="question-card__meta">
                                                <span class="question-card__index">Câu {{ $index + 1 }}</span>
                                                <span class="question-card__points">{{ number_format($question->diem, 1) }} điểm</span>
                                            </div>
                                            <div class="question-card__status autosave-status small text-muted" data-status></div>
                                        </header>
                                        <div class="question-card__body">
                                            <div class="question-card__text">{!! nl2br(e($question->noiDungCauHoi)) !!}</div>

                                            @if($question->audio_url)
                                                <div class="question-card__media">
                                                    <audio controls class="w-100">
                                                        <source src="{{ $question->audio_url }}" type="audio/mpeg">
                                                        Trình duyệt của bạn không hỗ trợ audio.
                                                    </audio>
                                                </div>
                                            @endif

                                            @if($question->image_url)
                                                <div class="question-card__media">
                                                    <img src="{{ $question->image_url }}" alt="Tài liệu minh họa" class="img-fluid rounded">
                                                </div>
                                            @endif

                                            @if($question->pdf_url && $miniTest->skill_type !== MiniTest::SKILL_SPEAKING)
                                                <div class="question-card__resource">
                                                    <a href="{{ $question->pdf_url }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                                        <i class="bi bi-file-earmark-pdf me-1"></i>Xem tài liệu PDF
                                                    </a>
                                                </div>
                                            @endif

                                            @if($question->isChoice())
                                                <div class="question-card__answers answers-list" data-choice-group>
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
                                                <div class="question-card__answers answers-list" data-true-false>
                                                    @foreach(['TRUE' => 'Đúng (TRUE)', 'FALSE' => 'Sai (FALSE)'] as $value => $label)
                                                        <label class="answer-item">
                                                            <input
                                                                type="radio"
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
                                                <div class="question-card__answers">
                                                    @if($isSpeaking)
                                                        <div class="speaking-upload">
                                                            <label class="form-label">Tải lên file ghi âm (.mp3, tối đa 10MB)</label>
                                                            <input
                                                                type="file"
                                                                accept="audio/mp3,audio/mpeg"
                                                                class="form-control speaking-file-input"
                                                                data-question-id="{{ $question->maCauHoi }}">
                                                            @if($answer && $answer->answer_audio_url)
                                                                <div class="current-audio mt-3">
                                                                    <audio controls class="w-100">
                                                                        <source src="{{ $answer->answer_audio_url }}" type="audio/mpeg">
                                                                        Trình duyệt của bạn không hỗ trợ audio.
                                                                    </audio>
                                                                    @if($answer->audio_size_kb)
                                                                        <p class="text-muted small mb-0">Dung lượng: {{ number_format($answer->audio_size_kb) }} KB</p>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                            <p class="text-muted small mb-0 mt-2">Hệ thống tự động lưu sau khi tải lên thành công.</p>
                                                        </div>
                                                    @else
                                                        <label class="form-label">Câu trả lời của bạn</label>
                                                        <textarea
                                                            name="answer-text-{{ $question->maCauHoi }}"
                                                            class="form-control answer-text"
                                                            rows="6"
                                                            data-question-id="{{ $question->maCauHoi }}"
                                                            placeholder="Nhập câu trả lời tại đây...">{{ $answer->answer_text ?? '' }}</textarea>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>

                            <div class="submit-zone">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitAttemptBtn">
                                    <i class="bi bi-check-circle me-2"></i>Nộp bài
                                </button>
                                <p class="text-muted small mb-0 mt-2">
                                    Bài làm được lưu tự động. Bạn có thể nộp bất kỳ lúc nào.
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    @elseif($type === 'result')
        @php
            /** @var MiniTestResult $result */
            $miniTest = $result->miniTest;
            $chapterName = $miniTest->chapter->tenChuong ?? 'Chương học';
            $skillLabel = $skillNames[$miniTest->skill_type] ?? $miniTest->skill_type;
            $questionCount = $miniTest->questions->count();
            $statusText = $result->status === MiniTestResult::STATUS_EXPIRED ? 'Quá thời gian' : 'Đã nộp';
            $timeSpent = $result->time_spent_sec !== null
                ? sprintf('%02d phút %02d giây', intdiv($result->time_spent_sec, 60), $result->time_spent_sec % 60)
                : 'Không xác định';
            $submittedAt = $result->nop_luc ? $result->nop_luc->format('d/m/Y H:i') : null;
            $autoScore = number_format($result->auto_graded_score ?? 0, 1);
            $totalScore = number_format($result->diem ?? 0, 1);
        @endphp

        <section class="minitests minitests--result" data-skill="{{ $miniTest->skill_type }}">
            <header class="minitests-hero">
                <div class="minitests-hero__main">
                    <div class="minitests-hero__meta">
                        <span class="skill-pill skill-pill--{{ strtolower($miniTest->skill_type) }}">{{ $skillLabel }}</span>
                        <span class="hero-meta__chapter">
                            <i class="bi bi-journal-text me-1"></i>{{ $chapterName }}
                        </span>
                    </div>
                    <h1 class="minitests-hero__title">Kết quả - {{ $miniTest->title }}</h1>
                    <ul class="minitests-hero__stats">
                        <li>
                            <i class="bi bi-clipboard-check me-1"></i>Lượt làm #{{ $result->attempt_no }}
                        </li>
                        <li>
                            <i class="bi bi-clock-history me-1"></i>Thời gian làm: {{ $timeSpent }}
                        </li>
                        <li>
                            <i class="bi bi-calendar-event me-1"></i>Nộp lúc: {{ $submittedAt ?? 'Chưa cập nhật' }}
                        </li>
                    </ul>
                </div>
                <div class="minitests-hero__aside">
                    <div class="score-card">
                        <span class="score-card__label">Tổng điểm</span>
                        <span class="score-card__value">{{ $totalScore }}</span>
                        @if(!$result->is_fully_graded)
                            <span class="score-card__note text-warning">Đang chờ chấm tự luận</span>
                        @else
                            <span class="score-card__note">Điểm tự động: {{ $autoScore }}</span>
                        @endif
                    </div>
                </div>
            </header>

            <div class="minitests-layout">
                <aside class="minitests-sidebar">
                    <div class="sidebar-card sidebar-card--summary">
                        <div class="sidebar-card__head">
                            <h5>Tổng quan</h5>
                        </div>
                        <ul class="sidebar-stats">
                            <li><span>Trạng thái</span><strong>{{ $statusText }}</strong></li>
                            <li><span>Số câu đúng</span><strong>{{ $correctCount }} / {{ $questionCount }}</strong></li>
                            <li><span>Câu sai</span><strong>{{ $incorrectCount }}</strong></li>
                            <li><span>Câu chờ chấm</span><strong>{{ $essayCount }}</strong></li>
                            <li><span>Lượt làm còn lại</span><strong>{{ $attemptsLeft }}</strong></li>
                        </ul>
                        @if($attemptsLeft > 0)
                            <form method="POST" action="{{ route('student.minitests.start', $miniTest->maMT) }}" class="mt-3">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-arrow-repeat me-2"></i>Làm lại mini-test
                                </button>
                            </form>
                        @endif
                    </div>
                    @if(!empty($attemptHistory) && $attemptHistory->count())
                        <div class="sidebar-card sidebar-card--history">
                            <div class="sidebar-card__head">
                                <h5>Lịch sử bài làm</h5>
                                <p class="text-muted small mb-0">Theo dõi các lần nộp mini-test trước đây</p>
                            </div>
                            <ul class="attempt-history">
                                @foreach($attemptHistory as $attempt)
                                    @php
                                        $isCurrent = $attempt->maKQDG === $result->maKQDG;
                                        $statusLabel = match($attempt->status) {
                                            \App\Models\MiniTestResult::STATUS_IN_PROGRESS => 'Đang làm',
                                            \App\Models\MiniTestResult::STATUS_EXPIRED => 'Hết giờ',
                                            default => ($attempt->is_fully_graded ? 'Đã chấm' : 'Chờ chấm'),
                                        };
                                        $statusClass = match($attempt->status) {
                                            \App\Models\MiniTestResult::STATUS_EXPIRED => 'is-warning',
                                            \App\Models\MiniTestResult::STATUS_IN_PROGRESS => 'is-info',
                                            default => ($attempt->is_fully_graded ? 'is-success' : 'is-muted'),
                                        };
                                        $scoreLabel = $attempt->diem !== null
                                            ? number_format((float) $attempt->diem, 1) . '/' . number_format((float) $result->miniTest->max_score, 1)
                                            : ($attempt->status === \App\Models\MiniTestResult::STATUS_IN_PROGRESS ? 'Đang làm' : 'Chưa cập nhật');
                                    @endphp
                                    <li class="attempt-history__item {{ $isCurrent ? 'is-current' : '' }}">
                                        <a href="{{ route('student.minitests.result', $attempt->maKQDG) }}" class="attempt-pill">
                                            <div class="attempt-pill__top">
                                                <span class="attempt-pill__label">Lần {{ $attempt->attempt_no }}</span>
                                                <span class="attempt-pill__status {{ $statusClass }}">{{ $statusLabel }}</span>
                                            </div>
                                            <div class="attempt-pill__meta">
                                                <span><i class="bi bi-calendar3 me-1"></i>{{ optional($attempt->nop_luc)->format('d/m/Y H:i') ?? 'Chưa nộp' }}</span>
                                                <span><i class="bi bi-star me-1"></i>{{ $scoreLabel }}</span>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </aside>

                <div class="minitests-main">
                    <div class="result-questions">
                        @foreach($miniTest->questions as $index => $question)
                            @php
                                $answer = $result->studentAnswers->firstWhere('maCauHoi', $question->maCauHoi);
                                $isChoice = $question->isChoice();
                                $correctAnswers = $question->correctAnswers();
                                $studentAnswers = $answer && $answer->answer_choice
                                    ? array_filter(array_map('trim', explode(';', $answer->answer_choice)))
                                    : [];
                                $isSpeaking = $miniTest->skill_type === MiniTest::SKILL_SPEAKING && $question->isEssay();
                                $isTrueFalse = $question->loai === MiniTestQuestion::TYPE_TRUE_FALSE;
                                $isObjective = $isChoice || $isTrueFalse;
                                $isCorrect = $isObjective
                                    ? !array_diff($correctAnswers, $studentAnswers) && count($studentAnswers) === count($correctAnswers)
                                    : null;
                            @endphp
                            <article class="result-question-card" id="result-question-{{ $question->maCauHoi }}">
                                <header class="result-question-card__header">
                                    <div class="result-question-card__meta">
                                        <span class="result-question-card__index">Câu {{ $index + 1 }}</span>
                                        <span class="result-question-card__points">{{ number_format($question->diem, 1) }} điểm</span>
                                    </div>
                                    @if($isObjective)
                                        <span class="result-question-card__badge {{ $isCorrect ? 'is-correct' : 'is-incorrect' }}">
                                            {{ $isCorrect ? 'Đúng' : 'Sai' }}
                                        </span>
                                    @endif
                                </header>
                                <div class="result-question-card__body">
                                    <div class="result-question-card__text">{!! nl2br(e($question->noiDungCauHoi)) !!}</div>

                                    @if($question->audio_url)
                                        <div class="result-question-card__media">
                                            <audio controls class="w-100">
                                                <source src="{{ $question->audio_url }}" type="audio/mpeg">
                                                Trình duyệt của bạn không hỗ trợ audio.
                                            </audio>
                                        </div>
                                    @endif

                                    @if($question->image_url)
                                        <div class="result-question-card__media">
                                            <img src="{{ $question->image_url }}" alt="Tài liệu minh họa" class="img-fluid rounded">
                                        </div>
                                    @endif

                                    @if($isChoice)
                                        <div class="answers-reviewed">
                                            @foreach(['A', 'B', 'C', 'D'] as $option)
                                                @php
                                                    $optionText = $question->{'phuongAn' . $option};
                                                    $isSelected = in_array($option, $studentAnswers, true);
                                                    $isOptionCorrect = in_array($option, $correctAnswers, true);
                                                @endphp
                                                @if($optionText)
                                                    <div class="answer-reviewed {{ $isSelected ? ($isOptionCorrect ? 'correct' : 'incorrect') : ($isOptionCorrect ? 'correct' : '') }}">
                                                        <span class="option-label">{{ $option }}</span>
                                                        <span class="option-text">{!! nl2br(e($optionText)) !!}</span>
                                                        @if($isSelected)
                                                            <span class="badge bg-{{ $isOptionCorrect ? 'success' : 'danger' }} ms-2">
                                                                {{ $isOptionCorrect ? 'Bạn chọn - Đúng' : 'Bạn chọn - Sai' }}
                                                            </span>
                                                        @elseif($isOptionCorrect)
                                                            <span class="badge bg-success ms-2">Đáp án đúng</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @elseif($isTrueFalse)
                                        <div class="answers-reviewed">
                                            @foreach(['TRUE' => 'Đúng (TRUE)', 'FALSE' => 'Sai (FALSE)'] as $value => $label)
                                                @php
                                                    $isOptionCorrect = in_array($value, $correctAnswers, true);
                                                    $isSelected = in_array($value, $studentAnswers, true);
                                                @endphp
                                                <div class="answer-reviewed {{ $isSelected ? ($isOptionCorrect ? 'correct' : 'incorrect') : ($isOptionCorrect ? 'correct' : '') }}">
                                                    <span class="option-text">{{ $label }}</span>
                                                    @if($isSelected)
                                                        <span class="badge bg-{{ $isOptionCorrect ? 'success' : 'danger' }} ms-2">
                                                            {{ $isOptionCorrect ? 'Bạn chọn - Đúng' : 'Bạn chọn - Sai' }}
                                                        </span>
                                                    @elseif($isOptionCorrect)
                                                        <span class="badge bg-success ms-2">Đáp án đúng</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="essay-reviewed">
                                            <h6>Câu trả lời của bạn</h6>
                                            @if($isSpeaking)
                                                @if($answer && $answer->answer_audio_url)
                                                    <audio controls class="w-100 mb-2">
                                                        <source src="{{ $answer->answer_audio_url }}" type="audio/mpeg">
                                                    </audio>
                                                    <p class="text-muted small mb-0">
                                                        Dung lượng: {{ $answer->audio_size_kb ? number_format($answer->audio_size_kb) . ' KB' : '-' }}
                                                    </p>
                                                @else
                                                    <p class="text-muted mb-0">Chưa nộp file ghi âm.</p>
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
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection

@push('scripts')
    <script src="{{ asset('js/Student/minitests.js') }}"></script>
@endpush
