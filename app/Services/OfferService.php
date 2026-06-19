<?php

namespace App\Services;

use App\Models\Offer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OfferService
{
    public function create(array $data, User $user): Offer
    {
        return DB::transaction(function () use ($data, $user) {
            $data['agency_id'] = $user->agency_id;
            $data['slug'] = $this->slug($data['slug'] ?? $data['title'], $user->agency_id);

            return Offer::create($data);
        });
    }

    public function update(Offer $offer, array $data, User $user): Offer
    {
        return DB::transaction(function () use ($offer, $data, $user) {
            $slugBase = $data['slug'] ?: $data['title'];
            $data['slug'] = $this->slug($slugBase, $user->agency_id, $offer->id);

            $offer->update($data);

            return $offer;
        });
    }

    private function slug(string $base, string $agencyId, ?string $ignoreId = null): string
    {
        return Offer::uniqueSlug(Str::slug($base) ?: $base, $agencyId, $ignoreId);
    }
}
