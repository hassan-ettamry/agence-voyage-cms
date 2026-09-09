<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Page;

class PagePolicy
{
    /**
     * Voir une page
     */
    public function view(User $user, Page $page): bool
    {
        return $this->sameAgency($user, $page)
            && $user->hasPermission('page.view');
    }

    /**
     * Liste des pages
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('page.view');
    }

    /**
     * Créer
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('page.create');
    }

    /**
     * Modifier
     */
    public function update(User $user, Page $page): bool
    {
        return $this->sameAgency($user, $page)
            && $user->hasPermission('page.update');
    }

    /**
     * Supprimer
     */
    public function delete(User $user, Page $page): bool
    {
        return $this->sameAgency($user, $page)
            && $user->hasPermission('page.delete');
    }

    /**
     * helper
     */
    protected function sameAgency(User $user, Page $page): bool
    {
        return $user->agency_id === $page->agency_id;
    }
}