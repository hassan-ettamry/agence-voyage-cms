<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('role.view');
    }

    public function view(User $user, Role $role): bool
    {
        return $this->sameAgency($user, $role)
            && $user->hasPermission('role.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('role.create');
    }

    public function update(User $user, Role $role): bool
    {
        return $this->sameAgency($user, $role)
            && $user->hasPermission('role.update');
    }

    public function delete(User $user, Role $role): bool
    {
        return $this->sameAgency($user, $role)
            && $role->slug !== 'admin'
            && $user->hasPermission('role.delete');
    }

    private function sameAgency(User $user, Role $role): bool
    {
        return $user->agency_id === $role->agency_id;
    }
}
