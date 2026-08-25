<?php

// require_once __DIR__ . '/../birashoboka_api/bootstrap.php';
require_once __DIR__ . '/../bootstrap.php';




use App\Middleware\AuthMiddleware;

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Serve storage files directly when present (helps shared hosting without vhost alias)
if (str_starts_with($uri, '/storage/')) {
    $relative = substr($uri, strlen('/storage'));

    // Prefer explicit storage path from env if provided
    $envStorage = env('STORAGE_PATH', null);
    $candidates = [];
    if ($envStorage) {
        $candidates[] = rtrim($envStorage, '/\\') . $relative;
    }

    // Common layouts: storage sibling to public_html or inside a sibling app folder
    $candidates[] = __DIR__ . '/../storage' . $relative;
    $candidates[] = __DIR__ . '/../birashoboka_api/storage' . $relative;
    $candidates[] = __DIR__ . '/../../birashoboka_api/storage' . $relative;

    $storagePath = null;
    foreach ($candidates as $p) {
        $rp = realpath($p);
        if ($rp && is_file($rp) && is_readable($rp)) {
            $storagePath = $rp;
            break;
        }
    }

    // compute base dirs for traversal protection
    $baseCandidates = [];
    if ($envStorage) {
        $baseCandidates[] = realpath(rtrim($envStorage, '/\\'));
    }
    $baseCandidates[] = realpath(__DIR__ . '/../storage');
    $baseCandidates[] = realpath(__DIR__ . '/../birashoboka_api/storage');
    $baseCandidates[] = realpath(__DIR__ . '/../../birashoboka_api/storage');

    $base = null;
    foreach ($baseCandidates as $b) {
        if ($b) { $base = $b; break; }
    }

    if ($storagePath && $base && str_starts_with($storagePath, $base)) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $storagePath) ?: 'application/octet-stream';
        finfo_close($finfo);
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($storagePath));
        header('Cache-Control: public, max-age=31536000');
        readfile($storagePath);
        exit;
    }
}


$routes = require __DIR__ . '/../birashoboka_api/routes/api.php';


if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Origin: ' . env('CORS_ALLOWED_ORIGINS', '*'));
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    http_response_code(204);
    exit;
}

// Handle post_max_size overflow: PHP silently discards $_POST & $_FILES when
// multipart payload exceeds post_max_size — detect it and return clean JSON.
if ($method === 'POST') {
    $contentLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
    $postMaxBytes  = self_parse_bytes(ini_get('post_max_size'));
    $isMultipart   = str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'multipart/form-data');
    if ($isMultipart && $contentLength > 0 && $contentLength > $postMaxBytes) {
        apiResponse(false, 'The uploaded file payload exceeds the maximum allowed server limit (' . ini_get('post_max_size') . ').', null, 422);
    }
}

// Parse body from raw JSON input and/or multipart/form-data $_POST
$body = json_decode(file_get_contents('php://input') ?: '', true);
if (!is_array($body)) {
    $body = [];
}
if (!empty($_POST)) {
    $body = array_merge($body, $_POST);
}

$matched = false;

foreach ($routes as $route) {
    if ($route['method'] !== $method) {
        continue;
    }

    [$matchedRoute, $params] = matchRoute($route['path'], $uri);
    if (!$matchedRoute) {
        continue;
    }

    $matched = true;

    if ($route['auth']) {
        AuthMiddleware::authenticate();
    }

    [$controllerName, $action] = explode('@', $route['action']);
    $controllerClass = 'App\\Controllers\\' . $controllerName;

    if (!class_exists($controllerClass) || !method_exists($controllerClass, $action)) {
        apiResponse(false, 'Endpoint not implemented', null, 500);
    }

    try {
        $reflection = new ReflectionMethod($controllerClass, $action);
        $args = array_values($params);
        if ($reflection->getNumberOfParameters() >= count($args) + 1) {
            $args[] = $body;
        }
        if ($reflection->getNumberOfParameters() >= count($args) + 1) {
            $args[] = $_FILES;
        }

        call_user_func_array([$controllerClass, $action], $args);
    } catch (\Throwable $exception) {
        if (isDebug()) {
            apiResponse(false, $exception->getMessage(), null, 500);
        }
        apiResponse(false, 'An unexpected error occurred', null, 500);
    }
}

if (!$matched) {
    if (str_starts_with($uri, '/api/') || $uri === '/api') {
        apiResponse(false, 'Endpoint not found', null, 404);
    }

    $indexPath = __DIR__ . '/index.html';
    if (file_exists($indexPath)) {
        header('Content-Type: text/html; charset=utf-8');
        readfile($indexPath);
        exit;
    }

    apiResponse(false, 'Endpoint not found', null, 404);
}

function matchRoute(string $pattern, string $uri): array
{
    $regex = preg_replace('#\{[^/]+\}#', '([^/]+)', $pattern);
    $regex = '#^' . $regex . '$#';
    if (!preg_match($regex, $uri, $matches)) {
        return [false, []];
    }

    array_shift($matches);
    preg_match_all('#\{([^/]+)\}#', $pattern, $keys);
    return [true, array_combine($keys[1], $matches) ?: []];
}

function self_parse_bytes(string $val): int
{
    $val  = trim($val);
    $last = strtolower($val[strlen($val) - 1]);
    $num  = (int) $val;
    return match ($last) {
        'g' => $num * 1024 * 1024 * 1024,
        'm' => $num * 1024 * 1024,
        'k' => $num * 1024,
        default => $num,
    };
}
