<?php

namespace App\Services;

use App\Models\MediaAsset;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class MediaService
{
    public function __construct(private SecureImageUploadService $imageUpload) {}

    public function upload(UploadedFile $file, array $data, User $user): MediaAsset
    {
        $stored = $this->imageUpload->store(
            $file,
            "agencies/{$user->agency_id}/media"
        );

        try {
            return DB::transaction(function () use ($stored, $data, $user) {
                return MediaAsset::create([
                    'agency_id' => $user->agency_id,
                    'filename' => $stored['filename'],
                    'original_name' => $stored['original_name'],
                    'path' => $stored['path'],
                    'mime_type' => $stored['mime_type'],
                    'size' => $stored['size'],
                    'title' => $data['title'] ?? Str::limit(
                        pathinfo($stored['original_name'], PATHINFO_FILENAME),
                        255,
                        ''
                    ),
                    'alt_text' => $data['alt_text'] ?? null,
                ]);
            });
        } catch (Throwable $exception) {
            $this->imageUpload->delete($stored['path']);

            throw $exception;
        }
    }

    public function update(MediaAsset $media, array $data): MediaAsset
    {
        $media->update($data);

        return $media;
    }

    public function delete(MediaAsset $media): void
    {
        if ($media->isUsed()) {
            throw new \RuntimeException('Ce media est utilise et ne peut pas etre supprime.');
        }

        $this->imageUpload->delete($media->path);
        $media->delete();
    }
}
