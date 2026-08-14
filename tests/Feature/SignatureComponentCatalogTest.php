<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Component;
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
}
