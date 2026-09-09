<?php

namespace App\Services;

use App\Models\MediaAsset;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class MediaService
{
    public function __construct(private SecureImageUploadService $imageUpload) {}

    public function upload(UploadedFile $file, array $data, User $user): MediaAsset
    {
        return $this->uploadMany([$file], $data, $user)->firstOrFail();
    }

    /**
     * @param  array<int, UploadedFile>  $files
     * @return Collection<int, MediaAsset>
     */
    public function uploadMany(array $files, array $data, User $user): Collection
    {
        $storedPaths = [];

        try {
            return DB::transaction(function () use ($files, $data, $user, &$storedPaths) {
                return collect($files)->map(function (UploadedFile $file) use ($data, $user, &$storedPaths) {
                    $stored = $this->imageUpload->store(
                        $file,
                        "agencies/{$user->agency_id}/media"
                    );
                    $storedPaths[] = $stored['path'];

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
                        'copyright_holder' => $data['copyright_holder'] ?? null,
                        'license' => $data['license'] ?? null,
                        'source_url' => $data['source_url'] ?? null,
                    ]);
                });
            });
        } catch (Throwable $exception) {
            foreach ($storedPaths as $path) {
                $this->imageUpload->delete($path);
            }

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
