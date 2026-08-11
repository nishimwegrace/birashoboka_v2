<?php

namespace App\Controllers;

use App\Models\Post;
use App\Validators\Validator;

class PostController extends Controller
{
    public static function index(): void
    {
        $query = Post::with('volet');
        self::applySearchAndSort($query, ['title', 'description'], ['title', 'published_at', 'created_at']);
        self::paginate($query, 'Posts retrieved successfully');
    }

    public static function store(array $body): void
    {
        $errors = Validator::validate($body, [
            'volet_id' => 'required|exists:volets,id',
            'title' => 'required|string',
            'description' => 'required|string',
            'published_at' => 'nullable|date',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        $post = Post::create([
            'volet_id' => $body['volet_id'],
            'title' => trim($body['title']),
            'description' => trim($body['description']),
            'published_at' => $body['published_at'] ?? null,
        ]);

        apiResponse(true, 'Post created successfully', $post, 201);
    }

    public static function show(int $id): void
    {
        $post = Post::with('volet')->find($id);
        if (!$post) {
            apiResponse(false, 'Post not found', null, 404);
        }

        apiResponse(true, 'Post retrieved successfully', $post);
    }

    public static function update(int $id, array $body): void
    {
        $post = Post::find($id);
        if (!$post) {
            apiResponse(false, 'Post not found', null, 404);
        }

        $errors = Validator::validate($body, [
            'volet_id' => 'nullable|exists:volets,id',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'published_at' => 'nullable|date',
        ]);

        if (!empty($errors)) {
            apiResponse(false, 'Validation failed', $errors, 422);
        }

        foreach (['volet_id', 'title', 'description', 'published_at'] as $field) {
            if (isset($body[$field])) {
                $post->{$field} = $body[$field];
            }
        }
        $post->save();

        apiResponse(true, 'Post updated successfully', $post);
    }

    public static function destroy(int $id): void
    {
        $post = Post::find($id);
        if (!$post) {
            apiResponse(false, 'Post not found', null, 404);
        }

        $post->delete();
        apiResponse(true, 'Post deleted successfully', null, 200);
    }
}
