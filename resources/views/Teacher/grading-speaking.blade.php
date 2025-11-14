@extends('layouts.teacher')

@section('title')
    @if($type === 'index')
        Chấm điểm mini-test Speaking
    @elseif($type === 'show')
        Chấm điểm bài Speaking
    @endif
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Teacher/grading.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Teacher/grading-speaking.css') }}">
@endpush

@section('content')
    @if($type === 'index')
        <section class="page-header">
            <span class="kicker">Giảng viên</span>
            <h1 class="title">Chấm điểm Speaking</h1>
            <p class="muted">Nghe các bản ghi do học viên nộp, đưa điểm và nhận xét kịp thời.</p>
        </section>

        {{-- <div class="btn-group btn-group-sm mb-4">
            <a href="{{ route('teacher.grading.writing.index') }}" class="btn btn-outline-secondary">
                Writing
            </a>
            <a href="{{ route('teacher.grading.speaking.index') }}" class="btn btn-primary">
                Speaking
            </a>
        </div> --}}

        <div class="card border-0 shadow-sm mb-4 grading-filter">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <label for="courseFilter" class="form-label text-muted text-uppercase small mb-1">Lọc theo khóa học</label>
                        <select id="courseFilter" class="form-select form-select-lg">
                            <option value="">Tất cả khóa học</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->maKH }}" {{ $selectedCourseId == $course->maKH ? 'selected' : '' }}>
                                    {{ $course->tenKH }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="row g-2 mt-2 mt-md-0">
                            <div class="col-6">
                                <div class="metric-box">
                                    <div class="metric-value">{{ $results->total() }}</div>
                                    <div class="metric-label">Bài cần chấm</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="metric-box">
                                    <div class="metric-value">{{ $results->count() }}</div>
                                    <div class="metric-label">Trên trang này</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($results->isEmpty())
            <div class="alert alert-info border-0 shadow-sm">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-check-circle fs-3 text-success"></i>
                    <div>
                        <h5 class="mb-1">Chưa có bài cần chấm</h5>
                        <p class="mb-0">Tất cả bài Speaking đã được chấm điểm hoặc học viên chưa nộp bản ghi.</p>
                    </div>
                </div>
            </div>
        @else
            @foreach($results as $result)
                @php
                    $audioAnswers = $result->studentAnswers;
                    $audioCount = $audioAnswers->count();
                    $totalDurationSec = (int) $audioAnswers->sum('audio_duration_sec');
                    $durationLabel = $totalDurationSec > 0
                        ? sprintf('%02d:%02d', intdiv($totalDurationSec, 60), $totalDurationSec % 60)
                        : null;
                @endphp
                <div class="grading-card">
                    <div class="student-info">
                        <div class="student-avatar">
                            {{ strtoupper(substr($result->student->user->hoTen, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">{{ $result->student->user->hoTen }}</h5>
                            <p class="text-muted mb-0">
                                <i class="bi bi-envelope me-1"></i> {{ $result->student->user->email }}
                            </p>
                        </div>
                        <span class="pending-badge">
                            <i class="bi bi-mic me-1"></i> Chờ chấm
                        </span>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <strong>Khóa học:</strong><br>
                            {{ $result->miniTest->course->tenKH }}
                        </div>
                        <div class="col-md-4">
                            <strong>Mini-test:</strong><br>
                            {{ $result->miniTest->title }}
                        </div>
                        <div class="col-md-4">
                            <strong>Nộp lúc:</strong><br>
                            {{ $result->nop_luc->format('d/m/Y H:i') }}
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <strong>Điểm trắc nghiệm:</strong>
                            <span class="badge bg-success">{{ number_format($result->auto_graded_score ?? 0, 2) }}</span>
                        </div>
                        <div class="col-md-4">
                            <strong>Số bản ghi:</strong>
                            <span class="badge bg-info text-dark">
                                <i class="bi bi-soundwave me-1"></i>{{ $audioCount }}
                            </span>
                        </div>
                        <div class="col-md-4">
                            <strong>Lần làm bài:</strong>
                            <span class="badge bg-secondary">{{ $result->attempt_no }}</span>
                        </div>
                    </div>

                    <div class="speaking-audio-summary mb-3">
                        <span class="summary-pill">
                            <i class="bi bi-clock-history me-1"></i>
                            {{ $durationLabel ?? 'Thời lượng đang cập nhật' }}
                        </span>
                        <span class="summary-pill">
                            <i class="bi bi-mic-fill me-1"></i>
                            {{ $audioCount > 1 ? 'Nhiều nhiệm vụ Speaking' : '1 nhiệm vụ Speaking' }}
                        </span>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route($routePrefix . '.show', $result->maKQDG) }}" class="btn btn-primary">
                            <i class="bi bi-headphones me-2"></i> Nghe &amp; chấm điểm
                        </a>
                        <a href="{{ route('teacher.minitests.index', ['course' => $result->miniTest->maKH]) }}"
                           class="btn btn-outline-secondary">
                            <i class="bi bi-eye me-2"></i> Xem mini-test
                        </a>
                    </div>
                </div>
            @endforeach

            <div class="d-flex justify-content-center mt-4">
                {{ $results->links() }}
            </div>
        @endif
    @elseif($type === 'show')
        <section class="page-header page-header--has-action">
            <div class="page-header-actions">
                <a href="{{ route($routePrefix . '.index') }}" class="btn btn-ghost back-link">
                    <i class="bi bi-arrow-left me-2"></i> Quay lại
                </a>
            </div>
            <span class="kicker">Chấm điểm Speaking</span>
            <h1 class="title">{{ $result->miniTest->title }}</h1>
            <p class="muted">{{ $result->miniTest->course->tenKH }} / {{ $result->miniTest->chapter->tenChuong }}</p>
        </section>

        <div class="info-box">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-person-circle me-2"></i> Thông tin học viên</h5>
                    <p class="mb-1"><strong>Họ tên:</strong> {{ $result->student->user->hoTen }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $result->student->user->email }}</p>
                    <p class="mb-0"><strong>Nộp bài:</strong> {{ $result->nop_luc->format('d/m/Y H:i') }}</p>
                </div>
                <div class="col-md-6">
                    <h5><i class="bi bi-clipboard-check me-2"></i> Thông tin mini-test</h5>
                    <p class="mb-1"><strong>Lần làm:</strong> {{ $result->attempt_no }}</p>
                    <p class="mb-1">
                        <strong>Điểm trắc nghiệm:</strong>
                        <span class="badge bg-success">{{ number_format($result->auto_graded_score ?? 0, 2) }}</span>
                    </p>
                    <p class="mb-0"><strong>Điểm tối đa:</strong> {{ $result->miniTest->max_score }}</p>
                </div>
            </div>
        </div>

        @php
            $audioWithFiles = $result->studentAnswers->whereNotNull('answer_audio_url');
            $audioAnswers = $audioWithFiles
                ->reject(fn ($answer) => $answer->isGraded())
                ->values();
            $hasAudioFiles = $audioWithFiles->isNotEmpty();
        @endphp

        @if(!$hasAudioFiles)
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Không tìm thấy bản ghi Speaking cho bài làm này. Kiểm tra lại với học viên trước khi cập nhật điểm.
            </div>
        @elseif($audioAnswers->isEmpty())
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-2"></i>
                Tất cả bản ghi Speaking đã được chấm. Quay lại danh sách để tiếp tục.
            </div>
        @else
            <noscript>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-octagon me-2"></i>
                    Cần bật JavaScript để nghe bản ghi và chấm điểm Speaking.
                </div>
            </noscript>
            <form action="{{ route($routePrefix . '.grade', $result->maKQDG) }}" method="POST" data-speaking-form>
                @csrf

                @foreach($audioAnswers as $index => $answer)
                    <div class="answer-card" data-speaking-card>
                        <div class="question-header">
                            <h5 class="mb-2">
                                <span class="badge bg-primary me-2">Nhiệm vụ {{ $loop->iteration }}</span>
                                Điểm tối đa: {{ $answer->question->diem }}
                            </h5>
                            <p class="mb-0">{{ $answer->question->noiDungCauHoi }}</p>
                        </div>

                        @if($answer->question->image_url)
                            <div class="mb-3">
                                <img src="{{ $answer->question->image_url }}" alt="Tư liệu câu hỏi" class="question-asset">
                            </div>
                        @endif

                        @if($answer->question->pdf_url)
                            <div class="mb-3">
                                <a href="{{ $answer->question->pdf_url }}" target="_blank" rel="noopener"
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-file-pdf me-2"></i> Xem tài liệu PDF
                                </a>
                            </div>
                        @endif

                        <div class="student-answer student-answer--audio">
                            <h6 class="mb-3"><i class="bi bi-soundwave me-2"></i> Bản ghi của học viên</h6>
                            <div class="speaking-audio" data-speaking-audio-wrapper>
                                <div class="speaking-audio__player">
                                    <audio controls preload="metadata"
                                           data-speaking-audio
                                           src="{{ $answer->answer_audio_url }}">
                                        <source src="{{ $answer->answer_audio_url }}" type="{{ $answer->audio_mime ?? 'audio/mpeg' }}">
                                        Trình duyệt của bạn không hỗ trợ phát âm thanh.
                                    </audio>
                                </div>
                                <div class="speaking-audio__meta">
                                    <span class="speaking-audio__meta-item">
                                        <i class="bi bi-clock-history me-1"></i>
                                        <span data-audio-duration>
                                            {{ $answer->audio_duration_sec ? sprintf('%02d:%02d', intdiv($answer->audio_duration_sec, 60), $answer->audio_duration_sec % 60) : 'Đang tính thời lượng' }}
                                        </span>
                                    </span>
                                    <span class="speaking-audio__meta-item">
                                        <i class="bi bi-hdd me-1"></i>
                                        @if($answer->audio_size_kb)
                                            {{ number_format(max($answer->audio_size_kb / 1024, 0.1), 2) }} MB
                                        @else
                                            Chưa có dung lượng
                                        @endif
                                    </span>
                                    <a href="{{ $answer->answer_audio_url }}" target="_blank" rel="noopener"
                                       class="speaking-audio__meta-item">
                                        <i class="bi bi-cloud-arrow-down me-1"></i> Tải xuống
                                    </a>
                                </div>
                                <div class="d-flex align-items-center gap-2 speaking-audio__status">
                                    <span class="badge text-bg-warning" data-listen-status>Chưa nghe</span>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-mark-listened>
                                        <i class="bi bi-check2-circle me-1"></i> Đánh dấu đã nghe
                                    </button>
                                </div>
                                <p class="speaking-audio__hint text-muted small mt-2" data-listen-hint>
                                    Nghe ít nhất 60% thời lượng bản ghi để mở khóa phần chấm điểm.
                                </p>
                            </div>
                        </div>

                        <div class="scoring-section">
                            <input type="hidden" name="grades[{{ $index }}][answer_id]" value="{{ $answer->id }}">
                            <input type="hidden" name="grades[{{ $index }}][listened]" value="0" data-listened-field>

                            <div class="alert alert-warning speaking-gate-alert small mb-3" data-gate-warning>
                                <i class="bi bi-lock me-2"></i>
                                Vui lòng nghe bản ghi trước khi nhập điểm và nhận xét.
                            </div>

                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label"><strong>Điểm *</strong></label>
                                    <input type="number"
                                           name="grades[{{ $index }}][score]"
                                           class="form-control score-input"
                                           min="0"
                                           max="{{ $answer->question->diem }}"
                                           step="0.5"
                                           placeholder="0.0"
                                           required
                                           disabled
                                           data-requires-listened>
                                    <small class="text-muted">Tối đa: {{ $answer->question->diem }}</small>
                                </div>
                                <div class="col-md-9">
                                    <label class="form-label"><strong>Phản hồi cho học viên</strong></label>
                                    <textarea name="grades[{{ $index }}][feedback]"
                                              class="form-control"
                                              rows="3"
                                              placeholder="Gợi ý cải thiện phát âm, ngữ điệu, từ vựng..."
                                              disabled
                                              data-requires-listened></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="card border-0 shadow-sm speaking-submit-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="mb-1">Hoàn tất chấm điểm Speaking</h6>
                                <p class="text-muted small mb-0">
                                    Hệ thống sẽ lưu điểm thủ công, cộng với điểm trắc nghiệm (nếu có) và thông báo cho học viên.
                                </p>
                                <p class="text-muted small mb-0" data-submit-hint>
                                    Nghe toàn bộ các bản ghi để kích hoạt nút lưu điểm.
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <a href="{{ route($routePrefix . '.index') }}" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-x-circle me-2"></i> Hủy
                                </a>
                                <button type="submit" class="btn btn-success btn-lg" data-submit-grades disabled>
                                    <i class="bi bi-check-circle me-2"></i> Lưu điểm
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @endif
    @endif
@endsection

@push('scripts')
    @if($type === 'index')
        <script src="{{ asset('js/Teacher/grading.js') }}"></script>
    @elseif($type === 'show')
        <script src="{{ asset('js/Teacher/grading-speaking.js') }}" defer></script>
    @endif
@endpush
