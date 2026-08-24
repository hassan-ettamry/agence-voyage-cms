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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class DemoTravelContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        AgencyContext::clear();
    }

    protected function tearDown(): void
    {
        AgencyContext::clear();

        parent::tearDown();
    }

    public function test_admin_can_preview_and_apply_demo_content(): void
    {
        Storage::fake('public');

        $agency = $this->agency('Demo Agency');
        $user = $this->adminUser($agency);

        $this->actingAs($user)
            ->get(route('demo-content.index'))
            ->assertOk()
            ->assertSee('Marrakech Medina');

        $this->actingAs($user)
            ->post(route('demo-content.apply'))
            ->assertRedirect(route('demo-content.index'))
            ->assertSessionHas('success');

        $this->assertSame(6, Destination::withoutGlobalScopes()->where('agency_id', $agency->id)->count());
        $this->assertSame(6, Offer::withoutGlobalScopes()->where('agency_id', $agency->id)->count());
        $this->assertSame(6, MediaAsset::withoutGlobalScopes()->where('agency_id', $agency->id)->count());
        $this->assertSame(6, Destination::withoutGlobalScopes()->where('agency_id', $agency->id)->published()->count());
        $this->assertSame(3, Destination::withoutGlobalScopes()->where('agency_id', $agency->id)->published()->featured()->count());
        $this->assertSame(6, Offer::withoutGlobalScopes()->where('agency_id', $agency->id)->published()->count());
        $this->assertSame(3, Offer::withoutGlobalScopes()->where('agency_id', $agency->id)->published()->special()->count());

        $media = MediaAsset::withoutGlobalScopes()
            ->where('agency_id', $agency->id)
            ->where('filename', 'marrakech-medina.svg')
            ->firstOrFail();

        Storage::disk('public')->assertExists($media->path);
    }

    public function test_applying_demo_content_is_idempotent(): void
    {
        Storage::fake('public');

        $agency = $this->agency('Repeat Agency');
        $user = $this->adminUser($agency);

        $this->actingAs($user)->post(route('demo-content.apply'))->assertRedirect();
        $this->actingAs($user)->post(route('demo-content.apply'))->assertRedirect();

        $this->assertSame(6, Destination::withoutGlobalScopes()->where('agency_id', $agency->id)->count());
        $this->assertSame(6, Offer::withoutGlobalScopes()->where('agency_id', $agency->id)->count());
        $this->assertSame(6, MediaAsset::withoutGlobalScopes()->where('agency_id', $agency->id)->count());
    }

    public function test_existing_destination_slug_is_not_overwritten(): void
    {
        Storage::fake('public');

        $agency = $this->agency('Existing Agency');
        $user = $this->adminUser($agency);
        $destination = Destination::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'name' => 'Custom Marrakech',
            'slug' => 'marrakech-medina',
            'country' => 'Custom Country',
            'description' => 'Keep my content.',
            'status' => Destination::STATUS_DRAFT,
        ]);

        $this->actingAs($user)
            ->post(route('demo-content.apply'))
            ->assertRedirect();

        $destination->refresh();

        $this->assertSame('Custom Marrakech', $destination->name);
        $this->assertSame(Destination::STATUS_DRAFT, $destination->status);
        $this->assertSame(6, Destination::withoutGlobalScopes()->where('agency_id', $agency->id)->count());

        $offer = Offer::withoutGlobalScopes()
            ->where('agency_id', $agency->id)
            ->where('slug', 'marrakech-weekend-escape')
            ->firstOrFail();

        $this->assertSame($destination->id, $offer->destination_id);
    }

    public function test_non_admin_cannot_apply_demo_content(): void
    {
        Storage::fake('public');

        $agency = $this->agency('Editor Agency');
        $user = $this->userWithRole($agency, 'Editor', 'editor');

        $this->actingAs($user)
            ->post(route('demo-content.apply'))
            ->assertForbidden();

        $this->assertSame(0, Destination::withoutGlobalScopes()->where('agency_id', $agency->id)->count());
    }

    public function test_demo_content_is_tenant_scoped(): void
    {
        Storage::fake('public');

        $agencyA = $this->agency('Tenant A');
        $agencyB = $this->agency('Tenant B');
        $user = $this->adminUser($agencyA);

        $this->actingAs($user)
            ->post(route('demo-content.apply'))
            ->assertRedirect();

        $this->assertSame(6, Destination::withoutGlobalScopes()->where('agency_id', $agencyA->id)->count());
        $this->assertSame(0, Destination::withoutGlobalScopes()->where('agency_id', $agencyB->id)->count());
        $this->assertSame(6, Offer::withoutGlobalScopes()->where('agency_id', $agencyA->id)->count());
        $this->assertSame(0, Offer::withoutGlobalScopes()->where('agency_id', $agencyB->id)->count());
    }

    public function test_dynamic_destination_component_renders_demo_content(): void
    {
        Storage::fake('public');

        $agency = $this->agency('Render Agency');
        $user = $this->adminUser($agency);

        $this->actingAs($user)->post(route('demo-content.apply'))->assertRedirect();

        $this->actingAs($user)
            ->post('/builder/render', [
                'mode' => 'editor',
                'structure' => [
                    $this->node('featured-destinations', [
                        'title' => 'Featured trips',
                        'limit' => 3,
                    ]),
                ],
            ])
            ->assertOk()
            ->assertSee('Marrakech Medina')
            ->assertSee('Santorini Coast');
    }

    public function test_dashboard_demo_cta_disappears_after_apply(): void
    {
        Storage::fake('public');

        $agency = $this->agency('CTA Agency');
        $user = $this->adminUser($agency);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Add demonstration content');

        $this->actingAs($user)
            ->post(route('demo-content.apply'))
            ->assertRedirect();

        Cache::flush();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Add demonstration content');
    }

    private function agency(string $name): Agency
    {
        return Agency::create([
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(6),
            'email' => Str::slug($name).'-'.Str::random(6).'@example.com',
            'onboarding_status' => Agency::ONBOARDING_COMPLETED,
            'onboarding_completed_at' => now(),
        ]);
    }

    private function adminUser(Agency $agency): User
    {
        return $this->userWithRole($agency, 'Admin', 'admin');
    }

    private function userWithRole(Agency $agency, string $name, string $slug): User
    {
        $role = Role::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'name' => $name,
            'slug' => $slug,
        ]);

        return tap(User::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'role_id' => $role->id,
            'name' => $name.' User',
            'email' => Str::random(8).'@example.com',
            'password' => 'password',
        ]), fn (User $user) => $user->markEmailAsVerified());
    }

    private function node(string $type, array $props = [], array $children = []): array
    {
        return [
            'id' => (string) Str::uuid(),
            'type' => $type,
            'props' => $props,
            'children' => $children,
        ];
    }
}
