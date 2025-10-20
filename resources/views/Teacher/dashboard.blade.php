@extends('layouts.teacher')
@section('title','Bảng điều khiển - Giảng viên')

@section('content')
    <section class="page-header">
        <span class="kicker">Giảng viên</span>
        <h1 class="title">Bảng điều khiển</h1>
        <p class="muted">Tổng quan nhanh và lối tắt thao tác.</p>
    </section>

    <div class="row g-3 mb-4 stat-row">
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('teacher.lectures.index') }}" class="s-card text-decoration-none">
                <div class="icon"><i class="bi bi-book"></i></div>
                <div class="meta">
                    <div class="n">{{ $stats['lectures'] ?? 0 }}</div>
                    <div class="t">Bài giảng</div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-lg-3">
            <a href="{{ route('teacher.progress.index') }}" class="s-card text-decoration-none">
                <div class="icon"><i class="bi bi-people"></i></div>
                <div class="meta">
                    <div class="n">{{ $stats['students'] ?? 0 }}</div>
                    <div class="t">Học viên đang học</div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-lg-3">
            <a href="{{ route('teacher.lectures.index') }}?filter=assignments" class="s-card text-decoration-none">
                <div class="icon"><i class="bi bi-pencil-square"></i></div>
                <div class="meta">
                    <div class="n">{{ $stats['assignments_pending'] ?? 0 }}</div>
                    <div class="t">Bài tập chờ chấm</div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-lg-3">
            <a href="{{ route('teacher.exams.index') }}" class="s-card text-decoration-none">
                <div class="icon"><i class="bi bi-clipboard-check"></i></div>
                <div class="meta">
                    <div class="n">{{ $stats['exams_upcoming'] ?? 0 }}</div>
                    <div class="t">Kỳ thi sắp tới</div>
                </div>
            </a>
        </div>
    </div>

    @php
        $assignmentsPending   = $stats['assignments_pending'] ?? 0;
        $examsPending         = $stats['exams_pending'] ?? 0;
        $lowProgressStudents  = $badges['low_progress'] ?? 0;
    @endphp

    <div class="card quick-links">
        <div class="card-body">
            <h5 class="mb-3">Tác vụ nhanh</h5>
            <div class="d-flex flex-wrap gap-2">
                {{-- Tác vụ nhanh --}}
                <a href="{{ route('teacher.lectures.index') }}?action=create" class="chip">
                    <i class="bi bi-book me-1"></i> Tạo bài giảng mới
                </a>
                <a href="{{ route('teacher.progress.index') }}" class="chip">
                    <i class="bi bi-graph-up me-1"></i> Cập nhật tiến độ
                </a>
                <a href="{{ route('teacher.exams.index') }}?action=create" class="chip">
                    <i class="bi bi-clipboard-check me-1"></i> Lập kế hoạch kỳ thi
                </a>

                {{-- Hàng đợi / cảnh báo --}}
                @if($assignmentsPending > 0)
                    <a href="{{ route('teacher.lectures.index') }}?filter=assignments" class="chip">
                        <i class="bi bi-pencil-square me-1"></i>
                        Bài tập chờ chấm
                        <span class="badge text-bg-warning ms-1">{{ $assignmentsPending }}</span>
                    </a>
                @endif

                @if($examsPending > 0)
                    <a href="{{ route('teacher.exams.index') }}?status=pending" class="chip">
                        <i class="bi bi-clipboard-check me-1"></i>
                        Kỳ thi chưa sắp xếp
                        <span class="badge text-bg-secondary ms-1">{{ $examsPending }}</span>
                    </a>
                @endif

                @if($lowProgressStudents > 0)
                    <a href="{{ route('teacher.progress.index') }}?filter=low" class="chip">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Học viên tiến độ thấp
                        <span class="badge text-bg-danger ms-1">{{ $lowProgressStudents }}</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
@endsection
