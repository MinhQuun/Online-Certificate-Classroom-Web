<?php

namespace App\Http\Controllers\API\Student\Concerns;

use App\Models\LessonProgress;
use Illuminate\Support\Carbon;

trait HasLessonProgressFormatting
{
    protected function formatProgress(?LessonProgress $progress): array
    {
        if (! $progress) {
            return [
                'status' => 'NOT_STARTED',
            ];
        }

        return [
            'status'                 => $progress->trangThai,
            'total_view_seconds'     => (int) ($progress->thoiGianHoc ?? 0),
            'video_progress_seconds' => (int) ($progress->video_progress_seconds ?? 0),
            'video_duration_seconds' => (int) ($progress->video_duration_seconds ?? 0),
            'watch_count'            => (int) ($progress->soLanXem ?? 0),
            'last_viewed_at'         => $this->formatDateTime($progress->lanXemCuoi),
            'completed_at'           => $this->formatDateTime($progress->completed_at),
            'note'                   => $progress->ghiChu,
        ];
    }

    protected function formatDateTime($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $dateTime = $value instanceof Carbon ? $value : Carbon::parse($value);

        return $dateTime->toIso8601String();
    }
}
