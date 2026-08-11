<?php

namespace App\Controllers;

use App\Models\User;
use App\Validators\Validator;
use App\Services\AuthService;

class UserController extends Controller
{
    public static function index(): void
    {
        $query = User::query();
        self::applySearchAndSort($query, ['name', 'email'], ['name', 'email', 'created_at']);
        self::paginate($query, 'Users retrieved successfully');
    }

    public static function show(int $id): void
    {
        $user = User::find($id);
        if (!$user) {
            apiResponse(false, 'User not found', null, 404);
        }

        apiResponse(true, 'User retrieved successfully', self::sanitize($user));
    }

    public static function store(array $body): void
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

        apiResponse(true, 'User created successfully', self::sanitize($user), 201);
    }

    public static function update(int $id, array $body): void
    {
        $user = User::find($id);
        if (!$user) {
            apiResponse(false, 'User not found', null, 404);
        }

        $errors = Validator::validate($body, [
            'name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email,' . $id . ',id',
            'password' => 'nullable|string|min:8',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        if (isset($body['name'])) {
            $user->name = trim($body['name']);
        }
        if (isset($body['email'])) {
            $user->email = strtolower(trim($body['email']));
        }
        if (!empty($body['password'])) {
            $user->password = AuthService::hashPassword($body['password']);
        }
        $user->save();

        apiResponse(true, 'User updated successfully', self::sanitize($user));
    }

    public static function destroy(int $id): void
    {
        $user = User::find($id);
        if (!$user) {
            apiResponse(false, 'User not found', null, 404);
        }

        $user->delete();
        apiResponse(true, 'User deleted successfully', null, 200);
    }

    private static function sanitize(User $user): array
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
