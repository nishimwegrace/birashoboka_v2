<?php

namespace App\Controllers;

use App\Models\Post;
use App\Validators\Validator;
use App\Services\ImageService;

class PostController extends Controller
{
    public static function index(): void
    {
        $query = Post::with('volet');
        self::applySearchAndSort($query, ['title', 'description'], ['title', 'published_at', 'created_at']);
        self::paginate($query, 'Posts retrieved successfully');
    }

    public static function store(array $body, array $files = []): void
    {
        $errors = Validator::validate($body, [
            'volet_id'    => 'required|exists:volets,id',
            'title'       => 'required|string',
            'description' => 'required|string',
            'published_at' => 'nullable|date',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        // Handle featured_image upload
        $featuredImagePath = null;
        if (!empty($files['featured_image'])) {
            try {
                $featuredImagePath = ImageService::processUpload($files['featured_image'], 'posts');
            } catch (\RuntimeException $e) {
                apiResponse(false, $e->getMessage(), null, 422);
            }
        }

        // Handle multiple image_urls uploads
        $imageUrls = [];
        if (!empty($files['image_urls'])) {
            $uploadedFiles = self::normalizeMultipleFiles($files['image_urls']);
            foreach ($uploadedFiles as $file) {
                try {
                    $imageUrls[] = ImageService::processUpload($file, 'posts');
                } catch (\RuntimeException $e) {
                    // Skip failed individual images
                }
            }
        }

        $post = Post::create([
            'volet_id'       => $body['volet_id'],
            'title'          => trim($body['title']),
            'description'    => trim($body['description']),
            'featured_image' => $featuredImagePath,
            'image_urls'     => !empty($imageUrls) ? $imageUrls : null,
            'published_at'   => $body['published_at'] ?? null,
        ]);

        apiResponse(true, 'Post created successfully', $post->load('volet'), 201);
    }

    public static function show(int $id): void
    {
        $post = Post::with('volet')->find($id);
        if (!$post) {
            apiResponse(false, 'Post not found', null, 404);
        }

        apiResponse(true, 'Post retrieved successfully', $post);
    }

    public static function update(int $id, array $body, array $files = []): void
    {
        $post = Post::find($id);
        if (!$post) {
            apiResponse(false, 'Post not found', null, 404);
        }

        $errors = Validator::validate($body, [
            'volet_id'    => 'nullable|exists:volets,id',
            'title'       => 'nullable|string',
            'description' => 'nullable|string',
            'published_at' => 'nullable|date',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        // Handle featured_image replacement
        if (!empty($files['featured_image'])) {
            try {
                $newPath = ImageService::processUpload($files['featured_image'], 'posts');
                if ($post->featured_image) {
                    ImageService::delete($post->featured_image);
                }
                $post->featured_image = $newPath;
            } catch (\RuntimeException $e) {
                apiResponse(false, $e->getMessage(), null, 422);
            }
        }

        // Handle additional image_urls
        if (!empty($files['image_urls'])) {
            $uploadedFiles = self::normalizeMultipleFiles($files['image_urls']);
            $existingUrls  = $post->image_urls ?? [];
            foreach ($uploadedFiles as $file) {
                try {
                    $existingUrls[] = ImageService::processUpload($file, 'posts');
                } catch (\RuntimeException $e) {
                    // Skip failed
                }
            }
            $post->image_urls = $existingUrls;
        }

        foreach (['volet_id', 'title', 'description', 'published_at'] as $field) {
            if (isset($body[$field])) {
                $post->{$field} = $body[$field];
            }
        }
        $post->save();

        apiResponse(true, 'Post updated successfully', $post->load('volet'));
    }

    public static function destroy(int $id): void
    {
        $post = Post::find($id);
        if (!$post) {
            apiResponse(false, 'Post not found', null, 404);
        }

        if ($post->featured_image) {
            ImageService::delete($post->featured_image);
        }
        if (!empty($post->image_urls)) {
            foreach ($post->image_urls as $url) {
                ImageService::delete($url);
            }
        }
        $post->delete();
        apiResponse(true, 'Post deleted successfully', null, 200);
    }

    /**
     * Normalize PHP multiple file upload array format.
     * When <input type="file" multiple> is used, $_FILES['image_urls'] has a different structure.
     */
    private static function normalizeMultipleFiles(array $fileGroup): array
    {
        // If already a flat single file array
        if (isset($fileGroup['tmp_name']) && !is_array($fileGroup['tmp_name'])) {
            return [$fileGroup];
        }
        // Multiple files
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
