<?php

namespace App\Policies;

use App\Models\MediaAsset;
use App\Models\User;

class MediaAssetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('media.view');
    }

    public function view(User $user, MediaAsset $media): bool
    {
        return $this->sameAgency($user, $media) && $user->hasPermission('media.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('media.upload');
    }

    public function update(User $user, MediaAsset $media): bool
    {
        return $this->sameAgency($user, $media) && $user->hasPermission('media.update');
    }

    public function delete(User $user, MediaAsset $media): bool
    {
        return $this->sameAgency($user, $media) && $user->hasPermission('media.delete');
    }

    private function sameAgency(User $user, MediaAsset $media): bool
    {
        return $user->agency_id === $media->agency_id;
    }
}
