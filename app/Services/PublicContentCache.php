<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class PublicContentCache
{
    public static function pageKey(string $agencyId, string $slug): string
    {
        return "page:{$agencyId}:{$slug}";
    }

    public static function destinationKey(string $agencyId, string $slug): string
    {
        return "destination:{$agencyId}:{$slug}";
    }

    public static function offerKey(string $agencyId, string $slug): string
    {
        return "offer:{$agencyId}:{$slug}";
    }

    public static function forgetPage(string $agencyId, ?string ...$slugs): void
    {
        self::forget('page', $agencyId, $slugs);
    }

    public static function forgetDestination(string $agencyId, ?string ...$slugs): void
    {
        self::forget('destination', $agencyId, $slugs);
    }

    public static function forgetOffer(string $agencyId, ?string ...$slugs): void
    {
        self::forget('offer', $agencyId, $slugs);
    }

    private static function forget(string $type, string $agencyId, array $slugs): void
    {
        foreach (array_unique(array_filter($slugs)) as $slug) {
            Cache::forget("{$type}:{$agencyId}:{$slug}");
        }
    }
}
