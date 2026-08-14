<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Destination;
use App\Models\MediaAsset;
use App\Models\Offer;
use App\Models\Role;
use App\Models\User;
use App\Support\AgencyContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TravelContentModulesTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        AgencyContext::clear();

        parent::tearDown();
    }

    public function test_destination_crud_uses_current_agency(): void
    {
        $agency = $this->agency('Agency A');
        $user = $this->adminUser($agency);

        $this->actingAs($user)
            ->post(route('destinations.store'), [
                'name' => 'Marrakech',
                'country' => 'Morocco',
                'description' => 'Red city escape.',
                'status' => Destination::STATUS_PUBLISHED,
                'is_featured' => '1',
            ])
            ->assertRedirect(route('destinations.index'));

        $this->assertDatabaseHas('destinations', [
            'agency_id' => $agency->id,
            'slug' => 'marrakech',
            'status' => Destination::STATUS_PUBLISHED,
            'is_featured' => true,
        ]);
    }

    public function test_destination_index_is_tenant_scoped(): void
    {
        $agencyA = $this->agency('Agency A');
        $agencyB = $this->agency('Agency B');
        $user = $this->adminUser($agencyA);

        Destination::create(['agency_id' => $agencyA->id, 'name' => 'Visible', 'country' => 'Morocco']);
        Destination::create(['agency_id' => $agencyB->id, 'name' => 'Hidden', 'country' => 'Spain']);

        $response = $this->actingAs($user)->get(route('destinations.index'));

        $response->assertOk();
        $response->assertSee('Visible');
        $response->assertDontSee('Hidden');
    }

    public function test_offer_rejects_destination_from_another_agency(): void
    {
        $agencyA = $this->agency('Agency A');
        $agencyB = $this->agency('Agency B');
        $user = $this->adminUser($agencyA);
        $foreignDestination = Destination::create([
            'agency_id' => $agencyB->id,
            'name' => 'Foreign',
            'country' => 'Spain',
        ]);

        $this->actingAs($user)
            ->post(route('offers.store'), [
                'destination_id' => $foreignDestination->id,
                'title' => 'Invalid Offer',
                'description' => 'Should fail.',
                'price' => 500,
                'duration_days' => 3,
                'status' => Offer::STATUS_DRAFT,
            ])
            ->assertSessionHasErrors('destination_id');
    }

    public function test_media_upload_and_used_delete_rule(): void
    {
        Storage::fake('public');

        $agency = $this->agency('Agency A');
        $user = $this->adminUser($agency);

        $this->actingAs($user)
            ->post(route('media.store'), [
                'file' => $this->fakePng('hero.png'),
                'title' => 'Hero',
                'alt_text' => 'Hero image',
            ])
            ->assertRedirect(route('media.index'));

        $media = MediaAsset::withoutGlobalScopes()->firstOrFail();

        Storage::disk('public')->assertExists($media->path);

        $destination = Destination::create([
            'agency_id' => $agency->id,
            'name' => 'Linked',
            'country' => 'Morocco',
        ]);
        $destination->media()->attach($media->id);

        $this->actingAs($user)
            ->delete(route('media.destroy', $media))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('media_assets', ['id' => $media->id]);
    }

    public function test_public_offer_page_shows_published_offer(): void
    {
        $agency = $this->agency('Agency A');
        $destination = Destination::create([
            'agency_id' => $agency->id,
            'name' => 'Essaouira',
            'country' => 'Morocco',
            'status' => Destination::STATUS_PUBLISHED,
        ]);
        $offer = Offer::create([
            'agency_id' => $agency->id,
            'destination_id' => $destination->id,
            'title' => 'Weekend Escape',
            'description' => 'A breezy coastal weekend.',
            'price' => 250,
            'duration_days' => 2,
            'status' => Offer::STATUS_PUBLISHED,
        ]);

        $this->get(route('public.site.offers.show', [$agency->slug, $offer->slug]))
            ->assertOk()
            ->assertSee('Weekend Escape')
            ->assertSee('Essaouira');
    }

    private function agency(string $name): Agency
    {
        $name = $name.' '.uniqid();

        return Agency::create([
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
            'email' => fake()->unique()->safeEmail(),
        ]);
    }

    private function adminUser(Agency $agency): User
    {
        $role = Role::create([
            'agency_id' => $agency->id,
            'name' => 'Admin',
            'slug' => 'admin',
        ]);

        return User::create([
            'agency_id' => $agency->id,
            'role_id' => $role->id,
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
        ]);
    }

    private function fakePng(string $name): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'media-test-');
        file_put_contents($path, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII='));

        return new UploadedFile($path, $name, 'image/png', null, true);
    }
}
