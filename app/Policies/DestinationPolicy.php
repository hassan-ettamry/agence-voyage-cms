<?php

namespace App\Policies;

use App\Models\Destination;
use App\Models\User;

class DestinationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('destination.view');
    }

    public function view(User $user, Destination $destination): bool
    {
        return $this->sameAgency($user, $destination) && $user->hasPermission('destination.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('destination.create');
    }

    public function update(User $user, Destination $destination): bool
    {
        return $this->sameAgency($user, $destination) && $user->hasPermission('destination.update');
    }

    public function delete(User $user, Destination $destination): bool
    {
        return $this->sameAgency($user, $destination) && $user->hasPermission('destination.delete');
    }

    private function sameAgency(User $user, Destination $destination): bool
    {
        return $user->agency_id === $destination->agency_id;
    }
}
