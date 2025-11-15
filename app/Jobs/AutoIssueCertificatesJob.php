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
        Enrollment::query()
            ->with(['student', 'course', 'course.certificateTemplate'])
            ->where('trangThai', 'ACTIVE')
            ->orderBy('maHV')
            ->orderBy('maKH')
            ->chunk(200, function ($enrollments) use ($certificateService) {
                foreach ($enrollments as $enrollment) {
                    /** @var Enrollment $enrollment */
                    $certificateService->issueCourseCertificateIfEligible($enrollment);
                }
            });
    }
}
