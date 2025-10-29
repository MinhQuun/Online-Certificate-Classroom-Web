@extends('layouts.teacher')

@section('title', 'Quản lý Mini-Test')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px 30px;
        border-radius: 16px;
        margin-bottom: 30px;
    }
    .course-selector-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 25px;
    }
    .minitest-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s;
        border-left: 5px solid #4285f4;
    }
    .minitest-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    .skill-badge {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 13px;
    }
    .skill-LISTENING { background: #e3f2fd; color: #1976d2; }
    .skill-SPEAKING { background: #f3e5f5; color: #7b1fa2; }
    .skill-READING { background: #e8f5e9; color: #388e3c; }
    .skill-WRITING { background: #fff3e0; color: #f57c00; }
    .minitest-info {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        margin-top: 15px;
        color: #666;
        font-size: 14px;
    }
    .minitest-info > span {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .chapter-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .chapter-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #dee2e6;
    }
    .stats-box {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
    }
    .stat-item {
        flex: 1;
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        text-align: center;
    }
    .stat-value {
        font-size: 32px;
        font-weight: bold;
        color: #4285f4;
    }
    .stat-label {
        color: #666;
        margin-top: 5px;
        font-size: 14px;
    }
    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }
    .btn-manage {
        background: #4285f4;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
    }
    .btn-manage:hover {
        background: #3367d6;
        color: white;
    }
    .btn-publish {
        background: #34a853;
        color: white;
    }
    .btn-publish:hover {
        background: #2d9047;
    }
    .btn-unpublish {
        background: #fbbc04;
        color: #333;
    }
    .btn-edit {
        background: #f8f9fa;
        color: #333;
        border: 1px solid #dee2e6;
    }
    .btn-delete {
        background: #ea4335;
        color: white;
    }
    .btn-delete:hover {
        background: #d33828;
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="page-header">
        <h1 class="mb-2">
            <i class="bi bi-file-earmark-text me-3"></i>Quản lý Mini-Test
        </h1>
        <p class="mb-0 opacity-90">Tạo và quản lý bài kiểm tra kỹ năng cho từng chương học</p>
    </div>

    @if($courses->isEmpty())
        <div class="alert alert-info border-0 shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-info-circle fs-3"></i>
                <div>
                    <h5 class="mb-1">Chưa có khóa học</h5>
                    <p class="mb-0">Bạn chưa được phân công giảng dạy khóa học nào.</p>
                </div>
            </div>
        </div>
    @else
        <!-- Course Selector -->
        <div class="course-selector-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <label class="form-label fw-bold">Chọn khóa học</label>
                    <select id="courseSelector" class="form-select form-select-lg"
                            data-base-url="{{ route('teacher.minitests.index') }}">
                        @foreach($courses as $course)
                            <option value="{{ $course->maKH }}" @selected($activeCourse && $activeCourse->maKH === $course->maKH)>
                                {{ $course->tenKH }} ({{ $course->chapters->count() }} chương)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 text-end mt-3 mt-md-0">
                    <button class="btn btn-primary btn-lg w-100" data-bs-toggle="modal" data-bs-target="#createMiniTestModal">
                        <i class="bi bi-plus-circle me-2"></i> Tạo Mini-Test Mới
                    </button>
                </div>
            </div>
        </div>

        @if($activeCourse)
            @php
                $totalTests = $activeCourse->chapters->sum(fn ($chapter) => $chapter->miniTests->count());
                $publishedTests = $activeCourse->chapters->sum(fn ($chapter) => $chapter->miniTests->where('is_published', true)->count());
                $totalQuestions = $activeCourse->chapters->sum(fn ($chapter) => 
                    $chapter->miniTests->sum(fn($mt) => $mt->questions->count())
                );
            @endphp

            <!-- Statistics -->
            <div class="stats-box">
                <div class="stat-item">
                    <div class="stat-value">{{ $totalTests }}</div>
                    <div class="stat-label">Tổng Mini-Test</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $publishedTests }}</div>
                    <div class="stat-label">Đã Công Bố</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $totalQuestions }}</div>
                    <div class="stat-label">Tổng Câu Hỏi</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $activeCourse->chapters->count() }}</div>
                    <div class="stat-label">Số Chương</div>
                </div>
            </div>

            <!-- Chapters & Mini-Tests -->
            @foreach($activeCourse->chapters as $chapter)
                <div class="chapter-section">
                    <div class="chapter-header">
                        <div>
                            <h4 class="mb-1">
                                <i class="bi bi-book me-2"></i>Chương {{ $chapter->thuTu }}: {{ $chapter->tenChuong }}
                            </h4>
                            <p class="text-muted mb-0">{{ $chapter->miniTests->count() }} mini-test</p>
                        </div>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createMiniTestModal"
                                onclick="setChapterForCreate({{ $chapter->maChuong }}, '{{ $chapter->tenChuong }}')">
                            <i class="bi bi-plus me-2"></i> Thêm Mini-Test
                        </button>
                    </div>

                    @if($chapter->miniTests->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mt-2">Chưa có mini-test nào cho chương này</p>
                        </div>
                    @else
                        @foreach($chapter->miniTests as $miniTest)
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
                            @endphp

                            <div class="minitest-card" id="minitest-{{ $miniTest->maMT }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                            <span class="badge bg-secondary">Test {{ $miniTest->thuTu }}</span>
                                            <span class="skill-badge skill-{{ $miniTest->skill_type }}">
                                                {{ $skillIcons[$miniTest->skill_type] ?? '' }} 
                                                {{ $skillNames[$miniTest->skill_type] ?? $miniTest->skill_type }}
                                            </span>
                                            @if($miniTest->is_published)
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Đã công bố
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-clock"></i> Bản nháp
                                                </span>
                                            @endif
                                        </div>
                                        <h5 class="mb-2">{{ $miniTest->title }}</h5>
                                        <div class="minitest-info">
                                            <span>
                                                <i class="bi bi-question-circle"></i>
                                                <strong>{{ $miniTest->questions->count() }}</strong> câu hỏi
                                            </span>
                                            <span>
                                                <i class="bi bi-clock"></i>
                                                <strong>{{ $miniTest->time_limit_min }}</strong> phút
                                            </span>
                                            <span>
                                                <i class="bi bi-trophy"></i>
                                                <strong>{{ $miniTest->max_score }}</strong> điểm
                                            </span>
                                            <span>
                                                <i class="bi bi-arrow-repeat"></i>
                                                <strong>{{ $miniTest->attempts_allowed }}</strong> lần thử
                                            </span>
                                        </div>
                                        
                                        <div class="action-buttons">
                                            <a href="{{ route('teacher.minitests.questions.form', $miniTest->maMT) }}" 
                                               class="btn btn-manage">
                                                <i class="bi bi-list-check me-1"></i> Quản lý câu hỏi ({{ $miniTest->questions->count() }})
                                            </a>
                                            
                                            @if($miniTest->is_published)
                                                <form action="{{ route('teacher.minitests.unpublish', $miniTest->maMT) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-manage btn-unpublish">
                                                        <i class="bi bi-x-circle me-1"></i> Hủy công bố
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('teacher.minitests.publish', $miniTest->maMT) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Công bố mini-test này? Học viên sẽ có thể thấy và làm bài.')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-manage btn-publish">
                                                        <i class="bi bi-check-circle me-1"></i> Công bố
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <button class="btn btn-manage btn-edit edit-minitest-btn"
                                                    data-minitest-id="{{ $miniTest->maMT }}"
                                                    data-course-id="{{ $activeCourse->maKH }}"
                                                    data-chapter-id="{{ $chapter->maChuong }}"
                                                    data-title="{{ $miniTest->title }}"
                                                    data-skill-type="{{ $miniTest->skill_type }}"
                                                    data-order="{{ $miniTest->thuTu }}"
                                                    data-max-score="{{ $miniTest->max_score }}"
                                                    data-weight="{{ $miniTest->trongSo }}"
                                                    data-time-limit="{{ $miniTest->time_limit_min }}"
                                                    data-attempts="{{ $miniTest->attempts_allowed }}"
                                                    data-is-active="{{ $miniTest->is_active ? '1' : '0' }}">
                                                <i class="bi bi-pencil me-1"></i> Sửa
                                            </button>
                                            
                                            <form action="{{ route('teacher.minitests.destroy', $miniTest->maMT) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Xác nhận xóa mini-test này? Tất cả câu hỏi và kết quả sẽ bị xóa.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-manage btn-delete">
                                                    <i class="bi bi-trash me-1"></i> Xóa
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            @endforeach
        @endif
    @endif

    <!-- Create Mini-Test Modal -->
    <div class="modal fade" id="createMiniTestModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle me-2"></i>Tạo Mini-Test Mới
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('teacher.minitests.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="course_id" id="create_course_id" value="{{ $activeCourse?->maKH }}">
                        
                        <div class="mb-3">
                            <label class="form-label">Chương <span class="text-danger">*</span></label>
                            <select name="chapter_id" id="create_chapter_id" class="form-select" required>
                                <option value="">-- Chọn chương --</option>
                                @if($activeCourse)
                                    @foreach($activeCourse->chapters as $ch)
                                        <option value="{{ $ch->maChuong }}">{{ $ch->thuTu }}. {{ $ch->tenChuong }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="VD: Kiểm tra kỹ năng nghe" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kỹ năng <span class="text-danger">*</span></label>
                            <select name="skill_type" class="form-select" required>
                                <option value="">-- Chọn kỹ năng --</option>
                                <option value="LISTENING">🎧 Nghe (Listening)</option>
                                <option value="SPEAKING">🗣️ Nói (Speaking)</option>
                                <option value="READING">📖 Đọc (Reading)</option>
                                <option value="WRITING">✍️ Viết (Writing)</option>
                            </select>
                            <div class="form-text">
                                <strong>Lưu ý:</strong> Kỹ năng Viết sẽ cần giảng viên chấm điểm thủ công.
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Thời gian (phút)</label>
                                <input type="number" name="time_limit" class="form-control" value="10" min="1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số lần thử</label>
                                <input type="number" name="attempts" class="form-control" value="1" min="1">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Điểm tối đa</label>
                                <input type="number" name="max_score" class="form-control" value="10" step="0.01" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Trọng số (%)</label>
                                <input type="number" name="weight" class="form-control" value="0" step="0.01" min="0" max="100">
                            </div>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="create_is_active" value="1" checked>
                            <label class="form-check-label" for="create_is_active">
                                Kích hoạt ngay
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Tạo Mini-Test
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Mini-Test Modal -->
    <div class="modal fade" id="editMiniTestModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil me-2"></i>Chỉnh sửa Mini-Test
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editMiniTestForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="course_id" id="edit_course_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Chương <span class="text-danger">*</span></label>
                            <select name="chapter_id" id="edit_chapter_id" class="form-select" required>
                                @if($activeCourse)
                                    @foreach($activeCourse->chapters as $ch)
                                        <option value="{{ $ch->maChuong }}">{{ $ch->thuTu }}. {{ $ch->tenChuong }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="edit_title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kỹ năng <span class="text-danger">*</span></label>
                            <select name="skill_type" id="edit_skill_type" class="form-select" required>
                                <option value="LISTENING">🎧 Nghe (Listening)</option>
                                <option value="SPEAKING">🗣️ Nói (Speaking)</option>
                                <option value="READING">📖 Đọc (Reading)</option>
                                <option value="WRITING">✍️ Viết (Writing)</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Thời gian (phút)</label>
                                <input type="number" name="time_limit" id="edit_time_limit" class="form-control" min="1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số lần thử</label>
                                <input type="number" name="attempts" id="edit_attempts" class="form-control" min="1">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Điểm tối đa</label>
                                <input type="number" name="max_score" id="edit_max_score" class="form-control" step="0.01" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Trọng số (%)</label>
                                <input type="number" name="weight" id="edit_weight" class="form-control" step="0.01" min="0" max="100">
                            </div>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="edit_is_active" value="1">
                            <label class="form-check-label" for="edit_is_active">
                                Kích hoạt
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Course selector
    document.getElementById('courseSelector')?.addEventListener('change', function() {
        const baseUrl = this.dataset.baseUrl;
        const courseId = this.value;
        window.location.href = `${baseUrl}?course=${courseId}`;
    });

    // Set chapter for create modal
    function setChapterForCreate(chapterId, chapterName) {
        document.getElementById('create_chapter_id').value = chapterId;
    }

    // Edit minitest
    document.querySelectorAll('.edit-minitest-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const minitestId = this.dataset.minitestId;
            const form = document.getElementById('editMiniTestForm');
            form.action = `/teacher/minitests/${minitestId}`;
            
            document.getElementById('edit_course_id').value = this.dataset.courseId;
            document.getElementById('edit_chapter_id').value = this.dataset.chapterId;
            document.getElementById('edit_title').value = this.dataset.title;
            document.getElementById('edit_skill_type').value = this.dataset.skillType;
            document.getElementById('edit_time_limit').value = this.dataset.timeLimit;
            document.getElementById('edit_attempts').value = this.dataset.attempts;
            document.getElementById('edit_max_score').value = this.dataset.maxScore;
            document.getElementById('edit_weight').value = this.dataset.weight;
            document.getElementById('edit_is_active').checked = this.dataset.isActive === '1';
            
            new bootstrap.Modal(document.getElementById('editMiniTestModal')).show();
        });
    });
</script>
@endpush
