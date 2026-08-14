<?php

namespace Tests\Feature;

use App\Models\SiteTemplate;
use App\Models\Theme;
use Database\Seeders\SiteTemplateSeeder;
use Database\Seeders\ThemeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SignatureTravelTemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_signature_travel_template_contains_an_editable_complete_homepage(): void
    {
        $this->seed(ThemeSeeder::class);
        $this->seed(SiteTemplateSeeder::class);
        $this->seed(SiteTemplateSeeder::class);

        $template = SiteTemplate::query()->where('slug', 'signature-travel')->firstOrFail();
        $home = collect($template->pages)->firstWhere('slug', 'home');
        $types = collect($home['structure'])->pluck('type')->all();

        $this->assertSame(Theme::query()->where('slug', 'signature-travel')->value('id'), $template->theme_id);
        $this->assertSame([
            'search-hero',
            'destination-carousel',
            'special-offers',
            'feature-grid',
            'stats-counter',
            'testimonials',
            'trust-logos',
            'cta-banner',
        ], $types);
        $this->assertSame('Go further. Travel deeper.', $home['structure'][0]['props']['title']);
        $this->assertSame('/contact', $home['structure'][7]['props']['url']);
        $this->assertSame(1, SiteTemplate::query()->where('slug', 'signature-travel')->count());
    }
}
