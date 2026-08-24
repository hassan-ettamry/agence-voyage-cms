<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Destination;
use App\Models\MediaAsset;
use App\Models\Offer;
use App\Models\Page;
use App\Models\Role;
use App\Models\User;
use App\Services\DemoTravelContentService;
use App\Support\AgencyContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RichTravelCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        AgencyContext::clear();

        parent::tearDown();
    }

    public function test_rich_catalog_migration_is_reversible_without_removing_existing_records(): void
    {
        $agency = $this->agency('Migration Agency');
        $destination = Destination::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'name' => 'Existing destination',
            'country' => 'Morocco',
        ]);
        $offer = Offer::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'destination_id' => $destination->id,
            'title' => 'Existing offer',
            'description' => 'Existing content',
            'price' => 100,
            'duration_days' => 2,
        ]);

        $migration = require database_path('migrations/2026_08_24_000002_enrich_travel_catalog.php');
        $migration->down();

        $this->assertFalse(Schema::hasColumn('destinations', 'travel_types'));
        $this->assertFalse(Schema::hasColumn('offers', 'itinerary'));
        $this->assertFalse(Schema::hasColumn('media_assets', 'license'));
        $this->assertDatabaseHas('destinations', ['id' => $destination->id]);
        $this->assertDatabaseHas('offers', ['id' => $offer->id]);

        $migration->up();

        $this->assertTrue(Schema::hasColumns('destinations', [
            'continent', 'region', 'travel_types', 'ideal_months',
            'practical_information', 'latitude', 'longitude',
        ]));
        $this->assertTrue(Schema::hasColumns('offers', [
            'summary', 'itinerary', 'inclusions', 'exclusions', 'practical_information',
        ]));
        $this->assertTrue(Schema::hasColumns('media_assets', [
            'copyright_holder', 'license', 'source_url',
        ]));
        $this->assertDatabaseHas('destinations', ['id' => $destination->id]);
        $this->assertDatabaseHas('offers', ['id' => $offer->id]);
    }

    public function test_admin_can_store_rich_destination_data_and_order_its_gallery(): void
    {
        $agency = $this->agency('Rich Destination');
        $user = $this->user($agency, 'admin');
        [$first, $second] = [
            $this->media($agency, 'first.jpg'),
            $this->media($agency, 'second.jpg'),
        ];

        $this->actingAs($user)->post(route('destinations.store'), [
            'name' => 'Atlas Mountains',
            'country' => 'Morocco',
            'continent' => 'africa',
            'region' => 'High Atlas',
            'description' => 'Mountain villages and guided trails.',
            'travel_types' => ['mountain', 'adventure', 'cultural'],
            'ideal_months' => ['4', '5', '9', '10'],
            'practical_information' => 'Bring layers and walking shoes.',
            'latitude' => '31.1342000',
            'longitude' => '-7.9180000',
            'media_ids' => [$second->id, $first->id],
            'status' => Destination::STATUS_PUBLISHED,
        ])->assertRedirect(route('destinations.index'));

        $destination = Destination::withoutGlobalScopes()->where('slug', 'atlas-mountains')->firstOrFail();

        $this->assertSame(['mountain', 'adventure', 'cultural'], $destination->travel_types);
        $this->assertSame([4, 5, 9, 10], $destination->ideal_months);
        $this->assertSame('africa', $destination->continent);
        $this->assertSame([$second->id, $first->id], $destination->media()->pluck('media_assets.id')->all());
        $this->assertSame([0, 1], $destination->media()->pluck('destination_media.sort_order')->all());
    }

    public function test_admin_can_store_structured_offer_content_with_limits(): void
    {
        $agency = $this->agency('Rich Offer');
        $user = $this->user($agency, 'admin');
        $destination = $this->destination($agency, 'Marrakech', ['cultural'], [4, 5]);

        $this->actingAs($user)->post(route('offers.store'), [
            'destination_id' => $destination->id,
            'title' => 'Marrakech Signature',
            'summary' => 'A compact cultural journey.',
            'description' => 'A complete guided journey through Marrakech.',
            'price' => 890,
            'duration_days' => 3,
            'itinerary' => [
                ['day' => 1, 'title' => 'Arrival', 'description' => 'Private transfer.'],
                ['day' => 2, 'title' => 'Medina', 'description' => 'Guided discovery.'],
            ],
            'inclusions' => ['Riad stay', '', 'Transfers'],
            'exclusions' => ['Flights'],
            'practical_information' => 'Comfortable shoes are recommended.',
            'status' => Offer::STATUS_PUBLISHED,
        ])->assertRedirect(route('offers.index'));

        $offer = Offer::withoutGlobalScopes()->where('slug', 'marrakech-signature')->firstOrFail();

        $this->assertSame('A compact cultural journey.', $offer->summary);
        $this->assertCount(2, $offer->itinerary);
        $this->assertSame(['Riad stay', 'Transfers'], $offer->inclusions);
        $this->assertSame(['Flights'], $offer->exclusions);

        $tooManyDays = collect(range(1, 31))->map(fn ($day) => [
            'day' => $day,
            'title' => "Day {$day}",
            'description' => '',
        ])->all();

        $this->actingAs($user)->post(route('offers.store'), [
            'title' => 'Too long',
            'description' => 'Invalid itinerary.',
            'price' => 100,
            'duration_days' => 31,
            'itinerary' => $tooManyDays,
            'status' => Offer::STATUS_DRAFT,
        ])->assertSessionHasErrors('itinerary');

        $this->actingAs($user)->post(route('offers.store'), [
            'title' => 'Invalid day number',
            'description' => 'The programme itself is limited to 30 days.',
            'price' => 100,
            'duration_days' => 31,
            'itinerary' => [
                ['day' => 31, 'title' => 'Day 31', 'description' => 'Outside the programme limit.'],
            ],
            'status' => Offer::STATUS_DRAFT,
        ])->assertSessionHasErrors('itinerary.0.day');
    }

    public function test_batch_media_upload_is_tenant_scoped_and_keeps_source_metadata(): void
    {
        Storage::fake('public');
        $agency = $this->agency('Batch Media');
        $user = $this->user($agency, 'admin');

        $this->actingAs($user)->post(route('media.store'), [
            'files' => [$this->fakePng('one.png'), $this->fakePng('two.png')],
            'copyright_holder' => 'Atlas Studio',
            'license' => 'Owned',
            'source_url' => 'https://example.test/source',
        ])->assertRedirect(route('media.index'));

        $this->assertDatabaseCount('media_assets', 2);
        $this->assertSame(2, MediaAsset::withoutGlobalScopes()->where('agency_id', $agency->id)->count());
        $this->assertSame(['one', 'two'], MediaAsset::withoutGlobalScopes()->orderBy('title')->pluck('title')->all());
        $this->assertSame(['Owned'], MediaAsset::withoutGlobalScopes()->distinct()->pluck('license')->all());
        $this->assertCount(2, Storage::disk('public')->allFiles());

        $this->actingAs($user)->post(route('media.store'), [
            'files' => [$this->fakePng('valid.png'), UploadedFile::fake()->create('invalid.txt', 1, 'text/plain')],
        ])->assertSessionHasErrors('files.1');

        $this->assertDatabaseCount('media_assets', 2);
        $this->assertCount(2, Storage::disk('public')->allFiles());
    }

    public function test_only_admin_can_change_the_agency_catalog_currency(): void
    {
        $agency = $this->agency('Currency Agency');
        $admin = $this->user($agency, 'admin');
        $editor = $this->user($agency, 'editor');
        $settings = [
            'language' => 'en',
            'timezone' => 'Africa/Casablanca',
            'date_format' => 'Y-m-d',
            'time_format' => '24h',
        ];

        $this->actingAs($admin)
            ->put(route('account.settings.update'), $settings + ['agency_currency' => 'eur'])
            ->assertSessionHasNoErrors();

        $this->assertSame('EUR', $agency->fresh()->catalogCurrency());

        $this->actingAs($editor)
            ->put(route('account.settings.update'), $settings + ['agency_currency' => 'USD'])
            ->assertSessionHasErrors('agency_currency');

        $this->assertSame('EUR', $agency->fresh()->catalogCurrency());
    }

    public function test_public_filters_and_builder_components_use_rich_tenant_catalog_data(): void
    {
        $atlas = $this->agency('Atlas Public');
        $ocean = $this->agency('Ocean Public');
        $matching = $this->destination($atlas, 'Atlas Trek', ['mountain', 'adventure'], [5, 9], 'africa');
        $this->destination($atlas, 'Atlas Beach', ['beach'], [7], 'africa');
        $this->destination($ocean, 'Foreign Trek', ['mountain'], [5], 'africa');
        $this->offer($atlas, $matching, 'Atlas Rich Journey');
        $this->offer($ocean, Destination::withoutGlobalScopes()->where('agency_id', $ocean->id)->firstOrFail(), 'Foreign Journey');

        Page::withoutGlobalScopes()->create([
            'agency_id' => $atlas->id,
            'title' => 'Home',
            'slug' => 'home',
            'status' => Page::STATUS_PUBLISHED,
            'structure' => [[
                'id' => 'filtered-destinations',
                'type' => 'destination-grid',
                'props' => ['travelType' => 'mountain', 'idealMonth' => 5],
                'children' => [],
            ], [
                'id' => 'filtered-offers',
                'type' => 'offer-grid',
                'props' => ['travelType' => 'mountain', 'idealMonth' => 5],
                'children' => [],
            ]],
        ]);

        $this->get(route('public.site.destinations.index', $atlas->slug).'?continent=africa&type=mountain&month=5')
            ->assertOk()
            ->assertSee('Atlas Trek')
            ->assertDontSee('Atlas Beach')
            ->assertDontSee('Foreign Trek');

        $this->get(route('public.site.offers.index', $atlas->slug).'?type=mountain&month=5')
            ->assertOk()
            ->assertSee('Atlas Rich Journey')
            ->assertDontSee('Foreign Journey');

        $this->get(route('public.site.home', $atlas->slug))
            ->assertOk()
            ->assertSee('Atlas Trek')
            ->assertSee('Atlas Rich Journey')
            ->assertDontSee('Atlas Beach')
            ->assertDontSee('Foreign Journey');
    }

    public function test_demo_content_service_creates_rich_catalog_records_without_overwriting_existing_slugs(): void
    {
        Storage::fake('public');
        $agency = $this->agency('Demo Rich');
        $user = $this->user($agency, 'admin');
        $service = app(DemoTravelContentService::class);

        $service->apply($user);

        $destination = Destination::withoutGlobalScopes()
            ->where('agency_id', $agency->id)
            ->where('slug', 'marrakech-medina')
            ->firstOrFail();
        $offer = Offer::withoutGlobalScopes()
            ->where('agency_id', $agency->id)
            ->where('slug', 'marrakech-weekend-escape')
            ->firstOrFail();

        $this->assertSame('africa', $destination->continent);
        $this->assertContains('cultural', $destination->travel_types);
        $this->assertNotEmpty($destination->ideal_months);
        $this->assertNotEmpty($offer->itinerary);
        $this->assertNotEmpty($offer->inclusions);

        $service->apply($user);

        $this->assertSame(1, Destination::withoutGlobalScopes()->where('agency_id', $agency->id)->where('slug', 'marrakech-medina')->count());
        $this->assertSame(1, Offer::withoutGlobalScopes()->where('agency_id', $agency->id)->where('slug', 'marrakech-weekend-escape')->count());
    }

    private function agency(string $name): Agency
    {
        return Agency::create([
            'name' => $name,
            'slug' => str($name)->slug()->append('-', str()->random(6))->toString(),
            'email' => str()->random(8).'@agency.test',
            'status' => 'active',
        ]);
    }

    private function user(Agency $agency, string $roleSlug): User
    {
        $role = Role::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'name' => ucfirst($roleSlug),
            'slug' => $roleSlug,
        ]);

        return tap(User::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'role_id' => $role->id,
            'name' => ucfirst($roleSlug).' User',
            'email' => str()->random(8).'@user.test',
            'password' => 'StrongPass1!',
        ]), fn (User $user) => $user->markEmailAsVerified());
    }

    private function destination(
        Agency $agency,
        string $name,
        array $types,
        array $months,
        string $continent = 'africa'
    ): Destination {
        return Destination::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'name' => $name,
            'country' => 'Morocco',
            'continent' => $continent,
            'description' => $name,
            'travel_types' => $types,
            'ideal_months' => $months,
            'status' => Destination::STATUS_PUBLISHED,
        ]);
    }

    private function offer(Agency $agency, Destination $destination, string $title): Offer
    {
        return Offer::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'destination_id' => $destination->id,
            'title' => $title,
            'description' => $title,
            'summary' => $title.' summary',
            'price' => 950,
            'duration_days' => 5,
            'status' => Offer::STATUS_PUBLISHED,
        ]);
    }

    private function media(Agency $agency, string $filename): MediaAsset
    {
        return MediaAsset::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'filename' => $filename,
            'original_name' => $filename,
            'path' => "agencies/{$agency->id}/media/{$filename}",
            'mime_type' => 'image/jpeg',
            'size' => 100,
            'title' => pathinfo($filename, PATHINFO_FILENAME),
        ]);
    }

    private function fakePng(string $name): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'rich-catalog-');
        file_put_contents($path, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII='));

        return new UploadedFile($path, $name, 'image/png', null, true);
    }
}
