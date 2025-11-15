<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class CertificateService
{
    public function issueCourseCertificateIfEligible(Enrollment $enrollment): ?Certificate
    {
        $enrollment->loadMissing(['student.user', 'course.teacher', 'course.certificateTemplate']);

        $student = $enrollment->student;
        $course = $enrollment->course;

        if (!$student || !$course || !$course->certificate_enabled) {
            return null;
        }

        $required = $course->certificateProgressThreshold();
        $progress = (int) ($enrollment->progress_percent ?? 0);

        if ($progress < $required) {
            return null;
        }

        $issuedAt = $enrollment->completed_at
            ? $enrollment->completed_at->copy()->timezone($this->timezone())
            : null;

        $certificate = null;

        DB::transaction(function () use (&$certificate, &$issuedAt, $enrollment, $student, $course, $required) {
            $locked = Enrollment::query()
                ->where('maHV', $enrollment->maHV)
                ->where('maKH', $enrollment->maKH)
                ->lockForUpdate()
                ->first();

            if (!$locked) {
                return;
            }

            $currentProgress = (int) ($locked->progress_percent ?? 0);
            if ($currentProgress < $required) {
                return;
            }

            if ($locked->completed_at) {
                $issuedAt = Carbon::parse($locked->completed_at)->timezone($this->timezone());
            } else {
                $issuedAt = Carbon::now($this->timezone());
                $locked->forceFill(['completed_at' => $issuedAt])->save();
            }

            $duplicateExists = Certificate::query()
                ->where('maHV', $student->maHV)
                ->where('maKH', $course->maKH)
                ->where('loaiCC', Certificate::TYPE_COURSE)
                ->where('issue_mode', Certificate::ISSUE_MODE_AUTO)
                ->where('trangThai', Certificate::STATUS_ISSUED)
                ->where('issued_at', $issuedAt)
                ->exists();

            if ($duplicateExists) {
                return;
            }

            $title = sprintf('Chứng chỉ hoàn thành khóa "%s"', Str::limit($course->tenKH ?? 'OCC Course', 70));
            $description = sprintf(
                'Hoàn thành khóa %s vào %s',
                $course->tenKH ?? 'OCC Course',
                $issuedAt->format('d/m/Y')
            );

            $certificate = Certificate::create([
                'maHV' => $student->maHV,
                'loaiCC' => Certificate::TYPE_COURSE,
                'maKH' => $course->maKH,
                'maGoi' => null,
                'tenCC' => $title,
                'moTa' => Str::limit($description, 240),
                'code' => $this->generateCertificateCode(),
                'pdf_url' => null,
                'trangThai' => Certificate::STATUS_ISSUED,
                'issue_mode' => Certificate::ISSUE_MODE_AUTO,
                'issued_at' => $issuedAt,
            ]);
        });

        if (!$certificate) {
            return null;
        }

        $template = $this->resolveCourseTemplate($course);
        $this->attachPdfToCertificate($certificate, [
            'student' => $student,
            'course' => $course,
            'template' => $template,
        ]);

        return $certificate->refresh();
    }

    public function issueManualCourseCertificate(
        Student $student,
        Course $course,
        User $admin,
        array $options = []
    ): Certificate {
        $student->loadMissing('user');
        $course->loadMissing(['teacher', 'certificateTemplate']);

        $issuedAt = $this->resolveIssuedAt($options['issued_at'] ?? null);
        $title = $options['title']
            ?? sprintf('Chứng chỉ khóa "%s"', Str::limit($course->tenKH ?? 'OCC Course', 70));
        $description = $options['description']
            ?? sprintf('Hoàn thành khóa %s vào %s', $course->tenKH ?? 'OCC Course', $issuedAt->format('d/m/Y'));

        $certificate = null;

        DB::transaction(function () use (&$certificate, $student, $course, $admin, $issuedAt, $title, $description) {
            $certificate = Certificate::create([
                'maHV'      => $student->maHV,
                'loaiCC'    => Certificate::TYPE_COURSE,
                'maKH'      => $course->maKH,
                'maGoi'     => null,
                'tenCC'     => $title,
                'moTa'      => Str::limit($description, 240),
                'code'      => $this->generateCertificateCode(),
                'pdf_url'   => null,
                'trangThai' => Certificate::STATUS_ISSUED,
                'issue_mode'=> Certificate::ISSUE_MODE_MANUAL,
                'issued_at' => $issuedAt,
                'issued_by' => $admin->getKey(),
            ]);
        });

        $template = $this->resolveCourseTemplate($course);
        $this->attachPdfToCertificate($certificate, [
            'student'  => $student,
            'course'   => $course,
            'template' => $template,
        ]);

        return $certificate->refresh();
    }

    public function revokeCertificate(Certificate $certificate, User $admin, string $reason): Certificate
    {
        $certificate->forceFill([
            'trangThai' => Certificate::STATUS_REVOKED,
            'revoked_at' => Carbon::now($this->timezone()),
            'revoked_by' => $admin->getKey(),
            'revoked_reason' => Str::limit($reason, 240),
        ])->save();

        return $certificate->refresh();
    }

    protected function attachPdfToCertificate(Certificate $certificate, array $context = []): void
    {
        $disk = $this->certificateDisk();

        $viewData = array_merge([
            'certificate' => $certificate->loadMissing(['student.user', 'course']),
            'student' => $certificate->student ?? $context['student'] ?? null,
            'course' => $context['course'] ?? $certificate->course,
            'template' => $context['template'] ?? null,
            'issuedDateLabel' => optional($certificate->issued_at)
                ? $certificate->issued_at->copy()->timezone($this->timezone())->format('d/m/Y')
                : Carbon::now($this->timezone())->format('d/m/Y'),
            'theme' => $this->defaultTheme(),
        ], $context);

        try {
            if ($disk === 'public') {
                $this->ensurePublicStorageSymlinkExists();
            }

            $this->ensureDompdfFontCacheExists();

            $pdf = Pdf::loadView('Admin.certificates.certificate-pdf', $viewData)
                ->setPaper('a4', 'landscape');

            $relativePath = sprintf(
                'certificates/%s/%s-%s.pdf',
                strtolower($certificate->loaiCC),
                optional($certificate->issued_at)->format('Ymd-His') ?? Carbon::now($this->timezone())->format('Ymd-His'),
                Str::slug($certificate->code)
            );

            Storage::disk($disk)->put($relativePath, $pdf->output());

            $certificate->forceFill([
                'pdf_url' => Storage::disk($disk)->url($relativePath),
            ])->save();
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    protected function resolveCourseTemplate(?Course $course): ?CertificateTemplate
    {
        if ($course?->relationLoaded('certificateTemplate') && $course->certificateTemplate?->trangThai === CertificateTemplate::STATUS_ACTIVE) {
            return $course->certificateTemplate;
        }

        if ($course) {
            $specific = CertificateTemplate::query()
                ->active()
                ->where('loaiTemplate', CertificateTemplate::TYPE_COURSE)
                ->where('maKH', $course->maKH)
                ->first();

            if ($specific) {
                return $specific;
            }
        }

        return CertificateTemplate::query()
            ->active()
            ->where('loaiTemplate', CertificateTemplate::TYPE_COURSE)
            ->whereNull('maKH')
            ->orderByDesc('updated_at')
            ->first();
    }

    protected function generateCertificateCode(): string
    {
        $timezone = $this->timezone();
        $yearPrefix = Carbon::now($timezone)->format('Y');

        for ($attempt = 0; $attempt < 10; $attempt++) {
            $code = sprintf('OCC-%s-%s', $yearPrefix, strtoupper(Str::random(6)));

            $exists = Certificate::query()
                ->where('code', $code)
                ->exists();

            if (! $exists) {
                return $code;
            }
        }

        throw new \RuntimeException('Unable to generate a unique certificate code after multiple attempts.');
    }

    protected function certificateDisk(): string
    {
        return config('occ.certificates.disk', env('CERTIFICATE_STORAGE_DISK', 'public'));
    }

    protected function timezone(): string
    {
        return config('app.timezone', 'Asia/Ho_Chi_Minh');
    }

    protected function resolveIssuedAt(?string $value): Carbon
    {
        $timezone = $this->timezone();

        if ($value) {
            return Carbon::parse($value, $timezone);
        }

        return Carbon::now($timezone);
    }

    protected function defaultTheme(): array
    {
        return [
            'primary' => '#2563eb',
            'primaryDark' => '#1d4ed8',
            'accent' => '#f97316',
            'text' => '#0f172a',
            'muted' => '#64748b',
        ];
    }

    protected function ensureDompdfFontCacheExists(): void
    {
        File::ensureDirectoryExists(storage_path('fonts'));
    }

    protected function ensurePublicStorageSymlinkExists(): void
    {
        $publicStoragePath = public_path('storage');

        if (File::exists($publicStoragePath)) {
            return;
        }

        try {
            File::link(storage_path('app/public'), $publicStoragePath);
            return;
        } catch (Throwable $exception) {
            report($exception);
        }

        try {
            Artisan::call('storage:link');
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
