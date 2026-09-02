<?php

namespace App\Controllers;

use App\Models\Volet;
use App\Validators\Validator;
use App\Services\ImageService;
use Illuminate\Database\Capsule\Manager as DB;

class VoletController extends Controller
{
    public static function index(): void
    {
        $query = Volet::query();
        self::applySearchAndSort($query, ['name', 'slogan', 'subtitle', 'description', 'place'], ['name', 'created_at']);
        self::paginate($query, 'Volets retrieved successfully');
    }

    public static function store(array $body, array $files = []): void
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

        $carouselImages = self::processCarouselImages($files, []);

        $volet = Volet::create([
            'name' => trim($body['name']),
            'slogan' => trim($body['slogan'] ?? ''),
            'subtitle' => trim($body['subtitle'] ?? ''),
            'description' => trim($body['description'] ?? ''),
            'target' => $body['target'] ?? 'women',
            'place' => trim($body['place'] ?? 'Ngozi & Bujumbura, Burundi'),
            'carousel_images' => $carouselImages,
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

    public static function update(int $id, array $body, array $files = []): void
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

        if (!empty($files['carousel_images'])) {
            $newImages = self::processCarouselImages($files, $volet->carousel_images ?? []);
            $volet->carousel_images = $newImages;
            $volet->save();
            apiResponse(true, 'Volet updated successfully', $volet);
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

        if (!empty($volet->carousel_images)) {
            foreach ($volet->carousel_images as $url) {
                ImageService::delete($url);
            }
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

    /**
     * Upload any carousel image files and merge them onto the existing image list.
     */
    private static function processCarouselImages(array $files, array $existing): array
    {
        $images = $existing;
        if (!empty($files['carousel_images'])) {
            $uploadedFiles = self::normalizeMultipleFiles($files['carousel_images']);
            foreach ($uploadedFiles as $file) {
                try {
                    $images[] = ImageService::processUpload($file, 'volets');
                } catch (\RuntimeException $e) {
                    // Skip failed individual images
                }
            }
        }
        return $images;
    }

    /**
     * Normalize PHP multiple file upload array format.
     */
    private static function normalizeMultipleFiles(array $fileGroup): array
    {
        if (isset($fileGroup['tmp_name']) && !is_array($fileGroup['tmp_name'])) {
            return [$fileGroup];
        }
        if (isset($fileGroup['tmp_name']) && is_array($fileGroup['tmp_name'])) {
            $files = [];
            foreach ($fileGroup['tmp_name'] as $index => $tmp) {
                if ($fileGroup['error'][$index] === UPLOAD_ERR_OK) {
                    $files[] = [
                        'name'     => $fileGroup['name'][$index],
                        'type'     => $fileGroup['type'][$index],
                        'tmp_name' => $tmp,
                        'error'    => $fileGroup['error'][$index],
                        'size'     => $fileGroup['size'][$index],
                    ];
                }
            }
            return $files;
        }
        return [];
    }
}
