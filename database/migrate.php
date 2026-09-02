<?php

require_once __DIR__ . '/../bootstrap.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// Create a migrations tracking table if it does not yet exist (idempotent runner).
if (!Capsule::schema()->hasTable('migrations')) {
    Capsule::schema()->create('migrations', function (Blueprint $table) {
        $table->increments('id');
        $table->string('migration');
        $table->integer('batch');
    });
}

$applied = Capsule::table('migrations')->pluck('migration')->all();

$migrations = glob(__DIR__ . '/migrations/*.php');
sort($migrations);

// Determine the next batch number.
$batch = Capsule::table('migrations')->max('batch') + 1;

$run = 0;
foreach ($migrations as $file) {
    $name = basename($file);
    if (in_array($name, $applied, true)) {
        continue; // Already applied; skip.
    }

    $migration = require $file;
    if (!is_callable($migration)) {
        continue;
    }

    try {
        $migration();
        Capsule::table('migrations')->insert(['migration' => $name, 'batch' => $batch]);
        $run++;
        echo "Migrated: " . $name . PHP_EOL;
    } catch (\Throwable $e) {
        echo "Failed migration " . $name . ": " . $e->getMessage() . PHP_EOL;
        exit(1);
    }
}

if ($run === 0) {
    echo "Nothing to migrate." . PHP_EOL;
} else {
    echo "Migrations complete (run={$run})." . PHP_EOL;
}
