<?php

namespace App\Middleware;

use App\Services\AuthService;

class AuthMiddleware
{
    public static ?\App\Models\User $user = null;

    public static function authenticate(): void
    {
        $headers = self::getRequestHeaders();
        $authorization = $headers['authorization'] ?? $headers['Authorization'] ?? null;

        if (!$authorization || !str_starts_with($authorization, 'Bearer ')) {
            apiResponse(false, 'Unauthenticated', null, 401);
        }

        $token = trim(str_replace('Bearer', '', $authorization));
        $user = AuthService::validateToken($token);

        if (!$user) {
            apiResponse(false, 'Unauthenticated', null, 401);
        }

        self::$user = $user;
    }

    public static function user(): ?\App\Models\User
    {
        return self::$user;
    }

    private static function getRequestHeaders(): array
    {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (str_starts_with($name, 'HTTP_')) {
                $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$key] = $value;
            }
        }
        return $headers;
    }
}
