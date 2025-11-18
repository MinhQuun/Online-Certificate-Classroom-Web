<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\MiniTest;
use App\Models\MiniTestResult;
use App\Models\Promotion;
use App\Models\StudentNotification;
use App\Models\User;
use Illuminate\Support\Str;

class StudentNotificationService
{
    public function notifyGradedResult(MiniTestResult $result): void
    {
        $result->loadMissing(['student.user', 'miniTest.course']);

        $user = $result->student?->user;
        $course = $result->miniTest?->course;
        $miniTest = $result->miniTest;

        if (!$user || !$miniTest) {
            return;
        }

        $exists = StudentNotification::query()
            ->where('maND', $user->maND)
            ->where('loai', 'GRADE')
            ->where('metadata->result_id', $result->maKQDG)
            ->exists();

        if ($exists) {
            return;
        }

        $skillLabel = $this->skillLabel($miniTest->skill_type);
        $courseName = $course?->tenKH ?? 'Khóa học OCC';

        StudentNotification::create([
            'maND' => $user->maND,
            'maKH' => $course?->maKH,
            'tieuDe' => sprintf('Giảng viên đã chấm %s', $skillLabel),
            'noiDung' => sprintf('Bài %s trong khóa %s đã có điểm và nhận xét. Xem chi tiết để cải thiện.', $skillLabel, $courseName),
            'loai' => 'GRADE',
            'action_url' => route('student.minitests.result', $result->maKQDG),
            'action_label' => 'Xem điểm chi tiết',
            'metadata' => [
                'result_id' => $result->maKQDG,
                'skill' => $miniTest->skill_type,
                'course_name' => $courseName,
            ],
        ]);
    }

    public function notifyCertificateIssued(Certificate $certificate): void
    {
        $certificate->loadMissing(['student.user', 'course']);

        $user = $certificate->student?->user;
        if (!$user) {
            return;
        }

        $course = $certificate->course;
        $courseName = $course?->tenKH ?? 'khóa học';

        $exists = StudentNotification::query()
            ->where('maND', $user->maND)
            ->where('loai', 'COURSE')
            ->where('metadata->certificate_id', $certificate->maCC)
            ->exists();

        if ($exists) {
            return;
        }

        StudentNotification::create([
            'maND' => $user->maND,
            'maKH' => $course?->maKH,
            'tieuDe' => sprintf('Bạn đã được cấp chứng chỉ %s', Str::limit($courseName, 70)),
            'noiDung' => 'Chúc mừng! Bạn đã đủ điều kiện và chứng chỉ đã sẵn sàng trong trang "Chứng chỉ của tôi".',
            'loai' => 'COURSE',
            'action_url' => route('student.certificates.index'),
            'action_label' => 'Xem chứng chỉ của tôi',
            'metadata' => [
                'certificate_id' => $certificate->maCC,
                'course_name' => $courseName,
            ],
        ]);
    }

    public function syncActivePromotionsForUser(User $user): void
    {
        $promotions = Promotion::query()
            ->active()
            ->with(['courses', 'combos'])
            ->latest('ngayBatDau')
            ->limit(5)
            ->get();

        foreach ($promotions as $promotion) {
            $this->notifyPromotion($user, $promotion);
        }
    }

    protected function notifyPromotion(User $user, Promotion $promotion): void
    {
        $exists = StudentNotification::query()
            ->where('maND', $user->maND)
            ->where('loai', 'PROMOTION')
            ->where('metadata->promotion_id', $promotion->maKM)
            ->exists();

        if ($exists) {
            return;
        }

        [$targetName, $actionUrl, $courseId, $comboId] = $this->resolvePromotionTarget($promotion);

        StudentNotification::create([
            'maND' => $user->maND,
            'maKH' => $courseId,
            'maGoi' => $comboId,
            'tieuDe' => sprintf('Ưu đãi mới: %s', $promotion->tenKM),
            'noiDung' => $this->buildPromotionDescription($promotion, $targetName),
            'loai' => 'PROMOTION',
            'action_url' => $actionUrl ?? route('student.courses.index'),
            'action_label' => 'Xem ưu đãi',
            'metadata' => [
                'promotion_id' => $promotion->maKM,
                'target_name' => $targetName,
            ],
        ]);
    }

    protected function resolvePromotionTarget(Promotion $promotion): array
    {
        $courseId = null;
        $comboId = null;
        $actionUrl = null;
        $name = 'khóa học/ combo ưu đãi';

        $course = $promotion->courses->first();
        $combo = $promotion->combos->first();

        if ($combo) {
            $comboId = $combo->maGoi;
            $actionUrl = route('student.combos.show', $combo->slug ?? $combo->maGoi);
            $name = $combo->tenGoi ?? 'Combo ưu đãi';
        } elseif ($course) {
            $courseId = $course->maKH;
            $actionUrl = route('student.courses.show', $course->slug ?? $course->maKH);
            $name = $course->tenKH ?? 'Khóa học ưu đãi';
        }

        return [$name, $actionUrl, $courseId, $comboId];
    }

    protected function buildPromotionDescription(Promotion $promotion, string $targetName): string
    {
        $value = (float) ($promotion->giaTriUuDai ?? 0);
        $unit = $promotion->loaiUuDai === Promotion::TYPE_PERCENT ? '%' : 'đ';
        $dateRange = [];

        if ($promotion->ngayBatDau) {
            $dateRange[] = $promotion->ngayBatDau->format('d/m');
        }
        if ($promotion->ngayKetThuc) {
            $dateRange[] = $promotion->ngayKetThuc->format('d/m');
        }

        $rangeText = '';
        if (!empty($dateRange)) {
            $rangeText = ' diễn ra ' . implode(' - ', $dateRange);
        }

        return sprintf('Ưu đãi %s cho %s%s. Số lượng có hạn, áp dụng ngay!', $value . $unit, $targetName, $rangeText);
    }

    public function syncCertificatesForUser(User $user): void
    {
        $certificates = Certificate::query()
            ->whereHas('student', fn ($q) => $q->where('maND', $user->maND))
            ->where('trangThai', Certificate::STATUS_ISSUED)
            ->orderByDesc('issued_at')
            ->limit(10)
            ->with('course')
            ->get();

        foreach ($certificates as $certificate) {
            $exists = StudentNotification::query()
                ->where('maND', $user->maND)
                ->where('loai', 'COURSE')
                ->where('metadata->certificate_id', $certificate->maCC)
                ->exists();

            if ($exists) {
                continue;
            }

            $courseName = $certificate->course?->tenKH ?? 'khóa học';

            StudentNotification::create([
                'maND' => $user->maND,
                'maKH' => $certificate->maKH,
                'tieuDe' => sprintf('Bạn đã được cấp chứng chỉ %s', Str::limit($courseName, 70)),
                'noiDung' => 'Chúc mừng! Chứng chỉ đã sẵn sàng trong trang "Chứng chỉ của tôi".',
                'loai' => 'COURSE',
                'action_url' => route('student.certificates.index'),
                'action_label' => 'Xem chứng chỉ của tôi',
                'metadata' => [
                    'certificate_id' => $certificate->maCC,
                    'course_name' => $courseName,
                ],
            ]);
        }
    }

    protected function skillLabel(?string $skill): string
    {
        return match ($skill) {
            MiniTest::SKILL_LISTENING => 'Listening',
            MiniTest::SKILL_SPEAKING => 'Speaking',
            MiniTest::SKILL_READING => 'Reading',
            MiniTest::SKILL_WRITING => 'Writing',
            default => 'bài tập',
        };
    }
}
