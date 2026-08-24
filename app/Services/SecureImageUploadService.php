<?php

namespace App\Services;

use App\Support\SecureImageRules;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class SecureImageUploadService
{
    private const MIME_EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    private const MAX_PIXELS = 24_000_000;

    public function store(
        UploadedFile $file,
        string $directory,
        string $field = 'file',
        string $disk = 'public'
    ): array {
        $metadata = $this->validate($file, $field);
        $filename = Str::uuid().'.'.$metadata['extension'];
        $path = $file->storeAs(trim($directory, '/'), $filename, $disk);

        if (! is_string($path) || $path === '') {
            throw new RuntimeException('The image could not be stored.');
        }

        return [
            'path' => $path,
            'filename' => $filename,
            'mime_type' => $metadata['mime_type'],
            'size' => (int) ($file->getSize() ?: 0),
            'width' => $metadata['width'],
            'height' => $metadata['height'],
            'original_name' => $this->safeOriginalName($file),
        ];
    }

    public function delete(?string $path, string $disk = 'public'): void
    {
        if (is_string($path) && $path !== '') {
            Storage::disk($disk)->delete($path);
        }
    }

    private function validate(UploadedFile $file, string $field): array
    {
        $mimeType = $file->getMimeType();
        $extension = is_string($mimeType)
            ? (self::MIME_EXTENSIONS[$mimeType] ?? null)
            : null;
        $imageSize = $file->getRealPath() !== false
            ? @getimagesize($file->getRealPath())
            : false;

        if (! $file->isValid() || $extension === null || $imageSize === false) {
            throw ValidationException::withMessages([
                $field => 'Upload a valid JPG, PNG or WebP image.',
            ]);
        }

        [$width, $height] = $imageSize;

        if (
            $width > SecureImageRules::MAX_DIMENSION
            || $height > SecureImageRules::MAX_DIMENSION
            || ($width * $height) > self::MAX_PIXELS
        ) {
            throw ValidationException::withMessages([
                $field => 'The image dimensions are too large.',
            ]);
        }

        if (($file->getSize() ?: 0) > SecureImageRules::MAX_KILOBYTES * 1024) {
            throw ValidationException::withMessages([
                $field => 'The image may not be larger than 5 MB.',
            ]);
        }

        return [
            'mime_type' => $mimeType,
            'extension' => $extension,
            'width' => $width,
            'height' => $height,
        ];
    }

    private function safeOriginalName(UploadedFile $file): string
    {
        $name = basename(str_replace('\\', '/', $file->getClientOriginalName()));

        return Str::limit($name !== '' ? $name : 'image', 255, '');
    }
}
