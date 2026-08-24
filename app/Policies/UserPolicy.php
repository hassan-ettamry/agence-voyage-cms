<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('user.view');
    }

    public function view(User $user, User $model): bool
    {
        return $this->sameAgency($user, $model)
            && $user->hasPermission('user.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('user.create');
    }

    public function update(User $user, User $model): bool
    {
        return $this->sameAgency($user, $model)
            && $user->hasPermission('user.update');
    }

    public function delete(User $user, User $model): bool
    {
        if (! $this->sameAgency($user, $model)
            || ! $user->hasPermission('user.delete')
            || $user->is($model)) {
            return false;
        }

        return ! $model->isOnlyAgencyAdmin();
    }

    protected function sameAgency(User $user, User $model): bool
    {
        return $user->agency_id === $model->agency_id;
    }
}
