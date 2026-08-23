<?php

namespace App\Services;

use Intervention\Image\ImageManagerStatic as Image;

class ImageService
{
    protected const MAX_FILE_SIZE = 20 * 1024 * 1024; // 20 MB
    protected const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    protected const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];

    public static function processUpload(array $file, string $folder): string
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new \RuntimeException('Invalid file upload.');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Failed to upload image.');
        }

        if ($file['size'] > self::MAX_FILE_SIZE) {
            throw new \RuntimeException('Image size exceeds the maximum allowed size.');
        }

        $info = getimagesize($file['tmp_name']);
        if ($info === false || !in_array($info['mime'], self::ALLOWED_MIMES, true)) {
            throw new \RuntimeException('Unsupported image format.');
        }

        $targetDirectory = __DIR__ . '/../../storage/uploads/' . trim($folder, '/');
        if (!is_dir($targetDirectory)) {
            @mkdir($targetDirectory, 0777, true);
        }
        if (!is_writable($targetDirectory)) {
            @chmod($targetDirectory, 0777);
            @chmod(dirname($targetDirectory), 0777);
        }

        $filename = bin2hex(random_bytes(12)) . '.webp';
        $outputPath = $targetDirectory . '/' . $filename;

        try {
            Image::configure(['driver' => 'gd']);
            $image = Image::make($file['tmp_name']);
            
            if (function_exists('exif_read_data')) {
                try {
                    $image->orientate();
                } catch (\Throwable $e) {
                    // Ignore EXIF errors if EXIF data is unreadable or unsupported
                }
            }

            $maxWidth = (int) env('IMAGE_MAX_WIDTH', 1600);
            $maxHeight = (int) env('IMAGE_MAX_HEIGHT', 1600);
            $image->resize($maxWidth, $maxHeight, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $quality = (int) env('IMAGE_QUALITY', 82);
            $image->encode('webp', $quality)->save($outputPath, $quality, 'webp');
        } catch (\Throwable $e) {
            // Fallback: if GD image processing or encoding fails, save the uploaded file directly
            $rawExtension = self::getExtensionFromMime($info['mime']);
            $filename = bin2hex(random_bytes(12)) . '.' . $rawExtension;
            $outputPath = $targetDirectory . '/' . $filename;

            if (!@copy($file['tmp_name'], $outputPath)) {
                throw new \RuntimeException("Can't write image data to path ({$outputPath})");
            }
        }

        return 'storage/uploads/' . trim($folder, '/') . '/' . $filename;
    }

    public static function delete(string $path): void
    {
        $storagePath = __DIR__ . '/../../storage/' . ltrim($path, '/');
        if (is_file($storagePath)) {
            unlink($storagePath);
        }
    }

    private static function getExtensionFromMime(string $mime): string
    {
        return match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => 'webp',
        };
    }
}
