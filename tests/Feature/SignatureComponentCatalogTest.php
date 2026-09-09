<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Component;
use App\Models\Destination;
use App\Models\Offer;
use App\Models\Page;
use Database\Seeders\ComponentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SignatureComponentCatalogTest extends TestCase
{
    use RefreshDatabase;

    private const TYPES = [
        'search-hero', 'image-text', 'feature-grid', 'destination-carousel',
        'offer-comparison', 'testimonials', 'trust-logos', 'stats-counter',
        'cta-banner', 'newsletter', 'accordion', 'tabs', 'contact-info',
        'social-links',
    ];

    public function test_signature_components_are_seeded_once_with_views_and_editable_schemas(): void
    {
        $this->seed(ComponentSeeder::class);
        $this->seed(ComponentSeeder::class);

        $components = Component::query()->whereIn('type', self::TYPES)->get()->keyBy('type');

        $this->assertCount(count(self::TYPES), $components);

        foreach (self::TYPES as $type) {
            $this->assertArrayHasKey($type, $components);
            $this->assertNotEmpty(data_get($components[$type]->schema_json, 'tabs.content.fields'));
            $this->assertNotEmpty(data_get($components[$type]->schema_json, 'tabs.responsive.fields'));
            $this->assertTrue(view()->exists("components.builder.{$type}"));
        }
    }

    public function test_signature_components_render_on_a_public_agency_page_with_tenant_urls(): void
    {
        $this->seed(ComponentSeeder::class);
        $agency = Agency::create([
            'name' => 'Signature Atlas',
            'slug' => 'signature-atlas',
            'email' => 'hello@signature-atlas.test',
            'phone' => '+212 500 000 000',
            'address' => 'Marrakech, Morocco',
            'status' => 'active',
        ]);

        $nodes = Component::query()->whereIn('type', self::TYPES)->get()->map(function (Component $component) {
            $fields = data_get($component->schema_json, 'tabs.content.fields', []);
            $defaults = collect($fields)->mapWithKeys(fn (array $field, string $key) => [$key => $field['default'] ?? null])->all();

            return ['id' => (string) Str::uuid(), 'type' => $component->type, 'props' => $defaults, 'children' => []];
        })->values()->all();

        Page::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'title' => 'Signature component gallery',
            'slug' => 'home',
            'status' => Page::STATUS_PUBLISHED,
            'structure' => $nodes,
        ]);

        $this->get(route('public.site.home', $agency->slug))
            ->assertOk()
            ->assertSee('Where will you go next?')
            ->assertSee('Every detail, thoughtfully handled')
            ->assertSee('What our travellers say')
            ->assertSee('Talk to a travel designer')
            ->assertSee(route('public.site.destinations.index', $agency->slug), false)
            ->assertSee(route('public.site.pages.show', [$agency->slug, 'contact']), false);
    }

    public function test_signature_component_responsive_visibility_is_rendered_as_css_classes(): void
    {
        $this->seed(ComponentSeeder::class);
        $renderer = app(\App\Services\Renderer\PageRenderer::class);
        $html = $renderer->render([[
            'id' => 'responsive-cta',
            'type' => 'cta-banner',
            'props' => [
                'title' => 'Responsive CTA',
                'url' => '/contact',
                'hideOnMobile' => 'yes',
                'hideOnTablet' => 'yes',
            ],
            'children' => [],
        ]], 'preview');

        $this->assertStringContainsString('site-hide-mobile', $html);
        $this->assertStringContainsString('site-hide-tablet', $html);
        $this->assertStringContainsString('Responsive CTA', $html);
    }

    public function test_travel_components_render_builder_fallback_images_when_content_has_no_media(): void
    {
        $this->seed(ComponentSeeder::class);
        $agency = Agency::create([
            'name' => 'Image Fallback Travel',
            'slug' => 'image-fallback-travel',
            'email' => 'images@example.test',
            'status' => 'active',
        ]);

        $destination = Destination::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'name' => 'Marrakech',
            'slug' => 'marrakech',
            'country' => 'Morocco',
            'description' => 'A destination without attached media.',
            'is_featured' => true,
            'status' => Destination::STATUS_PUBLISHED,
        ]);

        Offer::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'destination_id' => $destination->id,
            'title' => 'Atlas Journey',
            'slug' => 'atlas-journey',
            'description' => 'An offer without attached media.',
            'price' => 990,
            'duration_days' => 5,
            'is_special' => true,
            'status' => Offer::STATUS_PUBLISHED,
        ]);

        Page::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'title' => 'Home',
            'slug' => 'home',
            'status' => Page::STATUS_PUBLISHED,
            'structure' => [
                ['id' => 'destinations', 'type' => 'destination-carousel', 'props' => ['fallbackImage' => '/images/destination-fallback.jpg'], 'children' => []],
                ['id' => 'offers', 'type' => 'special-offers', 'props' => ['fallbackImage' => '/images/offer-fallback.jpg'], 'children' => []],
            ],
        ]);

        $this->get(route('public.site.home', $agency->slug))
            ->assertOk()
            ->assertSee('/images/destination-fallback.jpg', false)
            ->assertSee('/images/offer-fallback.jpg', false);
    }

    public function test_travel_sections_expose_editorial_controls_and_render_inside_tenant_aware_containers(): void
    {
        $this->seed(ComponentSeeder::class);
        $agency = Agency::create([
            'name' => 'Professional Atlas',
            'slug' => 'professional-atlas',
            'email' => 'hello@professional-atlas.test',
            'status' => 'active',
        ]);

        Destination::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'name' => 'Atlas Mountains',
            'slug' => 'atlas-mountains',
            'country' => 'Morocco',
            'description' => 'Mountain landscapes and village trails.',
            'status' => Destination::STATUS_PUBLISHED,
        ]);

        $component = Component::query()->where('type', 'destination-grid')->firstOrFail();
        $this->assertSame('EXPLORE THE WORLD', data_get($component->schema_json, 'tabs.content.fields.eyebrow.default'));
        $this->assertSame('yes', data_get($component->schema_json, 'tabs.content.fields.showViewAll.default'));
        $this->assertSame(['default', 'surface', 'dark'], data_get($component->schema_json, 'tabs.style.fields.sectionTone.options'));

        Page::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'title' => 'Professional home',
            'slug' => 'home',
            'status' => Page::STATUS_PUBLISHED,
            'structure' => [[
                'id' => 'professional-destinations',
                'type' => 'destination-grid',
                'props' => [
                    'eyebrow' => 'CURATED FOR YOU',
                    'title' => 'Places with a story',
                    'intro' => 'A focused collection of memorable places.',
                    'sectionTone' => 'dark',
                    'showViewAll' => 'yes',
                    'viewAllLabel' => 'Explore every destination',
                ],
                'children' => [],
            ]],
        ]);

        $this->get(route('public.site.home', $agency->slug))
            ->assertOk()
            ->assertSee('site-section--dark', false)
            ->assertSee('site-container', false)
            ->assertSee('CURATED FOR YOU')
            ->assertSee('A focused collection of memorable places.')
            ->assertSee('Explore every destination')
            ->assertSee(route('public.site.destinations.index', $agency->slug), false);
    }
}
