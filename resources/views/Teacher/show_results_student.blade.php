@extends('layouts.teacher')

@section('title', 'Chi tiết Kết quả Mini-Test')

@push('styles')
{{-- Thêm CSS tùy chỉnh nếu cần --}}
<style>
    .question-block {
        background-color: #f8f9fa;
        border-radius: 8px;
    }
    .answer-block {
        background-color: #fff;
    }
</style>
@endpush

@section('content')
    <section class="page-header">
        <span class="kicker">Giảng viên</span>
        <h1 class="title">
            <i class="bi bi-clipboard-check me-2"></i>
            Chi tiết Kết quả
        </h1>
        <p class="muted">
            Xem lại bài làm của học viên: <strong>{{ $result->student->user->name }}</strong>
        </p>
    </section>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $result->miniTest->title }}</h5>
                    <span class="badge {{ $result->is_fully_graded ? 'badge-graded' : 'badge-pending' }}">
                        @if($result->is_fully_graded)
                            <i class="bi bi-check-circle-fill me-1"></i>Đã chấm
                        @else
                            <i class="bi bi-hourglass-split me-1"></i>Chờ chấm
                        @endif
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Học viên:</strong> {{ $result->student->user->name }}</p>
                            <p class="mb-0"><strong>Email:</strong> {{ $result->student->user->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Khóa học:</strong> {{ $result->miniTest->course->tenKH }}</p>
                            <p class="mb-0"><strong>Chương:</strong> {{ $result->miniTest->chapter->tenChuong }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Nộp lúc:</strong> {{ $result->nop_luc->format('d/m/Y H:i') }}</p>
                            <p class="mb-0"><strong>Lần nộp thứ:</strong> {{ $result->attempt_no }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <span class="text-muted">Điểm</span>
                            <h2 class="fw-bold text-primary d-inline-block mb-0 ms-2">
                                {{ number_format($result->diem, 1) }} / {{ $result->miniTest->max_score }}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Bài làm chi tiết</h5>
                </div>
                <div class="card-body">
                    @if($result->studentAnswers->isEmpty())
                        <div class="text-center p-4">
                            <i class="bi bi-info-circle fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Không tìm thấy chi tiết bài làm cho lần nộp này.</p>
                        </div>
                    @else
                        @foreach($result->studentAnswers as $index => $answer)
                            <div class="mb-4">
                                <div class="p-3 border rounded question-block">
                                    <div class="d-flex justify-content-between">
                                        <strong class="text-dark">Câu {{ $index + 1 }}:</strong>
                                        <span class="fw-bold text-primary">
                                            {{ $answer->score ?? 0 }} / {{ $answer->question->points }} điểm
                                        </span>
                                    </div>
                                    <div class="mt-2">{!! $answer->question->question_text !!}</div>
                                </div>

                                <div class="p-3 border border-top-0 rounded-bottom answer-block">
                                    <strong class="text-muted">Câu trả lời của học viên:</strong>

                                    @if(in_array($answer->question->question_type, ['WRITING', 'SPEAKING']))
                                        @if($answer->answer_file)
                                            <div class="mt-2">
                                                @if($answer->question->question_type == 'SPEAKING')
                                                    <audio controls src="{{ Storage::url($answer->answer_file) }}" class="w-100">
                                                        Trình duyệt không hỗ trợ file audio.
                                                    </audio>
                                                @else
                                                    <a href="{{ Storage::url($answer->answer_file) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                                        <i class="bi bi-download me-1"></i> Tải file bài làm
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                        <div class="mt-2 fst-italic bg-light p-2 rounded">
                                            {!! nl2br(e($answer->answer_text)) ?: '<i>Không có câu trả lời dạng văn bản.</i>' !!}
                                        </div>
                                    @else
                                        <p class="mb-0 mt-1"><em>{{ $answer->answer_text ?? 'Chưa trả lời' }}</em></p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Lịch sử làm bài</h5>
                    <small class="text-muted">Tất cả các lần nộp của học viên</S>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($allAttempts as $attempt)
                        <a href="{{ route('teacher.results.show', $attempt->maKQDG) }}"
                           class="list-group-item list-group-item-action {{ $attempt->maKQDG == $result->maKQDG ? 'active' : '' }}">

                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 fw-bold">Lần làm bài {{ $attempt->attempt_no }}</h6>
                                <small>{{ $attempt->nop_luc->format('d/m/Y') }}</small>
                            </div>
                            <p class="mb-1">Điểm: <strong>{{ $attempt->diem }} / {{ $attempt->miniTest->max_score }}</strong></p>
                            <small class="{{ $attempt->maKQDG == $result->maKQDG ? '' : 'text-muted' }}">
                                {{ $attempt->nop_luc->format('H:i') }}
                                @if($attempt->is_fully_graded)
                                    (Đã chấm)
                                @else
                                    (Chờ chấm)
                                @endif
                            </small>
                        </a>
                    @empty
                        <div class="list-group-item">
                            <p class="text-muted mb-0">Chưa có lịch sử làm bài.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
