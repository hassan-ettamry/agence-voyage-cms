<?php

namespace App\Policies;

use App\Models\Theme;
use App\Models\User;

class ThemePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('theme.view');
    }

    public function apply(User $user, Theme $theme): bool
    {
        return $user->hasPermission('theme.apply');
    }

    public function update(User $user): bool
    {
        return $user->hasPermission('theme.update');
    }
}
