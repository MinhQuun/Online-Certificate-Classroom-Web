<?php

namespace App\Services;

use App\Mail\ActivationCodeMail;
use App\Models\ActivationCode;
use App\Models\Combo;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\InvoiceComboItem;
use App\Models\InvoiceItem;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CheckoutOrderService
{
    private ?bool $invoiceNotesSupported = null;

    /**
     * @param array{
     *     course_price_overrides?: array<int,int>,
     *     combo_price_overrides?: array<int,int>,
     *     combo_promotion_overrides?: array<int,?int>
     * } $options
     */
    public function finalize(User $user, Collection $courses, Collection $combos, string $method, array $options = []): array
    {
        $student = $user->student;

        if (!$student) {
            throw new \RuntimeException('Không tìm thấy hồ sơ học viên.');
        }

        $now = Carbon::now();
        $invoice = null;
        $activationPackages = [];
        $pendingCourses = [];
        $alreadyActiveCourses = [];

        $coursePriceOverrides = $options['course_price_overrides'] ?? [];
        $comboPriceOverrides = $options['combo_price_overrides'] ?? [];
        $comboPromotionOverrides = $options['combo_promotion_overrides'] ?? [];

        $coursePayloads = $this->buildCoursePayloads($courses, $combos, $comboPromotionOverrides);

        $totalCoursePrice = (int) $courses->sum(function (Course $course) use ($coursePriceOverrides) {
            return $this->resolveCoursePrice($course, $coursePriceOverrides);
        });

        $totalComboPrice = (int) $combos->sum(function (Combo $combo) use ($comboPriceOverrides) {
            return $this->resolveComboPrice($combo, $comboPriceOverrides);
        });

        DB::transaction(function () use (
            $student,
            $courses,
            $combos,
            $method,
            $coursePayloads,
            $totalCoursePrice,
            $totalComboPrice,
            $coursePriceOverrides,
            $comboPriceOverrides,
            $comboPromotionOverrides,
            $now,
            &$invoice,
            &$activationPackages,
            &$pendingCourses,
            &$alreadyActiveCourses
        ) {
            $invoiceData = [
                'maHV' => $student->maHV,
                'maTT' => $this->mapPaymentMethodToCode($method),
                'maND' => null,
                'ngayLap' => $now,
                'tongTien' => $totalCoursePrice + $totalComboPrice,
                'loai' => $combos->isNotEmpty() ? 'COMBO' : 'SINGLE_COURSE',
                'trangThai' => 'PAID',
            ];

            if ($this->invoiceSupportsNotes()) {
                $invoiceData['ghiChu'] = 'Thanh toán qua website - ' . strtoupper($method);
            }

            $invoice = Invoice::create($invoiceData);

            foreach ($courses as $course) {
                InvoiceItem::create([
                    'maHD' => $invoice->maHD,
                    'maKH' => $course->maKH,
                    'soLuong' => 1,
                    'donGia' => $this->resolveCoursePrice($course, $coursePriceOverrides),
                ]);
            }

            foreach ($combos as $combo) {
                $promotionId = $this->resolveComboPromotion($combo, $comboPromotionOverrides);

                InvoiceComboItem::create([
                    'maHD' => $invoice->maHD,
                    'maGoi' => $combo->maGoi,
                    'soLuong' => 1,
                    'donGia' => $this->resolveComboPrice($combo, $comboPriceOverrides),
                    'maKM' => $promotionId,
                ]);
            }

            foreach ($coursePayloads as $payload) {
                /** @var Course $course */
                $course = $payload['course'];

                $enrollment = Enrollment::firstOrNew([
                    'maHV' => $student->maHV,
                    'maKH' => $course->maKH,
                ]);

                if ($enrollment->exists && $enrollment->trangThai === 'ACTIVE') {
                    $alreadyActiveCourses[$course->maKH] = [
                        'maKH' => $course->maKH,
                        'tenKH' => $course->tenKH,
                    ];
                    continue;
                }

                if (!$enrollment->exists || empty($enrollment->ngayNhapHoc)) {
                    $enrollment->ngayNhapHoc = $now->toDateString();
                }

                if (!$enrollment->exists) {
                    $enrollment->progress_percent = 0;
                    $enrollment->video_progress_percent = 0;
                    $enrollment->avg_minitest_score = 0;
                    $enrollment->last_lesson_id = null;
                }

                $enrollment->trangThai = 'PENDING';
                $enrollment->activated_at = null;
                $enrollment->expires_at = null;
                $enrollment->maGoi = $payload['combo_id'];
                $enrollment->maKM = $payload['promotion_id'];
                $enrollment->updated_at = $now;
                $enrollment->save();

                ActivationCode::where('maHV', $student->maHV)
                    ->where('maKH', $course->maKH)
                    ->whereIn('trangThai', ['CREATED', 'SENT'])
                    ->update([
                        'trangThai' => 'EXPIRED',
                        'expires_at' => $now,
                        'updated_at' => $now,
                    ]);

                $codeValue = $this->generateActivationCode($student->maHV, $course->maKH);

                $activation = ActivationCode::create([
                    'maHV' => $student->maHV,
                    'maKH' => $course->maKH,
                    'maHD' => $invoice->maHD,
                    'code' => $codeValue,
                    'trangThai' => 'CREATED',
                    'generated_at' => $now,
                ]);

                $activationPackages[] = [
                    'model' => $activation,
                    'course' => $course,
                    'code' => $codeValue,
                ];

                $pendingCourses[$course->maKH] = [
                    'maKH' => $course->maKH,
                    'tenKH' => $course->tenKH,
                ];
            }
        });

        return [
            'invoice' => $invoice,
            'student' => $student,
            'user' => $user,
            'activation_packages' => $activationPackages,
            'pending_activation_courses' => array_values($pendingCourses),
            'already_active_courses' => array_values($alreadyActiveCourses),
            'course_total' => $totalCoursePrice,
            'combo_total' => $totalComboPrice,
        ];
    }

    public function dispatchActivationEmails(User $user, Student $student, array $packages): void
    {
        if (empty($packages)) {
            return;
        }

        $courseCodes = [];
        foreach ($packages as $package) {
            $course = $package['course'];
            $courseCodes[] = [
                'course_name' => $course->tenKH,
                'code' => $package['code'],
            ];
        }

        Mail::to($user->email)->send(new ActivationCodeMail($user->hoTen, $courseCodes));

        $ids = array_map(
            fn ($package) => $package['model']->id ?? null,
            $packages
        );
        $ids = array_filter($ids);

        if (!empty($ids)) {
            $timestamp = Carbon::now();
            ActivationCode::whereIn('id', $ids)->update([
                'trangThai' => 'SENT',
                'sent_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }
    }

    private function buildCoursePayloads(Collection $courses, Collection $combos, array $comboPromotionOverrides): array
    {
        $payloads = [];

        foreach ($courses as $course) {
            $payloads[$course->maKH] = [
                'course' => $course,
                'combo_id' => null,
                'promotion_id' => null,
            ];
        }

        foreach ($combos as $combo) {
            $promotionId = $this->resolveComboPromotion($combo, $comboPromotionOverrides);

            foreach ($combo->courses as $course) {
                $payloads[$course->maKH] = [
                    'course' => $course,
                    'combo_id' => $combo->maGoi,
                    'promotion_id' => $promotionId,
                ];
            }
        }

        return $payloads;
    }

    private function resolveCoursePrice(Course $course, array $overrides): int
    {
        if (array_key_exists($course->maKH, $overrides)) {
            return max(0, (int) $overrides[$course->maKH]);
        }

        return max(0, (int) $course->hocPhi);
    }

    private function resolveComboPrice(Combo $combo, array $overrides): int
    {
        if (array_key_exists($combo->maGoi, $overrides)) {
            return max(0, (int) $overrides[$combo->maGoi]);
        }

        return max(0, (int) $combo->sale_price);
    }

    private function resolveComboPromotion(Combo $combo, array $overrides): ?int
    {
        if (array_key_exists($combo->maGoi, $overrides)) {
            return $overrides[$combo->maGoi];
        }

        return $combo->active_promotion?->maKM;
    }

    private function generateActivationCode(int $studentId, int $courseId): string
    {
        do {
            $code = sprintf(
                'OCC-%04d-%04d-%s',
                $courseId % 10000,
                $studentId % 10000,
                Str::upper(Str::random(4))
            );
        } while (ActivationCode::where('code', $code)->exists());

        return $code;
    }

    private function mapPaymentMethodToCode(string $method): string
    {
        $map = [
            'qr' => 'TT02',
            'bank' => 'TT01',
            'visa' => 'TT03',
            'vnpay' => 'TT04',
        ];

        return $map[$method] ?? 'TT01';
    }

    private function invoiceSupportsNotes(): bool
    {
        if ($this->invoiceNotesSupported !== null) {
            return $this->invoiceNotesSupported;
        }

        try {
            $this->invoiceNotesSupported = Schema::hasColumn('HOADON', 'ghiChu');
        } catch (\Throwable $e) {
            $this->invoiceNotesSupported = false;
        }

        return $this->invoiceNotesSupported;
    }
}
