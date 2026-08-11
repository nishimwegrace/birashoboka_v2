<?php

if (!function_exists('apiResponse')) {
    function apiResponse(bool $success, string $message, $data = null, int $statusCode = 200): void
    {
        if (!in_array($statusCode, [200, 201, 204, 400, 401, 403, 404, 409, 422, 500], true)) {
            $statusCode = 500;
        }

        if ($data !== null && !is_array($data) && !is_object($data)) {
            $data = ['value' => $data];
        }

        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: ' . env('CORS_ALLOWED_ORIGINS', '*'));
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        $payload = [
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ];

        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if (!function_exists('apiError')) {
    function apiError(string $message, $data = null, int $statusCode = 500): void
    {
        apiResponse(false, $message, $data, $statusCode);
    }
}
