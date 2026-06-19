<?php

namespace App\Services;

use App\Models\Destination;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DestinationService
{
    public function create(array $data, User $user): Destination
    {
        return DB::transaction(function () use ($data, $user) {
            $mediaIds = $data['media_ids'] ?? [];
            unset($data['media_ids']);

            $data['agency_id'] = $user->agency_id;
            $data['slug'] = $this->slug($data['slug'] ?? $data['name'], $user->agency_id);

            $destination = Destination::create($data);
            $this->syncMedia($destination, $mediaIds);

            return $destination;
        });
    }

    public function update(Destination $destination, array $data, User $user): Destination
    {
        return DB::transaction(function () use ($destination, $data, $user) {
            $mediaIds = $data['media_ids'] ?? [];
            unset($data['media_ids']);

            $slugBase = $data['slug'] ?: $data['name'];
            $data['slug'] = $this->slug($slugBase, $user->agency_id, $destination->id);

            $destination->update($data);
            $this->syncMedia($destination, $mediaIds);

            return $destination;
        });
    }

    public function delete(Destination $destination): void
    {
        if ($destination->offers()->exists()) {
            throw new \RuntimeException('Cette destination contient des offres et ne peut pas etre supprimee.');
        }

        $destination->delete();
    }

    private function syncMedia(Destination $destination, array $mediaIds): void
    {
        $sync = [];

        foreach (array_values(array_unique($mediaIds)) as $index => $id) {
            $sync[$id] = ['sort_order' => $index];
        }

        $destination->media()->sync($sync);
    }

    private function slug(string $base, string $agencyId, ?string $ignoreId = null): string
    {
        return Destination::uniqueSlug(Str::slug($base) ?: $base, $agencyId, $ignoreId);
    }
}
