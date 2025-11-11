<?php

namespace App\Jobs;

use App\Models\Enrollment;
use App\Services\CertificateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutoIssueCertificatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(CertificateService $certificateService): void
    {
        $comboPairs = [];

        Enrollment::query()
            ->with(['student', 'course', 'course.certificateTemplate', 'combo'])
            ->where('trangThai', 'ACTIVE')
            ->orderBy('maHV')
            ->orderBy('maKH')
            ->chunk(200, function ($enrollments) use ($certificateService, &$comboPairs) {
                foreach ($enrollments as $enrollment) {
                    /** @var Enrollment $enrollment */
                    $certificateService->issueCourseCertificateIfEligible($enrollment);

                    if ($enrollment->maGoi && $enrollment->student && $enrollment->combo) {
                        $comboPairs[$enrollment->maHV . ':' . $enrollment->maGoi] = [
                            $enrollment->student,
                            $enrollment->combo,
                        ];
                    }
                }
            });

        foreach ($comboPairs as $pair) {
            [$student, $combo] = $pair;
            $certificateService->issueComboCertificateIfEligible($student, $combo);
        }
    }
}
