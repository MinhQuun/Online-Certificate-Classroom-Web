@extends('layouts.teacher')

@section('title', 'Chấm điểm Mini-Test')

@push('styles')
    <style>
        .grading-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        .grading-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .student-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        .student-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #4285f4;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
        }
        .pending-badge {
            background: #fff3cd;
            color: #856404;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .metric-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: #4285f4;
        }
        .metric-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
    </style>
@endpush

@section('content')
    <!-- Header -->
    <section class="page-header">
        <span class="kicker">Giảng viên</span>
        <h1 class="title">Chấm điểm Mini-Test</h1>
        <p class="muted">Chấm điểm các câu tự luận cho học viên</p>
    </section>

    <!-- Course Selector -->
    <div class="card border-0 shadow-sm mb-4">
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
                                <div class="metric-label">Trang này</div>
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
                    <h5 class="mb-1">Không có bài cần chấm</h5>
                    <p class="mb-0">Tất cả bài test đã được chấm điểm hoặc chưa có học viên nộp bài.</p>
                </div>
            </div>
        </div>
    @else
        <!-- Results List -->
        @foreach($results as $result)
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
                        <i class="bi bi-clock me-1"></i> Chờ chấm
                    </span>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <strong>Khóa học:</strong><br>
                        {{ $result->miniTest->course->tenKH }}
                    </div>
                    <div class="col-md-4">
                        <strong>Mini-Test:</strong><br>
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
                        <strong>Số câu tự luận:</strong>
                        <span class="badge bg-info">{{ $result->studentAnswers->count() }}</span>
                    </div>
                    <div class="col-md-4">
                        <strong>Lần thử:</strong>
                        <span class="badge bg-secondary">{{ $result->attempt_no }}</span>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('teacher.grading.show', $result->maKQDG) }}" class="btn btn-primary">
                        <i class="bi bi-pencil-square me-2"></i> Chấm điểm
                    </a>
                    <a href="{{ route('teacher.minitests.index', ['course' => $result->miniTest->maKH]) }}" 
                       class="btn btn-outline-secondary">
                        <i class="bi bi-eye me-2"></i> Xem Mini-Test
                    </a>
                </div>
            </div>
        @endforeach

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $results->links() }}
        </div>
    @endif

    @push('scripts')
        <script>
            document.getElementById('courseFilter')?.addEventListener('change', function() {
                const courseId = this.value;
                const url = new URL(window.location.href);
                if (courseId) {
                    url.searchParams.set('course', courseId);
                } else {
                    url.searchParams.delete('course');
                }
                window.location.href = url.toString();
            });
        </script>
    @endpush
@endsection
