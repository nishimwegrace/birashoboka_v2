<?php

namespace App\Controllers;

use App\Models\Partner;
use App\Validators\Validator;

class PartnerController extends Controller
{
    public static function index(): void
    {
        $query = Partner::with('volet');
        self::applySearchAndSort($query, ['name'], ['name', 'created_at']);
        self::paginate($query, 'Partners retrieved successfully');
    }

    public static function store(array $body): void
    {
        $errors = Validator::validate($body, [
            'name' => 'required|string',
            'volet_id' => 'nullable|exists:volets,id',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        $partner = Partner::create([
            'name' => trim($body['name']),
            'volet_id' => $body['volet_id'] ?? null,
        ]);

        apiResponse(true, 'Partner created successfully', $partner, 201);
    }

    public static function show(int $id): void
    {
        $partner = Partner::with('volet')->find($id);
        if (!$partner) {
            apiResponse(false, 'Partner not found', null, 404);
        }

        apiResponse(true, 'Partner retrieved successfully', $partner);
    }

    public static function update(int $id, array $body): void
    {
        $partner = Partner::find($id);
        if (!$partner) {
            apiResponse(false, 'Partner not found', null, 404);
        }

        $errors = Validator::validate($body, [
            'name' => 'nullable|string',
            'volet_id' => 'nullable|exists:volets,id',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        if (isset($body['name'])) {
            $partner->name = trim($body['name']);
        }
        if (array_key_exists('volet_id', $body)) {
            $partner->volet_id = $body['volet_id'];
        }
        $partner->save();

        apiResponse(true, 'Partner updated successfully', $partner);
    }

    public static function destroy(int $id): void
    {
        $partner = Partner::find($id);
        if (!$partner) {
            apiResponse(false, 'Partner not found', null, 404);
        }

        $partner->delete();
        apiResponse(true, 'Partner deleted successfully', null, 200);
    }
}
