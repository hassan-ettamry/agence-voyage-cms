<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\Page;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\Destination;
use App\Models\MediaAsset;
use App\Models\Offer;
use App\Policies\DestinationPolicy;
use App\Policies\MediaAssetPolicy;
use App\Policies\OfferPolicy;
use App\Policies\PagePolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use App\Models\Theme;
use App\Policies\ThemePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapping policies
     */
    protected $policies = [
        Page::class => PagePolicy::class,
        Destination::class => DestinationPolicy::class,
        Offer::class => OfferPolicy::class,
        MediaAsset::class => MediaAssetPolicy::class,
        Permission::class => PermissionPolicy::class,
        Role::class => RolePolicy::class,
        User::class => UserPolicy::class,
        Theme::class => ThemePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // SUPER ADMIN
        Gate::before(function ($user, $ability) {
            return $user->isAdmin() ? true : null;
        });
    }
}
