<?php

namespace App\Http\Middleware;

use App\Models\Agency;
use App\Support\AgencyContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolvePublicAgency
{
    public function handle(Request $request, Closure $next): Response
    {
        $slug = (string) $request->route('agencySlug');

        $agency = Agency::query()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        AgencyContext::set($agency->id);
        $request->attributes->set('publicAgency', $agency);

        try {
            return $next($request);
        } finally {
            AgencyContext::clear();
        }
    }
}
