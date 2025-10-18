<?php
// Quick DB introspection for Laravel app
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

try {
    $driver = config('database.default');
    $connection = config("database.connections.$driver");
    echo "Connected via: " . $driver . "\n";

    $tables = [];
    $database = $connection['database'] ?? null;
    if (!$database) {
        throw new Exception('No database configured.');
    }

    $rows = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
    foreach ($rows as $row) {
        $values = array_values((array)$row);
        $tables[] = $values[0] ?? null;
    }

    echo "Tables (" . count($tables) . "):\n";
    foreach ($tables as $t) {
        echo "- $t\n";
    }

    echo "\nColumns per table:\n";
    foreach ($tables as $t) {
        echo "\n[$t]\n";
        $cols = DB::select("SHOW COLUMNS FROM `$t`");
        foreach ($cols as $c) {
            $cArr = (array)$c;
            printf("  - %s %s %s\n", $cArr['Field'], $cArr['Type'], $cArr['Key'] ? ("[".$cArr['Key']."]") : '');
        }
        $count = DB::table($t)->count();
        echo "  rows: $count\n";
    }
} catch (Throwable $e) {
    fwrite(STDERR, 'Error: ' . $e->getMessage() . "\n");
    exit(1);
}

