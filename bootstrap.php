<?php

use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

// Turn off display of raw HTML PHP warnings so API JSON responses stay clean
ini_set('display_errors', '0');
error_reporting(E_ALL);

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

function env(string $key, $default = null)
{
    return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
}

function isDebug(): bool
{
    return filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN);
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/Helpers/apiResponse.php';
