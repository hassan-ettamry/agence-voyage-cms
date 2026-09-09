<?php

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    private const RETIRED_DEMO_SLUGS = [
        'ocean-blue',
        'sunset-luxe',
        'wild-nature',
        'urban-blue',
        'coastal-glow',
        'heritage-rose',
        'mountain-escape',
        'city-journey',
        'desert-roads',
        'urban-life',
        'beach-paradise',
        'culture-journey',
        'wellness-retreat',
    ];

    public function run(): void
    {
        Theme::updateOrCreate(
            ['slug' => 'signature-travel'],
            [
                'name' => 'Signature Travel',
                'preview' => 'images/site-templates/culture-journey.png',
                'variables' => [
                    'primary' => '#c84c2f',
                    'secondary' => '#102a2f',
                    'accent' => '#d8ad54',
                    'background' => '#f7f4ed',
                    'surface' => '#ffffff',
                    'text' => '#17231f',
                    'muted' => '#69736e',
                    'border' => '#d9ded8',
                    'bodyFont' => 'ui-sans-serif, system-ui, sans-serif',
                    'headingFont' => 'Georgia, "Times New Roman", serif',
                    'radius' => '6px',
                    'shadow' => 'soft',
                ],
                'status' => Theme::STATUS_ACTIVE,
            ]
        );

        Theme::query()->whereIn('slug', self::RETIRED_DEMO_SLUGS)->delete();
    }
}
