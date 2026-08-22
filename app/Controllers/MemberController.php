<?php

namespace App\Controllers;

use App\Models\Member;
use App\Validators\Validator;
use App\Services\ImageService;

class MemberController extends Controller
{
    public static function index(): void
    {
        $query = Member::query();
        self::applySearchAndSort($query, ['name', 'position', 'bio', 'email'], ['name', 'created_at']);
        self::paginate($query, 'Members retrieved successfully');
    }

    public static function store(array $body, array $files = []): void
    {
        $errors = Validator::validate($body, [
            'name'     => 'required|string',
            'position' => 'nullable|string',
            'bio'      => 'nullable|string',
            'email'    => 'nullable|email',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        $avatarPath = null;
        if (!empty($files['avatar'])) {
            try {
                $avatarPath = ImageService::processUpload($files['avatar'], 'members');
            } catch (\RuntimeException $e) {
                apiResponse(false, $e->getMessage(), null, 422);
            }
        }

        $member = Member::create([
            'name'     => trim($body['name']),
            'position' => $body['position'] ?? null,
            'bio'      => $body['bio'] ?? null,
            'avatar'   => $avatarPath,
            'email'    => isset($body['email']) ? strtolower(trim($body['email'])) : null,
        ]);

        apiResponse(true, 'Member created successfully', $member, 201);
    }

    public static function show(int $id): void
    {
        $member = Member::find($id);
        if (!$member) {
            apiResponse(false, 'Member not found', null, 404);
        }

        apiResponse(true, 'Member retrieved successfully', $member);
    }

    public static function update(int $id, array $body, array $files = []): void
    {
        $member = Member::find($id);
        if (!$member) {
            apiResponse(false, 'Member not found', null, 404);
        }

        $errors = Validator::validate($body, [
            'name'     => 'nullable|string',
            'position' => 'nullable|string',
            'bio'      => 'nullable|string',
            'email'    => 'nullable|email',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        if (!empty($files['avatar'])) {
            try {
                $avatarPath = ImageService::processUpload($files['avatar'], 'members');
                if ($member->avatar) {
                    ImageService::delete($member->avatar);
                }
                $member->avatar = $avatarPath;
            } catch (\RuntimeException $e) {
                apiResponse(false, $e->getMessage(), null, 422);
            }
        }

        foreach (['name', 'position', 'bio', 'email'] as $field) {
            if (isset($body[$field])) {
                $member->{$field} = $field === 'email' ? strtolower(trim($body[$field])) : $body[$field];
            }
        }
        $member->save();

        apiResponse(true, 'Member updated successfully', $member);
    }

    public static function destroy(int $id): void
    {
        $member = Member::find($id);
        if (!$member) {
            apiResponse(false, 'Member not found', null, 404);
        }

        if ($member->avatar) {
            ImageService::delete($member->avatar);
        }
        $member->delete();

        apiResponse(true, 'Member deleted successfully', null, 200);
    }
}
