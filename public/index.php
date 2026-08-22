<?php

require_once __DIR__ . '/../bootstrap.php';

use App\Middleware\AuthMiddleware;

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$routes = require __DIR__ . '/../routes/api.php';

if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Origin: ' . env('CORS_ALLOWED_ORIGINS', '*'));
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    http_response_code(204);
    exit;
}

$body = json_decode(file_get_contents('php://input') ?: '', true);
if (!is_array($body)) {
    $body = [];
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
