<?php

namespace App\Controllers;

use App\Models\Activity;
use App\Validators\Validator;

class ActivityController extends Controller
{
    public static function index(): void
    {
        $query = Activity::with('volet');
        self::applySearchAndSort($query, ['title', 'description'], ['title', 'created_at']);
        self::paginate($query, 'Activities retrieved successfully');
    }

    public static function store(array $body): void
    {
        $errors = Validator::validate($body, [
            'volet_id'    => 'required|exists:volets,id',
            'title'       => 'required|string',
            'description' => 'required|string',
            'icon'        => 'nullable|string',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        $activity = Activity::create([
            'volet_id'    => $body['volet_id'],
            'title'       => trim($body['title']),
            'description' => trim($body['description']),
            'icon'        => $body['icon'] ?? null,
        ]);

        apiResponse(true, 'Activity created successfully', $activity->load('volet'), 201);
    }

    public static function show(int $id): void
    {
        $activity = Activity::with(['volet', 'campaigns', 'testimonials'])->find($id);
        if (!$activity) {
            apiResponse(false, 'Activity not found', null, 404);
        }

        apiResponse(true, 'Activity retrieved successfully', $activity);
    }

    public static function update(int $id, array $body): void
    {
        $activity = Activity::find($id);
        if (!$activity) {
            apiResponse(false, 'Activity not found', null, 404);
        }

        $errors = Validator::validate($body, [
            'volet_id'    => 'nullable|exists:volets,id',
            'title'       => 'nullable|string',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        foreach (['volet_id', 'title', 'description', 'icon'] as $field) {
            if (isset($body[$field])) {
                $activity->{$field} = $body[$field];
            }
        }
        $activity->save();

        apiResponse(true, 'Activity updated successfully', $activity->load('volet'));
    }

    public static function destroy(int $id): void
    {
        $activity = Activity::find($id);
        if (!$activity) {
            apiResponse(false, 'Activity not found', null, 404);
        }

        $activity->delete();
        apiResponse(true, 'Activity deleted successfully', null, 200);
    }
}
