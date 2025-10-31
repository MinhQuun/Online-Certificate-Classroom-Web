@php
    use App\Models\MiniTest;
    use App\Models\MiniTestQuestion;
    use Illuminate\Support\Str;

    $skillBadges = [
        MiniTest::SKILL_LISTENING => ['icon' => 'bi-ear', 'label' => 'Listening', 'class' => 'skill-listening'],
        MiniTest::SKILL_READING   => ['icon' => 'bi-book', 'label' => 'Reading', 'class' => 'skill-reading'],
        MiniTest::SKILL_WRITING   => ['icon' => 'bi-pencil-square', 'label' => 'Writing', 'class' => 'skill-writing'],
        MiniTest::SKILL_SPEAKING  => ['icon' => 'bi-mic', 'label' => 'Speaking', 'class' => 'skill-speaking'],
    ];
@endphp

@extends('layouts.teacher')

@section('title')
    @if($type === 'index')
        Quản lý Mini-Test
    @else
        Mini-Test: {{ $miniTest->title ?? 'Mini-Test' }}
    @endif
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Teacher/minitests.css') }}">
@endpush

@section('content')
    @if($type === 'index')
        <div id="teacherMinitestsConfig"
             data-csrf="{{ csrf_token() }}"
             data-update-route="{{ route('teacher.minitests.update', '__ID__') }}"
             data-material-route="{{ route('teacher.minitests.materials.store', '__ID__') }}">
        </div>

        <section class="page-header">
            <span class="kicker">Giảng viên</span>
            <h1 class="title">Quản lý Mini-Test</h1>
            <p class="muted">Xây dựng bài kiểm tra theo kỹ năng cho từng chương học.</p>
        </section>

        @if($courses->isEmpty())
            <div class="alert alert-info border-0 shadow-sm">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-inboxes fs-3 text-primary"></i>
                    <div>
                        <h5 class="mb-1">Chưa có khóa học nào</h5>
                        <p class="mb-0">Hãy liên hệ Quản trị viên để được gán khóa học trước khi tạo mini-test.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body d-flex flex-wrap align-items-center gap-3">
                    <div class="flex-grow-1">
                        <label for="courseSelector" class="form-label text-uppercase small text-muted mb-1">Khóa học</label>
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <select id="courseSelector"
                                    class="form-select form-select-lg w-auto"
                                    data-base-url="{{ route('teacher.minitests.index') }}">
                                @foreach($courses as $course)
                                    <option value="{{ $course->maKH }}" @selected($activeCourse && $activeCourse->maKH === $course->maKH)>
                                        {{ $course->tenKH }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-people-fill me-1"></i>{{ number_format($activeCourse?->students_count ?? 0) }} học viên
                            </span>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-list-task me-1"></i>{{ number_format($activeCourse?->chapters->count() ?? 0) }} chương
                            </span>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#createMiniTestModal">
                        <i class="bi bi-plus-circle me-2"></i> Tạo Mini-Test
                    </button>
                </div>
            </div>

            @if($activeCourse)
                @php
                    $totalTests = $activeCourse->chapters->sum(fn($chapter) => $chapter->miniTests->count());
                    $publishedTests = $activeCourse->chapters->sum(fn($chapter) => $chapter->miniTests->where('is_published', true)->count());
                @endphp

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="metric-pill">
                            <div class="icon"><i class="bi bi-file-earmark-text"></i></div>
                            <div>
                                <div class="value">{{ $totalTests }}</div>
                                <div class="label">Mini-test đã tạo</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="metric-pill">
                            <div class="icon"><i class="bi bi-megaphone"></i></div>
                            <div>
                                <div class="value">{{ $publishedTests }}</div>
                                <div class="label">Mini-test đã công bố</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion" id="chaptersAccordion">
                    @foreach($activeCourse->chapters as $chapter)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="chapter-{{ $chapter->maChuong }}">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#chapterCollapse-{{ $chapter->maChuong }}"
                                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                        aria-controls="chapterCollapse-{{ $chapter->maChuong }}">
                                    <div class="w-100 d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0">{{ $chapter->tenChuong }}</h5>
                                            <span class="text-muted small">Có {{ $chapter->miniTests->count() }} mini-test</span>
                                        </div>
                                        <span class="badge bg-secondary">Thứ tự {{ $chapter->thuTu }}</span>
                                    </div>
                                </button>
                            </h2>
                            <div id="chapterCollapse-{{ $chapter->maChuong }}"
                                 class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                 aria-labelledby="chapter-{{ $chapter->maChuong }}"
                                 data-bs-parent="#chaptersAccordion">
                                <div class="accordion-body">
                                    @forelse($chapter->miniTests as $miniTest)
                                        @php
                                            $skill = $skillBadges[$miniTest->skill_type] ?? null;
                                            $questionCount = $miniTest->questions->count();
                                        @endphp
                                        <div class="minitest-item" id="minitest-{{ $miniTest->maMT }}">
                                            <div class="minitest-meta">
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="skill-badge {{ $skill['class'] ?? '' }}">
                                                        <i class="bi {{ $skill['icon'] ?? 'bi-puzzle' }}"></i>
                                                        {{ $skill['label'] ?? $miniTest->skill_type }}
                                                    </span>
                                                    <div>
                                                        <h5 class="mb-1">{{ $miniTest->title }}</h5>
                                                        <div class="d-flex flex-wrap gap-3 text-muted small">
                                                            <span><i class="bi bi-question-circle me-1"></i>{{ $questionCount }} câu hỏi</span>
                                                            <span><i class="bi bi-clock-history me-1"></i>{{ $miniTest->time_limit_min ?: 'Không giới hạn' }} phút</span>
                                                            <span><i class="bi bi-arrow-repeat me-1"></i>{{ $miniTest->attempts_allowed }} lượt làm</span>
                                                            <span><i class="bi bi-star me-1"></i>Điểm tối đa: {{ number_format($miniTest->max_score, 1) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="minitest-status">
                                                    @if($miniTest->is_published)
                                                        <span class="badge bg-success"><i class="bi bi-broadcast-pin me-1"></i>Đã công bố</span>
                                                    @else
                                                        <span class="badge bg-secondary"><i class="bi bi-eye-slash me-1"></i>Chưa công bố</span>
                                                    @endif
                                                    @if(!$miniTest->is_active)
                                                        <span class="badge bg-warning text-dark"><i class="bi bi-pause-circle me-1"></i>Tạm khóa</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="minitest-actions">
                                                <div class="btn-group">
                                                    <a href="{{ route('teacher.minitests.questions.form', $miniTest->maMT) }}"
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-pencil-square me-1"></i> Soạn câu hỏi
                                                    </a>
                                                    <button class="btn btn-outline-secondary btn-sm edit-minitest-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editMiniTestModal"
                                                            data-minitest-id="{{ $miniTest->maMT }}"
                                                            data-course-id="{{ $miniTest->maKH }}"
                                                            data-chapter-id="{{ $chapter->maChuong }}"
                                                            data-title="{{ $miniTest->title }}"
                                                            data-skill-type="{{ $miniTest->skill_type }}"
                                                            data-order="{{ $miniTest->thuTu }}"
                                                            data-time-limit="{{ $miniTest->time_limit_min }}"
                                                            data-attempts="{{ $miniTest->attempts_allowed }}"
                                                            data-weight="{{ $miniTest->weight }}"
                                                            data-is-active="{{ $miniTest->is_active ? 1 : 0 }}">
                                                        <i class="bi bi-gear me-1"></i> Cấu hình
                                                    </button>
                                                    <button class="btn btn-outline-info btn-sm add-material-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#addMaterialModal"
                                                            data-minitest-id="{{ $miniTest->maMT }}"
                                                            data-minitest-title="{{ $miniTest->title }}">
                                                        <i class="bi bi-paperclip me-1"></i> Thêm tài liệu
                                                    </button>
                                                </div>
                                                <div class="btn-group">
                                                    @if($miniTest->is_published)
                                                        <form action="{{ route('teacher.minitests.unpublish', $miniTest->maMT) }}" method="POST">
                                                            @csrf
                                                            <button class="btn btn-outline-warning btn-sm" type="submit">
                                                                <i class="bi bi-eye-slash me-1"></i> Hủy công bố
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('teacher.minitests.publish', $miniTest->maMT) }}" method="POST">
                                                            @csrf
                                                            <button class="btn btn-outline-success btn-sm" type="submit">
                                                                <i class="bi bi-broadcast me-1"></i> Công bố
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <form action="{{ route('teacher.minitests.destroy', $miniTest->maMT) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Bạn chắc chắn muốn xóa mini-test này?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-outline-danger btn-sm" type="submit">
                                                            <i class="bi bi-trash me-1"></i> Xóa
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            <div class="materials-block">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="bi bi-folder me-2 text-primary"></i>
                                                    <strong>Tài liệu đính kèm</strong>
                                                </div>
                                                @if($miniTest->materials->isEmpty())
                                                    <p class="text-muted small mb-0">Chưa có tài liệu nào.</p>
                                                @else
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @foreach($miniTest->materials as $material)
                                                            <div class="material-chip">
                                                                <i class="{{ $material->getIcon() }} me-1"></i>
                                                                <a href="{{ $material->public_url }}" target="_blank">{{ Str::limit($material->name, 28) }}</a>
                                                                <form action="{{ route('teacher.minitests.materials.destroy', $material->id) }}"
                                                                      method="POST"
                                                                      onsubmit="return confirm('Xóa tài liệu này?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-link text-danger p-0 ms-1">
                                                                        <i class="bi bi-x-circle"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-muted text-center py-4">
                                            <i class="bi bi-folder2-open fs-3 d-block mb-2"></i>
                                            Chưa có mini-test trong chương này.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @include('Teacher.partials.minitest-modals', ['courses' => $courses, 'activeCourse' => $activeCourse ?? null])
        @endif
    @elseif($type === 'questions' && isset($miniTest))
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="{{ route('teacher.minitests.index', ['course' => $miniTest->maKH, '_fragment' => 'minitest-' . $miniTest->maMT]) }}"
                   class="btn btn-link px-0 text-decoration-none">
                    <i class="bi bi-arrow-left-circle me-2"></i>Quay lại danh sách mini-test
                </a>
                <h1 class="h3 mt-2 mb-1">{{ $miniTest->title }}</h1>
                <div class="text-muted small">
                    <i class="bi bi-bookmark me-1"></i>{{ $miniTest->chapter->tenChuong ?? 'Chưa xác định' }}
                    <span class="ms-3"><i class="bi bi-list-check me-1"></i>{{ $miniTest->questions->count() }} câu hỏi</span>
                    <span class="ms-3"><i class="bi bi-clock-history me-1"></i>{{ $miniTest->time_limit_min ?: 'Không giới hạn' }} phút</span>
                </div>
            </div>
            <span class="badge {{ $skillBadges[$miniTest->skill_type]['class'] ?? 'bg-primary' }}">
                <i class="bi {{ $skillBadges[$miniTest->skill_type]['icon'] ?? 'bi-puzzle' }}"></i>
                {{ $skillBadges[$miniTest->skill_type]['label'] ?? $miniTest->skill_type }}
            </span>
        </div>

        <div class="alert alert-secondary border-0 shadow-sm">
            <strong>Lưu ý:</strong>
            @switch($miniTest->skill_type)
                @case(MiniTest::SKILL_LISTENING)
                    Câu hỏi Listening hỗ trợ audio và các lựa chọn trắc nghiệm.
                    @break
                @case(MiniTest::SKILL_READING)
                    Bạn có thể đính kèm hình ảnh hoặc PDF để hiển thị đoạn văn/đề bài.
                    @break
                @case(MiniTest::SKILL_WRITING)
                    Dạng tự luận, học viên nhập bài viết và chờ chấm điểm.
                    @break
                @case(MiniTest::SKILL_SPEAKING)
                    Học viên sẽ nộp file ghi âm (.mp3). Hãy cung cấp đề bài rõ ràng và tài liệu tham chiếu nếu cần.
                    @break
            @endswitch
        </div>

        <div class="row">
            <div class="col-lg-8">
                <form id="questionsForm"
                      action="{{ route('teacher.minitests.questions.store', $miniTest->maMT) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="question-collection">
                        @forelse($miniTest->questions as $index => $question)
                            @php
                                $correctAnswers = array_filter(array_map('trim', explode(';', (string) $question->dapAnDung)));
                            @endphp
                            <div class="question-card" data-question-index="{{ $index }}">
                                <div class="question-header">
                                    <span class="question-number">Câu {{ $index + 1 }}</span>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-question">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Nội dung câu hỏi <span class="text-danger">*</span></label>
                                    <textarea name="questions[{{ $index }}][content]" class="form-control" rows="3" required>{{ $question->noiDungCauHoi }}</textarea>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Loại câu hỏi <span class="text-danger">*</span></label>
                                        <select name="questions[{{ $index }}][type]" class="form-select question-type" required>
                                            <option value="single_choice" @selected($question->loai === MiniTestQuestion::TYPE_SINGLE_CHOICE)>Trắc nghiệm (một đáp án)</option>
                                            <option value="multiple_choice" @selected($question->loai === MiniTestQuestion::TYPE_MULTIPLE_CHOICE)>Trắc nghiệm (nhiều đáp án)</option>
                                            <option value="true_false" @selected($question->loai === MiniTestQuestion::TYPE_TRUE_FALSE)>Đúng / Sai</option>
                                            <option value="essay" @selected($question->loai === MiniTestQuestion::TYPE_ESSAY)>Tự luận</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Điểm <span class="text-danger">*</span></label>
                                        <input type="number" name="questions[{{ $index }}][points]" class="form-control" step="0.1" min="0" value="{{ $question->diem }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Thứ tự</label>
                                        <input type="number" name="questions[{{ $index }}][order]" class="form-control" min="1" value="{{ $question->thuTu }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Giải thích / Ghi chú (tùy chọn)</label>
                                    <textarea name="questions[{{ $index }}][explanation]" class="form-control" rows="2">{{ $question->giaiThich }}</textarea>
                                </div>

                                <div class="choice-section" data-choice style="{{ in_array($question->loai, [MiniTestQuestion::TYPE_ESSAY, MiniTestQuestion::TYPE_TRUE_FALSE]) ? 'display:none;' : '' }}">
                                    <h6 class="mb-3">Phương án trả lời</h6>
                                    @foreach(['A', 'B', 'C', 'D'] as $option)
                                        <div class="answer-item mb-3">
                                            <div class="input-group">
                                                <span class="input-group-text">{{ $option }}</span>
                                                <input type="text"
                                                       name="questions[{{ $index }}][options][{{ $option }}]"
                                                       class="form-control"
                                                       value="{{ $question->{'phuongAn' . $option} }}"
                                                       placeholder="Nội dung đáp án {{ $option }}">
                                            </div>
                                            <div class="form-check mt-2">
                                                <input type="checkbox"
                                                       class="form-check-input correct-answer"
                                                       name="questions[{{ $index }}][correct][]"
                                                       value="{{ $option }}"
                                                       @checked(in_array($option, $correctAnswers, true))>
                                                <label class="form-check-label">Đáp án đúng</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="true-false-section" data-true-false style="{{ $question->loai === MiniTestQuestion::TYPE_TRUE_FALSE ? '' : 'display:none;' }}">
                                    <h6 class="mb-3">Chọn đáp án đúng</h6>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input"
                                                   type="radio"
                                                   name="questions[{{ $index }}][correct]"
                                                   value="TRUE"
                                                   @checked(in_array('TRUE', $correctAnswers, true))>
                                            <label class="form-check-label">TRUE</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input"
                                                   type="radio"
                                                   name="questions[{{ $index }}][correct]"
                                                   value="FALSE"
                                                   @checked(in_array('FALSE', $correctAnswers, true))>
                                            <label class="form-check-label">FALSE</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="essay-hint" data-essay style="{{ $question->loai === MiniTestQuestion::TYPE_ESSAY ? '' : 'display:none;' }}">
                                    <div class="alert alert-light border">
                                        Dạng tự luận không có đáp án đúng. Học viên sẽ nộp bài và chờ bạn chấm điểm.
                                    </div>
                                </div>

                                <div class="media-upload-section">
                                    <h6 class="mb-3">Tài liệu đính kèm</h6>
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <label class="btn btn-outline-primary btn-sm mb-0">
                                            <i class="bi bi-volume-up me-1"></i> Audio
                                            <input type="file" name="questions[{{ $index }}][audio]" accept="audio/mp3,audio/mpeg,audio/wav" class="d-none">
                                        </label>
                                        <label class="btn btn-outline-primary btn-sm mb-0">
                                            <i class="bi bi-image me-1"></i> Hình ảnh
                                            <input type="file" name="questions[{{ $index }}][image]" accept="image/*" class="d-none">
                                        </label>
                                        <label class="btn btn-outline-primary btn-sm mb-0">
                                            <i class="bi bi-file-pdf me-1"></i> PDF
                                            <input type="file" name="questions[{{ $index }}][pdf]" accept="application/pdf" class="d-none">
                                        </label>
                                    </div>
                                    <div class="existing-media">
                                        @if($question->audio_url)
                                            <div class="existing-file">
                                                <i class="bi bi-music-note-beamed text-primary me-1"></i>
                                                <a href="{{ $question->audio_url }}" target="_blank">Nghe audio hiện tại</a>
                                            </div>
                                        @endif
                                        @if($question->image_url)
                                            <div class="existing-file">
                                                <i class="bi bi-image text-success me-1"></i>
                                                <a href="{{ $question->image_url }}" target="_blank">Xem hình ảnh</a>
                                            </div>
                                        @endif
                                        @if($question->pdf_url)
                                            <div class="existing-file">
                                                <i class="bi bi-file-earmark-pdf text-danger me-1"></i>
                                                <a href="{{ $question->pdf_url }}" target="_blank">Xem PDF</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted text-center py-4">
                                <i class="bi bi-ui-checks-grid fs-3 d-block mb-2"></i>
                                Chưa có câu hỏi nào. Nhấn <strong>Thêm câu hỏi</strong> để bắt đầu.
                            </div>
                        @endforelse
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <button type="button" class="btn btn-outline-primary" id="addQuestionBtn">
                            <i class="bi bi-plus-circle me-2"></i>Thêm câu hỏi
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-cloud-upload me-2"></i>Lưu câu hỏi
                        </button>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <aside class="sticky-sidebar">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="mb-3"><i class="bi bi-bar-chart me-2"></i>Tổng quan</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Số câu hỏi</span>
                                <strong id="questionCount">{{ $miniTest->questions->count() }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tổng điểm</span>
                                <strong id="questionTotalPoints">{{ number_format($miniTest->questions->sum('diem'), 1) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Thời lượng</span>
                                <strong>{{ $miniTest->time_limit_min ?: 'Không giới hạn' }} phút</strong>
                            </div>
                            <hr>
                            <h6 class="mb-2"><i class="bi bi-lightbulb me-2"></i>Hướng dẫn nhanh</h6>
                            <ul class="small text-muted mb-0">
                                <li>Trắc nghiệm nhiều đáp án: đánh dấu tất cả đáp án đúng.</li>
                                <li>Đúng/Sai: chỉ chọn TRUE hoặc FALSE.</li>
                                <li>Tự luận: không cần thiết lập đáp án đúng.</li>
                                <li>Đính kèm audio (mp3/wav) cho Listening, Speaking.</li>
                            </ul>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
        <template id="questionTemplate">
            <div class="question-card" data-question-index="__INDEX__">
                <div class="question-header">
                    <span class="question-number">Câu __NUMBER__</span>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-question">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nội dung câu hỏi <span class="text-danger">*</span></label>
                    <textarea name="questions[__INDEX__][content]" class="form-control" rows="3" required></textarea>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Loại câu hỏi <span class="text-danger">*</span></label>
                        <select name="questions[__INDEX__][type]" class="form-select question-type" required>
                            <option value="single_choice">Trắc nghiệm (một đáp án)</option>
                            <option value="multiple_choice">Trắc nghiệm (nhiều đáp án)</option>
                            <option value="true_false">Đúng / Sai</option>
                            <option value="essay">Tự luận</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Điểm <span class="text-danger">*</span></label>
                        <input type="number" name="questions[__INDEX__][points]" class="form-control" step="0.1" min="0" value="1" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Giải thích / Ghi chú</label>
                    <textarea name="questions[__INDEX__][explanation]" class="form-control" rows="2"></textarea>
                </div>

                <div class="choice-section" data-choice>
                    <h6 class="mb-3">Phương án trả lời</h6>
                    @foreach(['A', 'B', 'C', 'D'] as $option)
                        <div class="answer-item mb-3">
                            <div class="input-group">
                                <span class="input-group-text">{{ $option }}</span>
                                <input type="text"
                                       name="questions[__INDEX__][options][{{ $option }}]"
                                       class="form-control"
                                       placeholder="Nội dung đáp án {{ $option }}">
                            </div>
                            <div class="form-check mt-2">
                                <input type="checkbox"
                                       class="form-check-input correct-answer"
                                       name="questions[__INDEX__][correct][]"
                                       value="{{ $option }}">
                                <label class="form-check-label">Đáp án đúng</label>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="true-false-section" data-true-false style="display:none;">
                    <h6 class="mb-3">Chọn đáp án đúng</h6>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="radio"
                                   name="questions[__INDEX__][correct]"
                                   value="TRUE">
                            <label class="form-check-label">TRUE</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="radio"
                                   name="questions[__INDEX__][correct]"
                                   value="FALSE">
                            <label class="form-check-label">FALSE</label>
                        </div>
                    </div>
                </div>

                <div class="essay-hint" data-essay style="display:none;">
                    <div class="alert alert-light border">
                        Dạng tự luận – học viên sẽ nhập câu trả lời và chờ chấm điểm thủ công.
                    </div>
                </div>

                <div class="media-upload-section">
                    <h6 class="mb-3">Tài liệu đính kèm</h6>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <label class="btn btn-outline-primary btn-sm mb-0">
                            <i class="bi bi-volume-up me-1"></i> Audio
                            <input type="file" name="questions[__INDEX__][audio]" accept="audio/mp3,audio/mpeg,audio/wav" class="d-none">
                        </label>
                        <label class="btn btn-outline-primary btn-sm mb-0">
                            <i class="bi bi-image me-1"></i> Hình ảnh
                            <input type="file" name="questions[__INDEX__][image]" accept="image/*" class="d-none">
                        </label>
                        <label class="btn btn-outline-primary btn-sm mb-0">
                            <i class="bi bi-file-pdf me-1"></i> PDF
                            <input type="file" name="questions[__INDEX__][pdf]" accept="application/pdf" class="d-none">
                        </label>
                    </div>
                </div>
            </div>
        </template>
    @endif
@endsection

@push('scripts')
    <script src="{{ asset('js/Teacher/minitests.js') }}"></script>
@endpush


