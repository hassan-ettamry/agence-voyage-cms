<?php

namespace App\Services;

use App\Models\MediaAsset;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    public function upload(UploadedFile $file, array $data, User $user): MediaAsset
    {
        return DB::transaction(function () use ($file, $data, $user) {
            $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs("agencies/{$user->agency_id}/media", $filename, 'public');

            return MediaAsset::create([
                'agency_id' => $user->agency_id,
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                'size' => $file->getSize() ?: 0,
                'title' => $data['title'] ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'alt_text' => $data['alt_text'] ?? null,
            ]);
        });
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

        Storage::disk('public')->delete($media->path);
        $media->delete();
    }
}
