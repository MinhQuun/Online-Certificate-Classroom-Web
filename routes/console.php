<?php

use App\Jobs\AutoIssueCertificatesJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('certificates:auto-issue {--sync}', function () {
    $runSync = (bool) $this->option('sync');

    if ($runSync) {
        AutoIssueCertificatesJob::dispatchSync();
        $this->info('Certificate sweep completed (sync).');

        return;
    }

    AutoIssueCertificatesJob::dispatch();
    $this->info('Certificate sweep dispatched to the queue.');
})->purpose('Scan enrollments to auto-issue course/combo certificates');
