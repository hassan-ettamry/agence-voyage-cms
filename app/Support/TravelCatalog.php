<?php

namespace App\Support;

class TravelCatalog
{
    public const CONTINENTS = [
        'africa' => 'Africa',
        'asia' => 'Asia',
        'europe' => 'Europe',
        'north-america' => 'North America',
        'south-america' => 'South America',
        'oceania' => 'Oceania',
        'antarctica' => 'Antarctica',
    ];

    public const TRAVEL_TYPES = [
        'beach' => 'Beach',
        'mountain' => 'Mountain',
        'cultural' => 'Cultural',
        'adventure' => 'Adventure',
        'city' => 'City break',
        'desert' => 'Desert',
        'nature' => 'Nature',
        'wellness' => 'Wellness',
        'family' => 'Family',
    ];

    public const MONTHS = [
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December',
    ];

    public static function continentLabel(?string $continent): ?string
    {
        return $continent ? (self::CONTINENTS[$continent] ?? $continent) : null;
    }

    public static function travelTypeLabel(string $type): string
    {
        return self::TRAVEL_TYPES[$type] ?? $type;
    }

    public static function monthLabel(int|string $month): ?string
    {
        return self::MONTHS[(int) $month] ?? null;
    }
}
