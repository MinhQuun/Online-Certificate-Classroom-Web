<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
$app->make(Kernel::class)->bootstrap();

$rows = DB::table('tailieuhoctap')
    ->select('loai', DB::raw('count(*) as c'))
    ->groupBy('loai')
    ->get();
foreach ($rows as $r) {
    echo $r->loai . ':' . $r->c . PHP_EOL;
}

