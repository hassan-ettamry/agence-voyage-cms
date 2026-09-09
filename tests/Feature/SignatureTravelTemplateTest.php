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
            'image-text',
            'destination-carousel',
            'special-offers',
            'feature-grid',
            'stats-counter',
            'testimonials',
            'trust-logos',
            'cta-banner',
        ], $types);
        $this->assertSame('Go further. Travel deeper.', $home['structure'][0]['props']['title']);
        $this->assertSame('/images/site-templates/mountain-adventure.webp', $home['structure'][0]['props']['backgroundImage']);
        $this->assertSame(700, $home['structure'][0]['props']['minHeight']);
        $this->assertSame('Your journey should feel like your own', $home['structure'][1]['props']['title']);
        $this->assertSame(4, $home['structure'][2]['props']['limit']);
        $this->assertSame('surface', $home['structure'][3]['props']['sectionTone']);
        $this->assertSame('/contact', $home['structure'][8]['props']['url']);
        $this->assertSame(['home', 'destinations', 'offers', 'about', 'contact', 'faq', 'privacy-policy', 'terms-and-conditions'], $pages->keys()->all());
        $this->assertSame('/destinations', $pages['destinations']['menu_url']);
        $this->assertSame('/offers', $pages['offers']['menu_url']);
        $this->assertSame(['hero', 'section', 'map', 'newsletter'], collect($pages['contact']['structure'])->pluck('type')->all());
        $this->assertSame('standalone', $pages['contact']['structure'][2]['props']['presentation']);
        $this->assertSame(360, $pages['contact']['structure'][2]['props']['height']);
        $contactTypes = collect($this->flattenTypes($pages['contact']['structure']));
        $this->assertTrue($contactTypes->contains('contact-info'));
        $this->assertTrue($contactTypes->contains('contact-form'));
        $this->assertSame('yes', $pages['destinations']['structure'][1]['props']['catalogMode']);
        $this->assertSame('featured', $pages['destinations']['structure'][1]['props']['defaultSort']);
        $this->assertSame('yes', $pages['offers']['structure'][1]['props']['catalogMode']);
        $this->assertSame('special', $pages['offers']['structure'][1]['props']['defaultSort']);
        $this->assertSame(['hero', 'accordion', 'cta-banner'], collect($pages['faq']['structure'])->pluck('type')->all());
        $this->assertFalse($pages['privacy-policy']['include_in_menu']);
        $this->assertFalse($pages['terms-and-conditions']['include_in_menu']);
        $this->assertSame(1, SiteTemplate::query()->where('slug', 'signature-travel')->count());
        $this->assertSame(1, SiteTemplate::query()->count());
        $this->assertSame(1, Theme::query()->count());
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
        $this->assertSame(['home', 'destinations', 'offers', 'about', 'contact', 'faq', 'privacy-policy', 'terms-and-conditions'], $pages->keys()->all());
        $this->assertSame(6, $menu->items()->count());
        $this->assertDatabaseHas('menu_items', ['menu_id' => $menu->id, 'title' => 'Destinations', 'page_id' => null, 'url' => '/destinations']);
        $this->assertDatabaseHas('menu_items', ['menu_id' => $menu->id, 'title' => 'Offers', 'page_id' => null, 'url' => '/offers']);
        $this->assertSame(Page::STATUS_DRAFT, $pages['contact']->status);
        $this->assertSame('section', $pages['contact']->structure[1]['type']);
        $this->assertSame($template->theme_id, $agency->fresh()->theme_id);
    }

    private function flattenTypes(array $nodes): array
    {
        $types = [];

        foreach ($nodes as $node) {
            $types[] = $node['type'] ?? null;
            $types = [...$types, ...$this->flattenTypes($node['children'] ?? [])];
        }

        return array_values(array_filter($types));
    }
}
