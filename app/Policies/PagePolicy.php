<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Page;

class PagePolicy
{
    /**
     *  Voir une page
     * Autorisé seulement si même agence
     */
    public function view(User $user, Page $page): bool
    {
        return $user->agency_id === $page->agency_id;
    }

    /**
     *  Voir la liste des pages
     */
    public function viewAny(User $user): bool
    {
        return true; // filtré automatiquement par global scope
    }

    /**
     *  Créer une page
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     *  Modifier une page
     */
    public function update(User $user, Page $page): bool
    {
        return $user->agency_id === $page->agency_id;
    }

    /**
     *  Supprimer une page
     */
    public function delete(User $user, Page $page): bool
    {
        return $user->agency_id === $page->agency_id;
    }
}