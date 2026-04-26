<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\Page;
use App\Policies\PagePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapping policies
     */
    protected $policies = [
        Page::class => PagePolicy::class,
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