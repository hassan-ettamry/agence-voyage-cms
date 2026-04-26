<?php

namespace App\Http\Middleware;

use Closure;
use App\Support\AgencyContext;

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
        /**
         * Si un utilisateur est authentifié,
         * on injecte son agency_id dans le contexte global
         */
        if (auth()->check()) {
            AgencyContext::set(auth()->user()->agency_id);
        } else {
            AgencyContext::clear();
        }

        /**
         * Continuer le cycle de la requête
         */
        return $next($request);
    }
}