<?php

namespace App\Controllers;

use App\Models\Volet;
use App\Validators\Validator;
use Illuminate\Database\Capsule\Manager as DB;

class VoletController extends Controller
{
    public static function index(): void
    {
        $query = Volet::query();
        self::applySearchAndSort($query, ['name', 'slogan', 'subtitle', 'description', 'place'], ['name', 'created_at']);
        self::paginate($query, 'Volets retrieved successfully');
    }

    public static function store(array $body): void
    {
        $errors = Validator::validate($body, [
            'name' => 'required|string',
            'slogan' => 'nullable|string',
            'subtitle' => 'nullable|string',
            'description' => 'nullable|string',
            'target' => 'nullable|in:young,women,all',
            'place' => 'nullable|string',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        $volet = Volet::create([
            'name' => trim($body['name']),
            'slogan' => trim($body['slogan'] ?? ''),
            'subtitle' => trim($body['subtitle'] ?? ''),
            'description' => trim($body['description'] ?? ''),
            'target' => $body['target'] ?? 'women',
            'place' => trim($body['place'] ?? 'Ngozi & Bujumbura, Burundi'),
        ]);

        apiResponse(true, 'Volet created successfully', $volet, 201);
    }

    public static function show(int $id): void
    {
        $volet = Volet::with(['activities', 'partners', 'posts', 'campaigns'])->find($id);
        if (!$volet) {
            apiResponse(false, 'Volet not found', null, 404);
        }

        apiResponse(true, 'Volet retrieved successfully', $volet);
    }

    public static function update(int $id, array $body): void
    {
        $volet = Volet::find($id);
        if (!$volet) {
            apiResponse(false, 'Volet not found', null, 404);
        }

        $errors = Validator::validate($body, [
            'name' => 'nullable|string',
            'slogan' => 'nullable|string',
            'subtitle' => 'nullable|string',
            'description' => 'nullable|string',
            'target' => 'nullable|in:young,women,all',
            'place' => 'nullable|string',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        foreach (['name', 'slogan', 'subtitle', 'description', 'target', 'place'] as $field) {
            if (isset($body[$field])) {
                $volet->{$field} = $body[$field];
            }
        }
        $volet->save();

        apiResponse(true, 'Volet updated successfully', $volet);
    }

    public static function destroy(int $id): void
    {
        $volet = Volet::find($id);
        if (!$volet) {
            apiResponse(false, 'Volet not found', null, 404);
        }

        $volet->delete();
        apiResponse(true, 'Volet deleted successfully', null, 200);
    }

    public static function activities(int $id): void
    {
        $volet = Volet::find($id);
        if (!$volet) {
            apiResponse(false, 'Volet not found', null, 404);
        }

        $query = $volet->activities();
        self::applySearchAndSort($query, ['title', 'description'], ['title', 'created_at']);
        self::paginate($query, 'Volet activities retrieved successfully');
    }

    public static function partners(int $id): void
    {
        $volet = Volet::find($id);
        if (!$volet) {
            apiResponse(false, 'Volet not found', null, 404);
        }

        $query = $volet->partners();
        self::applySearchAndSort($query, ['name'], ['name', 'created_at']);
        self::paginate($query, 'Volet partners retrieved successfully');
    }

    public static function posts(int $id): void
    {
        $volet = Volet::find($id);
        if (!$volet) {
            apiResponse(false, 'Volet not found', null, 404);
        }

        $query = $volet->posts();
        self::applySearchAndSort($query, ['title', 'description'], ['title', 'published_at']);
        self::paginate($query, 'Volet posts retrieved successfully');
    }

    public static function campaigns(int $id): void
    {
        $volet = Volet::find($id);
        if (!$volet) {
            apiResponse(false, 'Volet not found', null, 404);
        }

        $query = $volet->campaigns();
        self::applySearchAndSort($query, ['title', 'description', 'edition', 'place'], ['start_date', 'registration_start', 'created_at']);
        self::paginate($query, 'Volet campaigns retrieved successfully');
    }
}
