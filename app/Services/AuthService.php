<?php

namespace App\Services;

use App\Models\User;

class AuthService
{
    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function createTokenForUser(User $user): string
    {
        $token = self::generateToken();
        $user->api_token = $token;
        $user->save();
        return $token;
    }

    public static function validateToken(string $token): ?User
    {
        return User::where('api_token', $token)->first();
    }
}
