<?php

namespace App\Policies;

use App\Models\SiteTemplate;
use App\Models\User;

class SiteTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('page.view')
            && $user->hasPermission('theme.view');
    }

    public function apply(User $user, SiteTemplate $siteTemplate): bool
    {
        return $siteTemplate->status === SiteTemplate::STATUS_ACTIVE
            && $user->hasPermission('page.create')
            && $user->hasPermission('page.update')
            && $user->hasPermission('page.delete')
            && $user->hasPermission('theme.apply');
    }
}
