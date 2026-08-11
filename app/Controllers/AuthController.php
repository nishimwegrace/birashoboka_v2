<?php

namespace App\Controllers;

use App\Models\User;
use App\Middleware\AuthMiddleware;
use App\Services\AuthService;
use App\Validators\Validator;

class AuthController extends Controller
{
    public static function register(array $body): void
    {
        $errors = Validator::validate($body, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        $user = User::create([
            'name' => trim($body['name']),
            'email' => strtolower(trim($body['email'])),
            'password' => AuthService::hashPassword($body['password']),
        ]);

        $token = AuthService::createTokenForUser($user);

        apiResponse(true, 'Registration successful', [
            'user' => self::sanitizeUser($user),
            'token' => $token,
        ], 201);
    }

    public static function login(array $body): void
    {
        $errors = Validator::validate($body, [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        $user = User::where('email', strtolower(trim($body['email'])))->first();
        if (!$user || !AuthService::verifyPassword($body['password'], $user->password)) {
            apiResponse(false, 'Invalid credentials', null, 401);
        }

        $token = AuthService::createTokenForUser($user);

        apiResponse(true, 'Login successful', [
            'user' => self::sanitizeUser($user),
            'token' => $token,
        ]);
    }

    public static function logout(): void
    {
        $user = AuthMiddleware::user();
        if ($user) {
            $user->api_token = null;
            $user->save();
        }

        apiResponse(true, 'Logout successful', null);
    }

    public static function me(): void
    {
        $user = AuthMiddleware::user();
        if (!$user) {
            apiResponse(false, 'Unauthenticated', null, 401);
        }

        apiResponse(true, 'User details retrieved successfully', self::sanitizeUser($user));
    }

    private static function sanitizeUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at?->toDateTimeString(),
            'updated_at' => $user->updated_at?->toDateTimeString(),
        ];
    }
}
