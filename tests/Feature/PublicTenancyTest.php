<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Destination;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Offer;
use App\Models\Page;
use App\Services\PublicContentCache;
use App\Support\AgencyContext;
use Database\Seeders\PublicTenancyDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PublicTenancyTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        AgencyContext::clear();
        parent::tearDown();
    }

    public function test_home_and_pages_are_resolved_inside_the_active_agency(): void
    {
        [$atlas, $ocean] = [$this->agency('Atlas'), $this->agency('Ocean')];
        $atlasHome = $this->page($atlas, 'home', 'Atlas home');
        $oceanHome = $this->page($ocean, 'home', 'Ocean home');

        $this->get(route('public.site.home', $atlas->slug))
            ->assertOk()->assertSee('Atlas home')->assertDontSee('Ocean home');
        $this->get(route('public.site.pages.show', [$ocean->slug, 'home']))
            ->assertOk()->assertSee('Ocean home')->assertDontSee('Atlas home');

        $atlasHome->update(['status' => Page::STATUS_DRAFT]);
        $this->get(route('public.site.pages.show', [$atlas->slug, 'home']))->assertNotFound();
        $this->assertNotSame($atlasHome->agency_id, $oceanHome->agency_id);
    }

    public function test_home_falls_back_to_first_published_page_and_inactive_agency_is_hidden(): void
    {
        $agency = $this->agency('Fallback');
        $this->page($agency, 'draft-home', 'Draft', Page::STATUS_DRAFT);
        $this->page($agency, 'about', 'Published fallback');

        $this->get(route('public.site.home', $agency->slug))
            ->assertOk()->assertSee('Published fallback');

        $agency->update(['status' => 'inactive']);
        $this->get(route('public.site.home', $agency->slug))->assertNotFound();
        $this->get(route('public.site.home', 'unknown-agency'))->assertNotFound();
    }

    public function test_destinations_and_offers_never_cross_agency_boundaries(): void
    {
        [$atlas, $ocean] = [$this->agency('Atlas'), $this->agency('Ocean')];
        $atlasDestination = $this->destination($atlas, 'Atlas Marrakech');
        $oceanDestination = $this->destination($ocean, 'Ocean Marrakech');
        $atlasOffer = $this->offer($atlas, $atlasDestination, 'Atlas summer');
        $oceanOffer = $this->offer($ocean, $oceanDestination, 'Ocean summer');

        $this->get(route('public.site.destinations.index', $atlas->slug))
            ->assertOk()->assertSee('Atlas Marrakech')->assertDontSee('Ocean Marrakech');
        $this->get(route('public.site.destinations.show', [$ocean->slug, 'marrakech']))
            ->assertOk()->assertSee('Ocean Marrakech')->assertDontSee('Atlas Marrakech');
        $this->get(route('public.site.offers.index', $atlas->slug))
            ->assertOk()->assertSee('Atlas summer')->assertDontSee('Ocean summer');
        $this->get(route('public.site.offers.show', [$ocean->slug, 'summer-offer']))
            ->assertOk()->assertSee('Ocean summer')->assertDontSee('Atlas summer');

        $atlasOffer->update(['status' => Offer::STATUS_DRAFT]);
        $this->get(route('public.site.offers.show', [$atlas->slug, 'summer-offer']))->assertNotFound();
        $this->assertNotSame($atlasDestination->agency_id, $oceanDestination->agency_id);
    }

    public function test_menu_links_keep_the_agency_slug(): void
    {
        $agency = $this->agency('Atlas');
        $home = $this->page($agency, 'home', 'Atlas home');
        $home->update(['structure' => [[
            'type' => 'button',
            'props' => ['text' => 'Offers CTA', 'url' => '/offers'],
            'children' => [],
        ]]]);
        $menu = Menu::withoutGlobalScopes()->create([
            'agency_id' => $agency->id, 'name' => 'Atlas Menu', 'slug' => 'main', 'is_default' => true,
        ]);
        MenuItem::create(['menu_id' => $menu->id, 'page_id' => $home->id, 'title' => 'Home', 'order' => 1]);
        MenuItem::create(['menu_id' => $menu->id, 'title' => 'Offers', 'url' => '/offers', 'order' => 2]);

        $response = $this->get(route('public.site.home', $agency->slug));
        $response->assertOk();
        $response->assertSee(route('public.site.pages.show', [$agency->slug, 'home']), false);
        $response->assertSee(route('public.site.offers.index', $agency->slug), false);
    }

    public function test_public_layout_renders_tenant_aware_navigation_and_agency_footer(): void
    {
        $agency = $this->agency('Signature Atlas');
        $agency->update([
            'phone' => '+212 500 000 000',
            'address' => 'Marrakech, Morocco',
            'settings' => [
                'public_site' => ['tagline' => 'Private journeys across Morocco.'],
                'social_links' => ['instagram' => 'https://example.com/signature-atlas'],
            ],
        ]);
        $home = $this->page($agency, 'home', 'Signature home');
        $menu = Menu::withoutGlobalScopes()->create([
            'agency_id' => $agency->id, 'name' => 'Main Menu', 'slug' => 'main', 'is_default' => true,
        ]);
        MenuItem::create(['menu_id' => $menu->id, 'page_id' => $home->id, 'title' => 'Home', 'order' => 1]);
        MenuItem::create(['menu_id' => $menu->id, 'title' => 'Offers', 'url' => '/offers', 'order' => 2]);

        $response = $this->get(route('public.site.home', $agency->slug));

        $response->assertOk()
            ->assertSee('data-site-navigation', false)
            ->assertSee('data-site-nav-toggle', false)
            ->assertSee('Signature Atlas')
            ->assertSee('Private journeys across Morocco.')
            ->assertSee('+212 500 000 000')
            ->assertSee('Marrakech, Morocco')
            ->assertSee(route('public.site.offers.index', $agency->slug), false)
            ->assertSee(route('public.site.pages.show', [$agency->slug, 'privacy-policy']), false);
    }

    public function test_public_caches_are_tenant_aware_and_invalidated_after_update(): void
    {
        [$atlas, $ocean] = [$this->agency('Atlas'), $this->agency('Ocean')];
        $atlasPage = $this->page($atlas, 'home', 'Atlas cached');
        $this->page($ocean, 'home', 'Ocean cached');

        $this->get(route('public.site.pages.show', [$atlas->slug, 'home']))->assertOk();
        $this->get(route('public.site.pages.show', [$ocean->slug, 'home']))->assertOk();

        $atlasKey = PublicContentCache::pageKey($atlas->id, 'home');
        $oceanKey = PublicContentCache::pageKey($ocean->id, 'home');
        $this->assertNotSame($atlasKey, $oceanKey);
        $this->assertTrue(Cache::has($atlasKey));
        $this->assertTrue(Cache::has($oceanKey));

        $atlasPage->update(['title' => 'Atlas updated']);
        $this->assertFalse(Cache::has($atlasKey));
        $this->assertTrue(Cache::has($oceanKey));
    }

    public function test_legacy_urls_redirect_only_when_a_published_slug_is_unambiguous(): void
    {
        [$atlas, $ocean] = [$this->agency('Atlas'), $this->agency('Ocean')];
        $this->page($atlas, 'unique', 'Unique page');

        $this->get(route('pages.show', 'unique'))
            ->assertRedirect(route('public.site.pages.show', [$atlas->slug, 'unique']));

        $this->page($atlas, 'home', 'Atlas home');
        $this->page($ocean, 'home', 'Ocean home');
        $this->get(route('pages.show', 'home'))->assertNotFound();

        $this->destination($atlas, 'Atlas Marrakech');
        $this->destination($ocean, 'Ocean Marrakech');
        $this->get(route('public.destinations.show', 'marrakech'))->assertNotFound();

        $this->offer($atlas, null, 'Draft offer', Offer::STATUS_DRAFT, 'draft-offer');
        $this->get(route('public.offers.show', 'draft-offer'))->assertNotFound();
    }

    public function test_page_tenant_indexes_can_be_rolled_back_and_restored(): void
    {
        $this->assertTrue(Schema::hasIndex('pages', ['agency_id', 'slug'], 'unique'));
        $this->assertTrue(Schema::hasIndex('pages', ['agency_id', 'status']));

        $migration = require database_path('migrations/2026_08_12_000001_add_public_tenancy_indexes_to_pages_table.php');
        $migration->down();
        $this->assertFalse(Schema::hasIndex('pages', ['agency_id', 'slug'], 'unique'));
        $this->assertFalse(Schema::hasIndex('pages', ['agency_id', 'status']));
        $migration->up();
        $this->assertTrue(Schema::hasIndex('pages', ['agency_id', 'slug'], 'unique'));
    }

    public function test_demo_seeder_creates_two_distinct_idempotent_public_sites(): void
    {
        $this->seed(PublicTenancyDemoSeeder::class);
        $this->seed(PublicTenancyDemoSeeder::class);

        $this->assertSame(2, Agency::query()
            ->whereIn('slug', ['agence-atlas', 'agence-ocean'])
            ->count());

        $this->get('/sites/agence-atlas')
            ->assertOk()
            ->assertSee('Atlas: aventures au Maroc')
            ->assertSee('Accueil Atlas');
        $this->get('/sites/agence-ocean')
            ->assertOk()
            ->assertSee('Ocean: escapades en bord de mer')
            ->assertSee('Accueil Ocean');
        $this->get('/sites/agence-atlas/destinations/marrakech')
            ->assertOk()->assertSee('Marrakech Atlas')->assertDontSee('Marrakech Ocean');
        $this->get('/sites/agence-ocean/offers/summer-offer')
            ->assertOk()->assertSee('Summer Offer Ocean')->assertDontSee('Summer Offer Atlas');
    }

    private function agency(string $name): Agency
    {
        return Agency::create([
            'name' => $name, 'slug' => strtolower($name).'-'.uniqid(),
            'email' => fake()->unique()->safeEmail(), 'status' => 'active',
        ]);
    }

    private function page(Agency $agency, string $slug, string $title, string $status = Page::STATUS_PUBLISHED): Page
    {
        return Page::withoutGlobalScopes()->create([
            'agency_id' => $agency->id, 'slug' => $slug, 'title' => $title,
            'status' => $status, 'structure' => [['type' => 'text', 'props' => ['content' => $title], 'children' => []]],
        ]);
    }

    private function destination(Agency $agency, string $name): Destination
    {
        return Destination::withoutGlobalScopes()->create([
            'agency_id' => $agency->id, 'name' => $name, 'slug' => 'marrakech',
            'country' => 'Morocco', 'description' => $name, 'status' => Destination::STATUS_PUBLISHED,
        ]);
    }

    private function offer(Agency $agency, ?Destination $destination, string $title, string $status = Offer::STATUS_PUBLISHED, string $slug = 'summer-offer'): Offer
    {
        return Offer::withoutGlobalScopes()->create([
            'agency_id' => $agency->id, 'destination_id' => $destination?->id,
            'title' => $title, 'slug' => $slug, 'description' => $title,
            'price' => 100, 'duration_days' => 3, 'status' => $status,
        ]);
    }
}
