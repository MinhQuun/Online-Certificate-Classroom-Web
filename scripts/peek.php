<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
$app->make(Kernel::class)->bootstrap();

$rows = DB::table('khoahoc')->limit(2)->get();
echo json_encode($rows, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . "\n";

