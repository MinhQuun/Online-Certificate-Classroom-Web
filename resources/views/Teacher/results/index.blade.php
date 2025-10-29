@extends('layouts.teacher')

@section('title', 'Điểm học viên')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 16px;
        margin-bottom: 30px;
    }
    .stats-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }
    .stats-value {
        font-size: 32px;
        font-weight: bold;
        color: #667eea;
    }
    .filter-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }
    .result-table {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .badge-graded {
        background: linear-gradient(135deg, #d4edda, #c3e6cb);
        color: #155724;
    }
    .badge-pending {
        background: linear-gradient(135deg, #fff3cd, #ffe8a1);
        color: #856404;
    }
    .score-badge {
        font-size: 16px;
        padding: 8px 14px;
        border-radius: 8px;
        font-weight: 600;
    }
    .score-excellent { background: #d4edda; color: #155724; }
    .score-good { background: #d1ecf1; color: #0c5460; }
    .score-average { background: #fff3cd; color: #856404; }
    .score-poor { background: #f8d7da; color: #721c24; }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="page-header">
        <h3 class="mb-2">
            <i class="bi bi-clipboard-data me-2"></i>Điểm học viên
        </h3>
        <p class="mb-0 opacity-90">Theo dõi kết quả làm bài Mini-Test của học viên</p>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card text-center">
                <i class="bi bi-file-earmark-text text-primary fs-1 mb-2"></i>
                <div class="stats-value">{{ $stats['total_submissions'] }}</div>
                <div class="text-muted">Tổng bài nộp</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center">
                <i class="bi bi-check-circle text-success fs-1 mb-2"></i>
                <div class="stats-value text-success">{{ $stats['fully_graded'] }}</div>
                <div class="text-muted">Đã chấm xong</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center">
                <i class="bi bi-hourglass-split text-warning fs-1 mb-2"></i>
                <div class="stats-value text-warning">{{ $stats['pending_grading'] }}</div>
                <div class="text-muted">Chờ chấm</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center">
                <i class="bi bi-graph-up text-info fs-1 mb-2"></i>
                <div class="stats-value text-info">{{ number_format($stats['avg_score'], 1) }}</div>
                <div class="text-muted">Điểm trung bình</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('teacher.results.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-bold">Khóa học</label>
                <select name="course" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả khóa học</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->maKH }}" {{ $selectedCourseId == $course->maKH ? 'selected' : '' }}>
                            {{ $course->tenKH }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">Mini-Test</label>
                <select name="minitest" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả Mini-Test</option>
                    @foreach($miniTests as $miniTest)
                        <option value="{{ $miniTest->maMT }}" {{ $selectedMiniTestId == $miniTest->maMT ? 'selected' : '' }}>
                            {{ $miniTest->title }} ({{ $miniTest->chapter->tenChuong }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold">Tìm học viên</label>
                <input type="text" name="student" class="form-control" 
                       placeholder="Nhập tên học viên..." 
                       value="{{ $searchStudent }}">
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-2"></i>Tìm kiếm
                </button>
            </div>
        </form>

        @if($selectedCourseId || $selectedMiniTestId || $searchStudent)
            <div class="mt-3">
                <a href="{{ route('teacher.results.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i>Xóa bộ lọc
                </a>
            </div>
        @endif
    </div>

    <!-- Results Table -->
    <div class="result-table">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Học viên</th>
                        <th>Khóa học</th>
                        <th>Mini-Test</th>
                        <th>Kỹ năng</th>
                        <th class="text-center">Điểm</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center">Nộp lúc</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $result)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-person-circle fs-4 text-primary"></i>
                                    <div>
                                        <div class="fw-bold">{{ $result->student->user->name }}</div>
                                        <small class="text-muted">{{ $result->student->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $result->miniTest->course->tenKH }}</div>
                                <small class="text-muted">{{ $result->miniTest->chapter->tenChuong }}</small>
                            </td>
                            <td>{{ $result->miniTest->title }}</td>
                            <td>
                                @php
                                    $skillIcons = ['LISTENING' => '🎧', 'SPEAKING' => '🗣️', 'READING' => '📖', 'WRITING' => '✍️'];
                                    $skillNames = ['LISTENING' => 'Nghe', 'SPEAKING' => 'Nói', 'READING' => 'Đọc', 'WRITING' => 'Viết'];
                                    $skillClass = [
                                        'LISTENING' => 'primary',
                                        'SPEAKING' => 'danger',
                                        'READING' => 'success',
                                        'WRITING' => 'warning'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $skillClass[$result->miniTest->skill_type] ?? 'secondary' }}">
                                    {{ $skillIcons[$result->miniTest->skill_type] ?? '' }}
                                    {{ $skillNames[$result->miniTest->skill_type] ?? $result->miniTest->skill_type }}
                                </span>
                            </td>
                            <td class="text-center">
                                @php
                                    $percentage = ($result->diem / $result->miniTest->max_score) * 100;
                                    $scoreClass = 'score-poor';
                                    if ($percentage >= 80) $scoreClass = 'score-excellent';
                                    elseif ($percentage >= 65) $scoreClass = 'score-good';
                                    elseif ($percentage >= 50) $scoreClass = 'score-average';
                                @endphp
                                <span class="score-badge {{ $scoreClass }}">
                                    {{ number_format($result->diem, 1) }}/{{ $result->miniTest->max_score }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($result->is_fully_graded)
                                    <span class="badge badge-graded">
                                        <i class="bi bi-check-circle-fill me-1"></i>Đã chấm
                                    </span>
                                @else
                                    <span class="badge badge-pending">
                                        <i class="bi bi-hourglass-split me-1"></i>Chờ chấm
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <small>{{ $result->nop_luc->format('d/m/Y H:i') }}</small>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('teacher.results.show', $result->maKQDG) }}" 
                                   class="btn btn-sm btn-outline-primary"
                                   title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(!$result->is_fully_graded)
                                    <a href="{{ route('teacher.grading.show', $result->maKQDG) }}" 
                                       class="btn btn-sm btn-outline-warning ms-1"
                                       title="Chấm điểm">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                <p class="text-muted mb-0">Chưa có kết quả nào</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($results->hasPages())
            <div class="p-3 border-top">
                {{ $results->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection
