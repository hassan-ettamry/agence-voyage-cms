<?php

namespace App\Http\Middleware;

use App\Support\AgencyContext;
use Closure;

class SetAgencyContext
{
    /**
     * Définir automatiquement le contexte d'agence pour chaque requête
     *
     * Ce middleware initialise l'agence courante à partir de l'utilisateur authentifié.
     * Cela permet de centraliser la gestion du multi-tenant (agency_id)
     * dans toute l'application (scopes, services, etc.).
     */
    public function handle($request, Closure $next)
    {
        AgencyContext::clear();

        if ($request->user()) {
            AgencyContext::set($request->user()->agency_id);
        }

        try {
            return $next($request);
        } finally {
            AgencyContext::clear();
        }
    }
}
