<?php

require_once __DIR__ . '/../bootstrap.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$migrations = glob(__DIR__ . '/migrations/*.php');
sort($migrations);

$run = 0;
foreach ($migrations as $file) {
    $migration = require $file;
    if (is_callable($migration)) {
        try {
            $migration();
            $run++;
            echo "Migrated: " . basename($file) . PHP_EOL;
        } catch (\Throwable $e) {
            echo "Failed migration " . basename($file) . ": " . $e->getMessage() . PHP_EOL;
            exit(1);
        }
    }
}

if ($run === 0) {
    echo "No migrations found." . PHP_EOL;
} else {
    echo "Migrations complete (run={$run})." . PHP_EOL;
}
