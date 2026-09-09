<?php

namespace App\Policies;

use App\Models\Offer;
use App\Models\User;

class OfferPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('offer.view');
    }

    public function view(User $user, Offer $offer): bool
    {
        return $this->sameAgency($user, $offer) && $user->hasPermission('offer.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('offer.create');
    }

    public function update(User $user, Offer $offer): bool
    {
        return $this->sameAgency($user, $offer) && $user->hasPermission('offer.update');
    }

    public function delete(User $user, Offer $offer): bool
    {
        return $this->sameAgency($user, $offer) && $user->hasPermission('offer.delete');
    }

    private function sameAgency(User $user, Offer $offer): bool
    {
        return $user->agency_id === $offer->agency_id;
    }
}
