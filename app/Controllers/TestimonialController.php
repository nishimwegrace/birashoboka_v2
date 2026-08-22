<?php

namespace App\Controllers;

use App\Models\Testimonial;
use App\Validators\Validator;
use App\Services\ImageService;

class TestimonialController extends Controller
{
    public static function index(): void
    {
        $query = Testimonial::with('activity');
        self::applySearchAndSort($query, ['name', 'content', 'role'], ['name', 'created_at']);
        self::paginate($query, 'Testimonials retrieved successfully');
    }

    public static function store(array $body, array $files = []): void
    {
        $errors = Validator::validate($body, [
            'activity_id' => 'nullable|exists:activities,id',
            'name'        => 'required|string',
            'role'        => 'nullable|string',
            'content'     => 'required|string',
            'rating'      => 'nullable|integer',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        $photoPath = null;
        if (!empty($files['photo'])) {
            try {
                $photoPath = ImageService::processUpload($files['photo'], 'testimonials');
            } catch (\RuntimeException $exception) {
                apiResponse(false, $exception->getMessage(), null, 422);
            }
        }

        $testimonial = Testimonial::create([
            'activity_id' => $body['activity_id'] ?? null,
            'name'        => trim($body['name']),
            'role'        => $body['role'] ?? null,
            'photo'       => $photoPath,
            'content'     => trim($body['content']),
            'rating'      => isset($body['rating']) ? (int) $body['rating'] : 5,
        ]);

        apiResponse(true, 'Testimonial created successfully', $testimonial->load('activity'), 201);
    }

    public static function show(int $id): void
    {
        $testimonial = Testimonial::with('activity')->find($id);
        if (!$testimonial) {
            apiResponse(false, 'Testimonial not found', null, 404);
        }

        apiResponse(true, 'Testimonial retrieved successfully', $testimonial);
    }

    public static function update(int $id, array $body, array $files = []): void
    {
        $testimonial = Testimonial::find($id);
        if (!$testimonial) {
            apiResponse(false, 'Testimonial not found', null, 404);
        }

        $errors = Validator::validate($body, [
            'activity_id' => 'nullable|exists:activities,id',
            'name'        => 'nullable|string',
            'role'        => 'nullable|string',
            'content'     => 'nullable|string',
            'rating'      => 'nullable|integer',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        if (!empty($files['photo'])) {
            try {
                $photoPath = ImageService::processUpload($files['photo'], 'testimonials');
                if ($testimonial->photo) {
                    ImageService::delete($testimonial->photo);
                }
                $testimonial->photo = $photoPath;
            } catch (\RuntimeException $exception) {
                apiResponse(false, $exception->getMessage(), null, 422);
            }
        }

        foreach (['activity_id', 'name', 'role', 'content'] as $field) {
            if (isset($body[$field])) $testimonial->{$field} = $body[$field];
        }
        if (isset($body['rating'])) $testimonial->rating = (int) $body['rating'];
        $testimonial->save();

        apiResponse(true, 'Testimonial updated successfully', $testimonial->load('activity'));
    }

    public static function destroy(int $id): void
    {
        $testimonial = Testimonial::find($id);
        if (!$testimonial) {
            apiResponse(false, 'Testimonial not found', null, 404);
        }

        if ($testimonial->photo) {
            ImageService::delete($testimonial->photo);
        }
        $testimonial->delete();

        apiResponse(true, 'Testimonial deleted successfully', null, 200);
    }
}
