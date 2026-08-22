<?php

namespace App\Controllers;

use App\Models\Partner;
use App\Validators\Validator;
use App\Services\ImageService;

class PartnerController extends Controller
{
    public static function index(): void
    {
        $query = Partner::with('volet');
        self::applySearchAndSort($query, ['name', 'type'], ['name', 'created_at']);
        self::paginate($query, 'Partners retrieved successfully');
    }

    public static function store(array $body, array $files = []): void
    {
        $errors = Validator::validate($body, [
            'name'        => 'required|string',
            'volet_id'    => 'nullable|exists:volets,id',
            'type'        => 'nullable|string',
            'website_url' => 'nullable|string',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        $logoPath = null;
        if (!empty($files['logo'])) {
            try {
                $logoPath = ImageService::processUpload($files['logo'], 'partners');
            } catch (\RuntimeException $e) {
                apiResponse(false, $e->getMessage(), null, 422);
            }
        }

        $partner = Partner::create([
            'name'        => trim($body['name']),
            'volet_id'    => $body['volet_id'] ?? null,
            'logo'        => $logoPath,
            'type'        => $body['type'] ?? null,
            'website_url' => $body['website_url'] ?? null,
        ]);

        apiResponse(true, 'Partner created successfully', $partner->load('volet'), 201);
    }

    public static function show(int $id): void
    {
        $partner = Partner::with('volet')->find($id);
        if (!$partner) {
            apiResponse(false, 'Partner not found', null, 404);
        }

        apiResponse(true, 'Partner retrieved successfully', $partner);
    }

    public static function update(int $id, array $body, array $files = []): void
    {
        $partner = Partner::find($id);
        if (!$partner) {
            apiResponse(false, 'Partner not found', null, 404);
        }

        $errors = Validator::validate($body, [
            'name'        => 'nullable|string',
            'volet_id'    => 'nullable|exists:volets,id',
            'type'        => 'nullable|string',
            'website_url' => 'nullable|string',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        if (!empty($files['logo'])) {
            try {
                $newPath = ImageService::processUpload($files['logo'], 'partners');
                if ($partner->logo) {
                    ImageService::delete($partner->logo);
                }
                $partner->logo = $newPath;
            } catch (\RuntimeException $e) {
                apiResponse(false, $e->getMessage(), null, 422);
            }
        }

        if (isset($body['name']))        $partner->name        = trim($body['name']);
        if (array_key_exists('volet_id', $body)) $partner->volet_id = $body['volet_id'];
        if (isset($body['type']))        $partner->type        = $body['type'];
        if (isset($body['website_url'])) $partner->website_url = $body['website_url'];
        $partner->save();

        apiResponse(true, 'Partner updated successfully', $partner->load('volet'));
    }

    public static function destroy(int $id): void
    {
        $partner = Partner::find($id);
        if (!$partner) {
            apiResponse(false, 'Partner not found', null, 404);
        }

        if ($partner->logo) {
            ImageService::delete($partner->logo);
        }
        $partner->delete();
        apiResponse(true, 'Partner deleted successfully', null, 200);
    }
}
