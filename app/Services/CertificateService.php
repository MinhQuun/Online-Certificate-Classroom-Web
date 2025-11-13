<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Combo;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateService
{
    public function issueCourseCertificateIfEligible(Enrollment $enrollment): ?Certificate
    {
        $enrollment->loadMissing(['student.user', 'course.teacher', 'course.certificateTemplate', 'combo']);

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

        if ($enrollment->maGoi && $enrollment->combo) {
            $this->issueComboCertificateIfEligible($student, $enrollment->combo);
        }

        return $certificate->refresh();
    }

    public function issueComboCertificateIfEligible(Student $student, Combo $combo): ?Certificate
    {
        $student->loadMissing('user');
        $combo->loadMissing(['courses', 'certificateTemplate']);

        if ($combo->certificate_enabled === false) {
            return null;
        }

        if ($combo->courses->isEmpty()) {
            return null;
        }

        $courseIds = $combo->courses->pluck('maKH')->all();
        /** @var \Illuminate\Support\Collection<int, \App\Models\Enrollment> $enrollments */
        $enrollments = Enrollment::query()
            ->where('maHV', $student->maHV)
            ->whereIn('maKH', $courseIds)
            ->get()
            ->keyBy('maKH');

        if ($enrollments->count() !== count($courseIds)) {
            return null;
        }

        $courseCertificates = Certificate::query()
            ->where('maHV', $student->maHV)
            ->where('loaiCC', Certificate::TYPE_COURSE)
            ->whereIn('maKH', $courseIds)
            ->where('trangThai', Certificate::STATUS_ISSUED)
            ->get()
            ->groupBy('maKH');

        $requiredMap = [];
        foreach ($combo->courses as $course) {
            $requiredMap[$course->maKH] = $course->certificateProgressThreshold();
        }

        $issuedAt = Carbon::now($this->timezone());
        $certificate = null;

        DB::transaction(function () use (
            &$certificate,
            &$issuedAt,
            $student,
            $combo,
            $courseIds,
            $requiredMap,
            $courseCertificates
        ) {
            $lockedEnrollments = Enrollment::query()
                ->where('maHV', $student->maHV)
                ->whereIn('maKH', $courseIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('maKH');

            if ($lockedEnrollments->count() !== count($courseIds)) {
                return;
            }

            foreach ($courseIds as $courseId) {
                $enrollment = $lockedEnrollments->get($courseId);
                if (!$enrollment) {
                    return;
                }

                $progress = (int) ($enrollment->progress_percent ?? 0);
                $hasIssuedCourseCertificate = ($courseCertificates->get($courseId)?->isNotEmpty()) ?? false;

                if (!$hasIssuedCourseCertificate && $progress < ($requiredMap[$courseId] ?? 100)) {
                    return;
                }
            }

            $tokens = $this->buildSnapshotTokens($combo->courses, $lockedEnrollments, $courseCertificates);
            if ($tokens === null) {
                return;
            }

            $snapshotKey = $this->buildComboSnapshotKey($student->maHV, $combo->maGoi, $tokens);
            $snapshotMarker = $this->formatSnapshotMarker($snapshotKey);

            if ($this->comboSnapshotAlreadyIssued($student->maHV, $combo->maGoi, $snapshotMarker)) {
                return;
            }

            $title = sprintf('Chứng chỉ Combo "%s"', Str::limit($combo->tenGoi ?? 'Combo OCC', 70));
            $description = sprintf(
                'Hoàn thành toàn bộ combo %s %s',
                $combo->tenGoi ?? 'Combo OCC',
                $snapshotMarker
            );

            $certificate = Certificate::create([
                'maHV' => $student->maHV,
                'loaiCC' => Certificate::TYPE_COMBO,
                'maKH' => null,
                'maGoi' => $combo->maGoi,
                'tenCC' => $title,
                'moTa' => $description,
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

        $template = $this->resolveComboTemplate($combo);
        $this->attachPdfToCertificate($certificate, [
            'student' => $student,
            'combo' => $combo,
            'courseList' => $combo->courses,
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

    public function issueManualComboCertificate(
        Student $student,
        Combo $combo,
        User $admin,
        array $options = []
    ): Certificate {
        $student->loadMissing('user');
        $combo->loadMissing(['courses', 'certificateTemplate']);

        $issuedAt = $this->resolveIssuedAt($options['issued_at'] ?? null);
        $title = $options['title']
            ?? sprintf('Chứng chỉ Combo "%s"', Str::limit($combo->tenGoi ?? 'Combo OCC', 70));
        $description = $options['description']
            ?? sprintf('Hoàn thành đầy đủ combo %s', $combo->tenGoi ?? 'Combo OCC');

        $certificate = null;

        DB::transaction(function () use (&$certificate, $student, $combo, $admin, $issuedAt, $title, $description) {
            $certificate = Certificate::create([
                'maHV'      => $student->maHV,
                'loaiCC'    => Certificate::TYPE_COMBO,
                'maKH'      => null,
                'maGoi'     => $combo->maGoi,
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

        $template = $this->resolveComboTemplate($combo);
        $this->attachPdfToCertificate($certificate, [
            'student'    => $student,
            'combo'      => $combo,
            'courseList' => $combo->courses,
            'template'   => $template,
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
            'certificate' => $certificate->loadMissing(['student.user', 'course', 'combo']),
            'student' => $certificate->student ?? $context['student'] ?? null,
            'course' => $context['course'] ?? $certificate->course,
            'combo' => $context['combo'] ?? $certificate->combo,
            'courseList' => $context['courseList'] ?? null,
            'template' => $context['template'] ?? null,
            'issuedDateLabel' => optional($certificate->issued_at)
                ? $certificate->issued_at->copy()->timezone($this->timezone())->format('d/m/Y')
                : Carbon::now($this->timezone())->format('d/m/Y'),
            'theme' => $this->defaultTheme(),
        ], $context);

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

    protected function resolveComboTemplate(?Combo $combo): ?CertificateTemplate
    {
        if ($combo?->relationLoaded('certificateTemplate') && $combo->certificateTemplate?->trangThai === CertificateTemplate::STATUS_ACTIVE) {
            return $combo->certificateTemplate;
        }

        if ($combo) {
            $specific = CertificateTemplate::query()
                ->active()
                ->where('loaiTemplate', CertificateTemplate::TYPE_COMBO)
                ->where('maGoi', $combo->maGoi)
                ->first();

            if ($specific) {
                return $specific;
            }
        }

        return CertificateTemplate::query()
            ->active()
            ->where('loaiTemplate', CertificateTemplate::TYPE_COMBO)
            ->whereNull('maGoi')
            ->orderByDesc('updated_at')
            ->first();
    }

    protected function buildSnapshotTokens(
        Collection $courses,
        Collection $enrollments,
        Collection $courseCertificates
    ): ?array {
        $tokens = [];

        foreach ($courses as $course) {
            /** @var Course $course */
            $enrollment = $enrollments->get($course->maKH);
            if (!$enrollment) {
                return null;
            }

            $certificates = $courseCertificates->get($course->maKH);
            $tokens[] = $this->buildComboCourseToken($course, $enrollment, $certificates);
        }

        sort($tokens);

        return $tokens;
    }

    protected function buildComboCourseToken(
        Course $course,
        Enrollment $enrollment,
        ?EloquentCollection $certificates
    ): string {
        if ($certificates && $certificates->isNotEmpty()) {
            $latest = $certificates->sortByDesc('issued_at')->first();
            $issuedAt = optional($latest->issued_at)
                ? $latest->issued_at->copy()->timezone($this->timezone())->format('Y-m-d H:i:s')
                : '0000-00-00 00:00:00';

            return implode(':', [$course->maKH, 'CERT', $latest->code, $issuedAt]);
        }

        if ($enrollment->completed_at) {
            return implode(':', [
                $course->maKH,
                'ENROLL',
                $enrollment->completed_at->copy()->timezone($this->timezone())->format('Y-m-d H:i:s'),
            ]);
        }

        return implode(':', [
            $course->maKH,
            'PROGRESS',
            (int) ($enrollment->progress_percent ?? 0),
        ]);
    }

    protected function comboSnapshotAlreadyIssued(int $studentId, int $comboId, string $marker): bool
    {
        return Certificate::query()
            ->where('maHV', $studentId)
            ->where('maGoi', $comboId)
            ->where('loaiCC', Certificate::TYPE_COMBO)
            ->where('issue_mode', Certificate::ISSUE_MODE_AUTO)
            ->where('trangThai', Certificate::STATUS_ISSUED)
            ->where('moTa', 'like', '%' . $marker . '%')
            ->exists();
    }

    protected function buildComboSnapshotKey(int $studentId, int $comboId, array $tokens): string
    {
        return sha1($studentId . '|' . $comboId . '|' . implode('|', $tokens));
    }

    protected function formatSnapshotMarker(string $hash): string
    {
        return '[SNAPSHOT:' . $hash . ']';
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
}
