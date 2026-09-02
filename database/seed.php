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
    'email' => 'admin@birashobokacenter.org',
    'password' => AuthService::hashPassword('password'),
]);
AuthService::createTokenForUser($user);

// Secondary user
$user2 = User::create([
    'name' => 'giraso',
    'email' => 'giraso.pro@gmail.com',
    'password' => AuthService::hashPassword('password'),
]);
AuthService::createTokenForUser($user2);

echo "Seeding complete." . PHP_EOL;
