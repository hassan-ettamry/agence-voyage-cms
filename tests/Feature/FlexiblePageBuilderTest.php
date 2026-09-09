<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\BuilderSavedBlock;
use App\Models\Component;
use App\Models\Destination;
use App\Models\MediaAsset;
use App\Models\Offer;
use App\Models\Page;
use App\Models\Role;
use App\Models\User;
use App\Services\Renderer\PageRenderer;
use App\Support\AgencyContext;
use Database\Seeders\ComponentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FlexiblePageBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        AgencyContext::clear();
        parent::tearDown();
    }

    public function test_builder_saves_title_slug_and_structure_and_validates_slug(): void
    {
        $agency = $this->agency('Builder Save');
        $user = $this->admin($agency);
        $page = $this->page($agency, 'Old title', 'old-title');
        $structure = [[
            'id' => 'text-one',
            'type' => 'text',
            'props' => ['text' => 'Saved content'],
            'children' => [],
        ]];

        $this->actingAs($user)->putJson(route('pages.update', $page), [
            'title' => 'New builder title',
            'slug' => 'new-builder-title',
            'structure' => $structure,
        ])->assertOk()
            ->assertJsonPath('page.title', 'New builder title')
            ->assertJsonPath('page.slug', 'new-builder-title');

        $page->refresh();
        $this->assertSame('New builder title', $page->title);
        $this->assertSame('new-builder-title', $page->slug);
        $this->assertSame($structure, $page->structure);

        $this->actingAs($user)->putJson(route('pages.update', $page), [
            'title' => 'Invalid slug',
            'slug' => 'Invalid slug with spaces',
            'structure' => $structure,
        ])->assertUnprocessable()->assertJsonValidationErrors('slug');
    }

    public function test_preview_and_publish_use_the_last_successful_manual_save(): void
    {
        $agency = $this->agency('Save Flow');
        $user = $this->admin($agency);
        $page = $this->page($agency, 'Draft', 'draft-page');
        $this->seed(ComponentSeeder::class);

        $this->actingAs($user)->putJson(route('pages.update', $page), [
            'title' => 'Saved before preview',
            'slug' => 'saved-before-preview',
            'structure' => [[
                'id' => 'saved-heading',
                'type' => 'heading',
                'props' => ['text' => 'Saved before opening preview'],
                'children' => [],
            ]],
        ])->assertOk();

        $this->actingAs($user)->get(route('pages.preview', $page->fresh()))
            ->assertOk()
            ->assertSee('Saved before opening preview');

        $this->actingAs($user)->postJson(route('pages.publish', $page->fresh()))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSame(Page::STATUS_PUBLISHED, $page->fresh()->status);
    }

    public function test_builder_travel_selectors_are_published_and_tenant_aware(): void
    {
        $this->seed(ComponentSeeder::class);
        $agency = $this->agency('Atlas Selectors');
        $foreignAgency = $this->agency('Ocean Selectors');
        $user = $this->admin($agency);
        $page = $this->page($agency, 'Travel page', 'travel-page');

        $visibleDestination = $this->destination($agency, 'Visible Marrakech', Destination::STATUS_PUBLISHED);
        $this->destination($agency, 'Hidden Draft', Destination::STATUS_DRAFT);
        $this->destination($foreignAgency, 'Foreign Destination', Destination::STATUS_PUBLISHED);
        $this->offer($agency, $visibleDestination, 'Visible Summer Offer', Offer::STATUS_PUBLISHED);
        $this->offer($agency, $visibleDestination, 'Hidden Offer Draft', Offer::STATUS_DRAFT);
        $foreignDestination = $this->destination($foreignAgency, 'Foreign Coast', Destination::STATUS_PUBLISHED);
        $this->offer($foreignAgency, $foreignDestination, 'Foreign Offer', Offer::STATUS_PUBLISHED);

        $this->actingAs($user)->get(route('pages.builder', $page))
            ->assertOk()
            ->assertSee('destination-grid')
            ->assertSee('offer-grid')
            ->assertSee('Visible Marrakech')
            ->assertSee('Visible Summer Offer')
            ->assertDontSee('Hidden Draft')
            ->assertDontSee('Hidden Offer Draft')
            ->assertDontSee('Foreign Destination')
            ->assertDontSee('Foreign Offer');
    }

    public function test_travel_component_schemas_expose_only_relevant_visual_controls(): void
    {
        $this->seed(ComponentSeeder::class);

        $destination = Component::where('type', 'destination-grid')->firstOrFail()->schema_json;
        $offer = Component::where('type', 'offer-grid')->firstOrFail()->schema_json;

        $this->assertSame(
            ['latest', 'featured', 'manual'],
            data_get($destination, 'tabs.data.fields.source.options')
        );
        $this->assertSame('destinations', data_get($destination, 'tabs.data.fields.manual_ids.entity'));
        $this->assertSame('yes', data_get($destination, 'props.showLocation.default'));
        $this->assertSame('yes', data_get($destination, 'props.showTravelTypes.default'));

        $this->assertSame(
            ['latest', 'special', 'manual'],
            data_get($offer, 'tabs.data.fields.source.options')
        );
        $this->assertSame('offers', data_get($offer, 'tabs.data.fields.manual_ids.entity'));
        $this->assertSame('destinations', data_get($offer, 'tabs.data.fields.destination_id.entity'));
        $this->assertSame('yes', data_get($offer, 'props.showDestination.default'));
        $this->assertSame('yes', data_get($offer, 'props.showPrice.default'));
        $this->assertSame('yes', data_get($offer, 'props.showDuration.default'));
    }

    public function test_travel_components_apply_manual_order_filters_visibility_and_tenant_scope(): void
    {
        $this->seed(ComponentSeeder::class);
        $agency = $this->agency('Travel Render Atlas');
        $foreignAgency = $this->agency('Travel Render Ocean');
        AgencyContext::set($agency->id);

        $london = Destination::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'name' => 'Builder London',
            'slug' => 'builder-london',
            'country' => 'United Kingdom',
            'continent' => 'europe',
            'travel_types' => ['city', 'cultural'],
            'ideal_months' => [5, 6],
            'description' => 'London description',
            'is_featured' => true,
            'status' => Destination::STATUS_PUBLISHED,
        ]);
        $tahoe = Destination::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'name' => 'Builder Tahoe',
            'slug' => 'builder-tahoe',
            'country' => 'United States',
            'continent' => 'north-america',
            'travel_types' => ['nature'],
            'ideal_months' => [7],
            'description' => 'Tahoe description',
            'is_featured' => true,
            'status' => Destination::STATUS_PUBLISHED,
        ]);
        $foreign = $this->destination($foreignAgency, 'Foreign Builder Destination', Destination::STATUS_PUBLISHED);

        $special = Offer::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'destination_id' => $london->id,
            'title' => 'Builder London Special',
            'slug' => 'builder-london-special',
            'description' => 'Special description',
            'summary' => 'Special summary',
            'price' => 7500,
            'duration_days' => 4,
            'is_special' => true,
            'status' => Offer::STATUS_PUBLISHED,
        ]);
        $regular = Offer::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'destination_id' => $tahoe->id,
            'title' => 'Builder Tahoe Regular',
            'slug' => 'builder-tahoe-regular',
            'description' => 'Regular description',
            'price' => 6200,
            'duration_days' => 6,
            'is_special' => false,
            'status' => Offer::STATUS_PUBLISHED,
        ]);
        $this->offer($foreignAgency, $foreign, 'Foreign Builder Offer', Offer::STATUS_PUBLISHED);

        $destinationHtml = app(PageRenderer::class)->render([[
            'id' => 'manual-destinations',
            'type' => 'destination-grid',
            'props' => [
                'source' => 'manual',
                'manual_ids' => [$tahoe->id, $london->id, $foreign->id],
                'limit' => 2,
                'showLocation' => 'no',
                'showTravelTypes' => 'no',
            ],
            'children' => [],
        ]], 'editor');

        $this->assertLessThan(strpos($destinationHtml, 'Builder London'), strpos($destinationHtml, 'Builder Tahoe'));
        $this->assertStringNotContainsString('Foreign Builder Destination', $destinationHtml);
        $this->assertStringNotContainsString('United Kingdom', $destinationHtml);
        $this->assertStringNotContainsString('Cultural', $destinationHtml);

        $carouselHtml = app(PageRenderer::class)->render([[
            'id' => 'manual-destination-carousel',
            'type' => 'destination-carousel',
            'props' => [
                'source' => 'manual',
                'manual_ids' => [$london->id, $tahoe->id, $foreign->id],
                'limit' => 2,
                'showLocation' => 'yes',
                'showTravelTypes' => 'no',
                'showCta' => 'no',
            ],
            'children' => [],
        ]], 'editor');

        $this->assertLessThan(strpos($carouselHtml, 'Builder Tahoe'), strpos($carouselHtml, 'Builder London'));
        $this->assertStringContainsString('United Kingdom', $carouselHtml);
        $this->assertStringNotContainsString('Foreign Builder Destination', $carouselHtml);
        $this->assertStringNotContainsString('View destination', $carouselHtml);

        $offerHtml = app(PageRenderer::class)->render([[
            'id' => 'filtered-offers',
            'type' => 'offer-grid',
            'props' => [
                'source' => 'special',
                'destination_id' => $london->id,
                'limit' => 3,
                'showPrice' => 'yes',
                'showDuration' => 'no',
                'showDestination' => 'yes',
                'showCta' => 'no',
                'cardVariant' => 'deal',
            ],
            'children' => [],
        ]], 'editor');

        $this->assertStringContainsString('Builder London Special', $offerHtml);
        $this->assertStringContainsString('7,500.00 MAD', $offerHtml);
        $this->assertStringContainsString('Builder London', $offerHtml);
        $this->assertStringNotContainsString('4 days', $offerHtml);
        $this->assertStringNotContainsString('Builder Tahoe Regular', $offerHtml);
        $this->assertStringNotContainsString('Foreign Builder Offer', $offerHtml);
        $this->assertStringNotContainsString('View offer', $offerHtml);

        $comparisonHtml = app(PageRenderer::class)->render([[
            'id' => 'manual-offer-comparison',
            'type' => 'offer-comparison',
            'props' => [
                'source' => 'manual',
                'manual_ids' => [$regular->id, $special->id],
                'limit' => 2,
                'showPrice' => 'yes',
                'showDuration' => 'no',
                'showDestination' => 'yes',
                'showCta' => 'no',
            ],
            'children' => [],
        ]], 'editor');

        $this->assertLessThan(strpos($comparisonHtml, 'Builder London Special'), strpos($comparisonHtml, 'Builder Tahoe Regular'));
        $this->assertStringContainsString('6,200.00 MAD', $comparisonHtml);
        $this->assertStringContainsString('7,500.00 MAD', $comparisonHtml);
        $this->assertStringNotContainsString('4 days', $comparisonHtml);
        $this->assertStringNotContainsString('6 days', $comparisonHtml);
        $this->assertStringNotContainsString('View offer', $comparisonHtml);

        $cardHtml = app(PageRenderer::class)->render([[
            'id' => 'manual-offer-card',
            'type' => 'offer-card',
            'props' => [
                'offer_id' => $regular->id,
                'showPrice' => 'no',
                'showDuration' => 'yes',
                'showDestination' => 'no',
            ],
            'children' => [],
        ]], 'editor');

        $this->assertStringContainsString('Builder Tahoe Regular', $cardHtml);
        $this->assertStringContainsString('6 days', $cardHtml);
        $this->assertStringNotContainsString('6,200.00 MAD', $cardHtml);
        $this->assertStringNotContainsString('Builder Tahoe</div>', $cardHtml);
    }

    public function test_travel_components_render_clean_empty_states(): void
    {
        $this->seed(ComponentSeeder::class);
        $agency = $this->agency('Empty Travel Atlas');
        AgencyContext::set($agency->id);

        $html = app(PageRenderer::class)->render([[
            'id' => 'empty-destinations',
            'type' => 'destination-grid',
            'props' => ['continent' => 'antarctica'],
            'children' => [],
        ], [
            'id' => 'empty-offers',
            'type' => 'offer-grid',
            'props' => ['continent' => 'antarctica'],
            'children' => [],
        ]], 'editor');

        $this->assertStringContainsString('No published destinations match this source.', $html);
        $this->assertStringContainsString('No published offers match this source.', $html);
        $this->assertStringNotContainsString('Exception', $html);
        $this->assertStringNotContainsString('{&quot;', $html);
    }

    public function test_guided_design_renders_without_breaking_legacy_properties(): void
    {
        $this->seed(ComponentSeeder::class);

        $html = app(PageRenderer::class)->render([[
            'id' => 'guided-heading',
            'type' => 'heading',
            'props' => [
                'text' => 'Guided responsive heading',
                'hideOnTablet' => 'yes',
                'design' => [
                    'desktop' => ['fontSize' => 56, 'textColor' => '#12372f'],
                    'tablet' => ['fontSize' => 42],
                    'mobile' => ['fontSize' => 30, 'alignment' => 'center'],
                ],
            ],
            'children' => [],
        ]], 'preview');

        $this->assertStringContainsString('Guided responsive heading', $html);
        $this->assertStringContainsString('builder-design', $html);
        $this->assertStringContainsString('--bd-desktop-font-size:56px', $html);
        $this->assertStringContainsString('--bd-tablet-font-size:42px', $html);
        $this->assertStringContainsString('--bd-mobile-font-size:30px', $html);
        $this->assertStringContainsString('site-hide-tablet', $html);
    }

    public function test_saved_blocks_crud_is_tenant_isolated_and_page_copies_remain_independent(): void
    {
        $agency = $this->agency('Saved Blocks Atlas');
        $foreignAgency = $this->agency('Saved Blocks Ocean');
        $user = $this->admin($agency);
        $foreignUser = $this->admin($foreignAgency);
        $structure = [$this->section('saved-section', 'saved-text')];

        $create = $this->actingAs($user)->postJson(route('builder-saved-blocks.store'), [
            'name' => 'Hero block',
            'category' => 'Hero',
            'structure' => $structure,
        ])->assertCreated()->assertJsonPath('name', 'Hero block');

        $blockId = $create->json('id');
        $this->assertDatabaseHas('builder_saved_blocks', [
            'id' => $blockId,
            'agency_id' => $agency->id,
            'created_by' => $user->id,
        ]);

        $page = Page::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'title' => 'Independent saved block copy',
            'slug' => 'independent-copy',
            'status' => Page::STATUS_DRAFT,
            'structure' => $structure,
        ]);

        $this->actingAs($user)->patchJson(route('builder-saved-blocks.update', $blockId), [
            'name' => 'Renamed hero block',
            'category' => 'Marketing',
        ])->assertOk()->assertJsonPath('name', 'Renamed hero block');

        $this->actingAs($foreignUser)->getJson(route('builder-saved-blocks.index'))
            ->assertOk()
            ->assertJsonCount(0);
        $this->actingAs($foreignUser)->patchJson(route('builder-saved-blocks.update', $blockId), [
            'name' => 'Forbidden rename',
            'category' => null,
        ])->assertNotFound();

        $this->actingAs($user)->deleteJson(route('builder-saved-blocks.destroy', $blockId))
            ->assertNoContent();

        $this->assertDatabaseMissing('builder_saved_blocks', ['id' => $blockId]);
        $this->assertSame($structure, $page->fresh()->structure);
    }

    public function test_media_picker_returns_only_current_agency_images_even_when_search_matches_foreign_media(): void
    {
        $agency = $this->agency('Media Atlas');
        $foreignAgency = $this->agency('Media Ocean');
        $user = $this->admin($agency);
        $this->media($agency, 'atlas-hero.jpg', 'Shared hero');
        $this->media($foreignAgency, 'foreign-shared-hero.jpg', 'Foreign image');

        $this->actingAs($user)->getJson(route('media.picker', ['search' => 'hero']))
            ->assertOk()
            ->assertJsonFragment(['original_name' => 'atlas-hero.jpg'])
            ->assertJsonMissing(['original_name' => 'foreign-shared-hero.jpg']);
    }

    public function test_saved_blocks_migration_can_run_down_and_up(): void
    {
        $migration = require database_path('migrations/2026_08_24_000003_create_builder_saved_blocks_table.php');

        $this->assertTrue(Schema::hasTable('builder_saved_blocks'));
        $migration->down();
        $this->assertFalse(Schema::hasTable('builder_saved_blocks'));
        $migration->up();
        $this->assertTrue(Schema::hasColumns('builder_saved_blocks', [
            'id', 'agency_id', 'created_by', 'name', 'category', 'structure',
        ]));
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
            'name' => 'Builder Admin',
            'email' => str()->random(8).'@user.test',
            'password' => 'StrongPass1!',
        ]), fn (User $user) => $user->markEmailAsVerified());
    }

    private function page(Agency $agency, string $title, string $slug): Page
    {
        return Page::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'title' => $title,
            'slug' => $slug,
            'status' => Page::STATUS_DRAFT,
            'structure' => [],
        ]);
    }

    private function destination(Agency $agency, string $name, string $status): Destination
    {
        return Destination::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'name' => $name,
            'country' => 'Morocco',
            'description' => $name,
            'status' => $status,
        ]);
    }

    private function offer(Agency $agency, Destination $destination, string $title, string $status): Offer
    {
        return Offer::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'destination_id' => $destination->id,
            'title' => $title,
            'description' => $title,
            'price' => 800,
            'duration_days' => 4,
            'status' => $status,
        ]);
    }

    private function media(Agency $agency, string $originalName, string $title): MediaAsset
    {
        return MediaAsset::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'filename' => $originalName,
            'original_name' => $originalName,
            'path' => "agencies/{$agency->id}/media/{$originalName}",
            'mime_type' => 'image/jpeg',
            'size' => 100,
            'title' => $title,
        ]);
    }

    private function section(string $sectionId, string $textId): array
    {
        return [
            'id' => $sectionId,
            'type' => 'section',
            'props' => [],
            'children' => [[
                'id' => $textId,
                'type' => 'text',
                'props' => ['text' => 'Saved independently'],
                'children' => [],
            ]],
        ];
    }
}
