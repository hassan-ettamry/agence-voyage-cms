<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Menu;
use App\Models\Page;
use App\Models\SiteTemplate;
use App\Models\Theme;
use App\Models\User;
use App\Services\SiteTemplateApplicationService;
use Database\Seeders\ComponentSeeder;
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
        $pages = collect($template->pages)->keyBy('slug');
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
        $this->assertSame(['home', 'about', 'contact', 'faq', 'privacy-policy', 'terms-and-conditions'], $pages->keys()->all());
        $this->assertSame(['hero', 'contact-info', 'contact-form', 'map', 'newsletter'], collect($pages['contact']['structure'])->pluck('type')->all());
        $this->assertSame(['hero', 'accordion', 'cta-banner'], collect($pages['faq']['structure'])->pluck('type')->all());
        $this->assertFalse($pages['privacy-policy']['include_in_menu']);
        $this->assertFalse($pages['terms-and-conditions']['include_in_menu']);
        $this->assertSame(1, SiteTemplate::query()->where('slug', 'signature-travel')->count());
    }

    public function test_signature_static_pages_can_be_applied_without_losing_the_menu_rules(): void
    {
        $this->seed(ComponentSeeder::class);
        $this->seed(ThemeSeeder::class);
        $this->seed(SiteTemplateSeeder::class);
        $agency = Agency::create(['name' => 'New Signature Agency', 'slug' => 'new-signature-agency', 'email' => 'agency@example.test', 'status' => 'active']);
        $user = User::withoutGlobalScopes()->create(['agency_id' => $agency->id, 'name' => 'Admin', 'email' => 'admin@example.test', 'password' => bcrypt('password')]);
        $template = SiteTemplate::query()->where('slug', 'signature-travel')->firstOrFail();

        app(SiteTemplateApplicationService::class)->apply($template, $user);

        $pages = Page::withoutGlobalScopes()->where('agency_id', $agency->id)->get()->keyBy('slug');
        $menu = Menu::withoutGlobalScopes()->where('agency_id', $agency->id)->where('is_default', true)->firstOrFail();
        $this->assertSame(['home', 'about', 'contact', 'faq', 'privacy-policy', 'terms-and-conditions'], $pages->keys()->all());
        $this->assertSame(4, $menu->items()->count());
        $this->assertSame(Page::STATUS_DRAFT, $pages['contact']->status);
        $this->assertSame('contact-info', $pages['contact']->structure[1]['type']);
        $this->assertSame($template->theme_id, $agency->fresh()->theme_id);
    }
}
