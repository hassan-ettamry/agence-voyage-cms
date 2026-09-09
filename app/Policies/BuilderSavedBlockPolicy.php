<?php

namespace App\Policies;

use App\Models\BuilderSavedBlock;
use App\Models\User;

class BuilderSavedBlockPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('page.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('page.update');
    }

    public function update(User $user, BuilderSavedBlock $block): bool
    {
        return $user->agency_id === $block->agency_id && $user->hasPermission('page.update');
    }

    public function delete(User $user, BuilderSavedBlock $block): bool
    {
        return $this->update($user, $block);
    }
}
