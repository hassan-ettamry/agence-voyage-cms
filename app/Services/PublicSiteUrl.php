<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\Destination;
use App\Models\Offer;
use App\Models\Page;

class PublicSiteUrl
{
    public function home(Agency $agency): string
    {
        return route('public.site.home', ['agencySlug' => $agency->slug]);
    }

    public function page(Agency $agency, Page|string $page): string
    {
        return route('public.site.pages.show', [
            'agencySlug' => $agency->slug,
            'pageSlug' => $page instanceof Page ? $page->slug : $page,
        ]);
    }

    public function destinations(Agency $agency): string
    {
        return route('public.site.destinations.index', ['agencySlug' => $agency->slug]);
    }

    public function destination(Agency $agency, Destination|string $destination): string
    {
        return route('public.site.destinations.show', [
            'agencySlug' => $agency->slug,
            'destinationSlug' => $destination instanceof Destination ? $destination->slug : $destination,
        ]);
    }

    public function offers(Agency $agency): string
    {
        return route('public.site.offers.index', ['agencySlug' => $agency->slug]);
    }

    public function offer(Agency $agency, Offer|string $offer): string
    {
        return route('public.site.offers.show', [
            'agencySlug' => $agency->slug,
            'offerSlug' => $offer instanceof Offer ? $offer->slug : $offer,
        ]);
    }

    public function fromStoredUrl(Agency $agency, ?string $url): ?string
    {
        if (! $url || str_starts_with($url, '#') || preg_match('/^[a-z][a-z0-9+.-]*:/i', $url)) {
            return $url;
        }

        $path = '/'.ltrim($url, '/');

        if ($path === '/') {
            return $this->home($agency);
        }

        if ($path === '/destinations') {
            return $this->destinations($agency);
        }

        if ($path === '/offers') {
            return $this->offers($agency);
        }

        if (preg_match('#^/pages/([^/]+)$#', $path, $matches)) {
            return $this->page($agency, $matches[1]);
        }

        if (preg_match('#^/destinations/([^/]+)$#', $path, $matches)) {
            return $this->destination($agency, $matches[1]);
        }

        if (preg_match('#^/offers/([^/]+)$#', $path, $matches)) {
            return $this->offer($agency, $matches[1]);
        }

        if (preg_match('#^/([^/]+)$#', $path, $matches)) {
            return $this->page($agency, $matches[1]);
        }

        return $url;
    }
}
