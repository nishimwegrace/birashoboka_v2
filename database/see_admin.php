<?php

require_once __DIR__ . '/../bootstrap.php';

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Database\Capsule\Manager as Capsule;

try {
    Capsule::connection()->getPdo();
} catch (\Throwable $e) {
    echo "Database connection failed: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

Capsule::statement('SET FOREIGN_KEY_CHECKS=0');
Capsule::table('users')->truncate();
Capsule::statement('SET FOREIGN_KEY_CHECKS=1');

// Users
$user = User::create([
    'name' => 'Grace Nishimwe',
    'email' => 'nishimwegrace@gmail.com',
    'password' => AuthService::hashPassword('password'),
]);
AuthService::createTokenForUser($user);

echo "Seeding admin complete." . PHP_EOL;
