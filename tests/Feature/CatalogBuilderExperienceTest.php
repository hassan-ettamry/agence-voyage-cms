<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Component;
use App\Models\Destination;
use App\Models\Page;
use App\Models\Role;
use App\Models\User;
use App\Services\PageService;
use App\Support\AgencyContext;
use Database\Seeders\ComponentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogBuilderExperienceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        AgencyContext::clear();
        parent::tearDown();
    }

    public function test_catalog_schema_exposes_conditional_builder_controls(): void
    {
        $this->seed(ComponentSeeder::class);

        $destinations = Component::query()->where('type', 'destination-grid')->firstOrFail()->schema_json;
        $offers = Component::query()->where('type', 'offer-grid')->firstOrFail()->schema_json;

        $this->assertSame('no', data_get($destinations, 'tabs.data.fields.catalogMode.default'));
        $this->assertSame(['grid', 'list'], data_get($destinations, 'tabs.data.fields.defaultView.options'));
        $this->assertSame(['6', '9', '12'], data_get($destinations, 'tabs.data.fields.itemsPerPage.options'));
        $this->assertSame('yes', data_get($destinations, 'tabs.data.fields.showFeaturedFilter.default'));
        $this->assertSame('yes', data_get($offers, 'tabs.data.fields.showPriceFilter.default'));
        $this->assertSame('yes', data_get($offers, 'tabs.data.fields.showDurationFilter.default'));
        $this->assertSame('yes', data_get($offers, 'tabs.data.fields.showSpecialFilter.default'));
        $this->assertSame('yes', data_get($offers, 'tabs.data.fields.destination_id.when.isNot'));
    }

    public function test_design_system_defines_three_two_one_catalog_grids_and_horizontal_list_cards(): void
    {
        $css = file_get_contents(resource_path('css/components/site-design-system.css'));

        $this->assertMatchesRegularExpression('/\.site-card-grid\s*\{[^}]*display:\s*grid;[^}]*repeat\(var\(--site-card-columns, 3\)/s', $css);
        $this->assertMatchesRegularExpression('/@media \(min-width: 768px\) and \(max-width: 1023px\)[^{]*\{.*?\.site-card-grid\s*\{[^}]*repeat\(2,/s', $css);
        $this->assertMatchesRegularExpression('/@media \(max-width: 767px\)[^{]*\{.*?\.site-card-grid\s*\{[^}]*grid-template-columns:\s*1fr;/s', $css);
        $this->assertMatchesRegularExpression('/\.site-catalog-list \.site-catalog-card\s*\{[^}]*minmax\(220px, 34%\)/s', $css);
    }

    public function test_reserved_catalog_page_requires_exactly_one_catalog_block_before_publish(): void
    {
        $this->seed(ComponentSeeder::class);
        $agency = $this->agency('Guarded Catalog');
        $user = $this->admin($agency);
        $page = $this->page($agency, 'Destinations', 'destinations', []);

        $this->actingAs($user)->postJson(route('pages.publish', $page))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('structure')
            ->assertJsonFragment(['The Destinations catalog must contain exactly one Destination Grid with Catalog Page Mode enabled before it can be published.']);

        $structure = [$this->catalogNode('destination-grid', 'Destination catalog from Builder')];
        $this->actingAs($user)->putJson(route('pages.update', $page), [
            'title' => 'Destinations',
            'slug' => 'destinations',
            'structure' => $structure,
        ])->assertOk();

        $this->actingAs($user)->postJson(route('pages.publish', $page->fresh()))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->get(route('public.site.destinations.index', $agency->slug))
            ->assertOk()
            ->assertSee('Destination catalog from Builder')
            ->assertDontSee('Places that move you');
    }

    public function test_draft_catalog_page_uses_the_public_fallback_and_legacy_urls_redirect_canonically(): void
    {
        $this->seed(ComponentSeeder::class);
        $agency = $this->agency('Fallback Catalog');
        $page = $this->page($agency, 'Destinations', 'destinations', [
            $this->catalogNode('destination-grid', 'Draft-only builder catalog'),
        ]);

        $canonical = route('public.site.destinations.index', $agency->slug);
        $this->get($canonical)
            ->assertOk()
            ->assertSee('Places that move you')
            ->assertDontSee('Draft-only builder catalog');

        $page->update(['status' => Page::STATUS_PUBLISHED, 'published_at' => now()]);
        $this->get(route('public.site.pages.show', [$agency->slug, 'destinations']))
            ->assertRedirect($canonical)
            ->assertStatus(301);
        $this->get(route('pages.show', 'destinations'))
            ->assertRedirect($canonical)
            ->assertStatus(301);
    }

    public function test_public_catalog_validates_get_parameters_and_preserves_filters_during_pagination(): void
    {
        $agency = $this->agency('Paged Catalog');

        foreach (range(1, 7) as $position) {
            Destination::withoutGlobalScopes()->create([
                'agency_id' => $agency->id,
                'name' => sprintf('Destination %02d', $position),
                'slug' => sprintf('destination-%02d', $position),
                'country' => 'Morocco',
                'description' => 'A published destination',
                'status' => Destination::STATUS_PUBLISHED,
            ]);
        }

        $base = route('public.site.destinations.index', $agency->slug);
        $this->get($base.'?country=Morocco&sort=name_asc&view=list&per_page=6')
            ->assertOk()
            ->assertSee('site-catalog-list', false)
            ->assertSee('aria-label="Grid view"', false)
            ->assertSee('aria-label="List view"', false)
            ->assertDontSee('>Grid</a>', false)
            ->assertDontSee('>List</a>', false)
            ->assertSee('Destination 01')
            ->assertDontSee('Destination 07')
            ->assertSee('country=Morocco', false)
            ->assertSee('view=list', false)
            ->assertSee('per_page=6', false);

        $this->get($base.'?country=Morocco&sort=name_asc&view=list&per_page=6&page=2')
            ->assertOk()
            ->assertSee('Destination 07')
            ->assertDontSee('Destination 01');

        $this->getJson($base.'?view=cards&sort=unknown&per_page=100')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['view', 'sort', 'per_page']);
    }

    public function test_page_service_creates_a_version_before_replacing_a_catalog_structure(): void
    {
        $this->seed(ComponentSeeder::class);
        $agency = $this->agency('Versioned Catalog');
        $user = $this->admin($agency);
        $page = $this->page($agency, 'Destinations', 'destinations', [[
            'id' => 'old-heading',
            'type' => 'heading',
            'props' => ['text' => 'Old catalog'],
            'children' => [],
        ]]);

        app(PageService::class)->update($page, [
            'structure' => [$this->catalogNode('destination-grid', 'New catalog')],
        ], $user);

        $this->assertCount(1, $page->versions);
        $this->assertSame('Old catalog', data_get($page->versions->first()->structure, '0.props.text'));
    }

    private function agency(string $name): Agency
    {
        return Agency::withoutGlobalScopes()->create([
            'name' => $name,
            'slug' => str($name)->slug()->append('-', str()->random(6))->toString(),
            'email' => str()->random(8).'@agency.test',
            'status' => 'active',
        ]);
    }

    private function admin(Agency $agency): User
    {
        $role = Role::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'name' => 'Admin',
            'slug' => 'admin',
        ]);

        return tap(User::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'role_id' => $role->id,
            'name' => 'Catalog Admin',
            'email' => str()->random(8).'@user.test',
            'password' => 'StrongPass1!',
        ]), fn (User $user) => $user->markEmailAsVerified());
    }

    private function page(Agency $agency, string $title, string $slug, array $structure): Page
    {
        return Page::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'title' => $title,
            'slug' => $slug,
            'status' => Page::STATUS_DRAFT,
            'structure' => $structure,
        ]);
    }

    private function catalogNode(string $type, string $title): array
    {
        return [
            'id' => str()->uuid()->toString(),
            'type' => $type,
            'props' => [
                'catalogMode' => 'yes',
                'title' => $title,
                'defaultView' => 'grid',
                'defaultSort' => $type === 'destination-grid' ? 'featured' : 'special',
                'itemsPerPage' => '9',
            ],
            'children' => [],
        ];
    }
}
