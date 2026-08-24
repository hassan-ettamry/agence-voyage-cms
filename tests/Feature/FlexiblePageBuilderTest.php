<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\BuilderSavedBlock;
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
