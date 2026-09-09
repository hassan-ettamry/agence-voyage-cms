<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Page;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SiteTemplate;
use App\Models\User;
use App\Services\AuthService;
use App\Support\AgencyContext;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PlatformAuthorizationHardeningTest extends TestCase
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

    public function test_admin_privileges_do_not_bypass_tenant_policies(): void
    {
        $agencyA = $this->agency('Atlas');
        $agencyB = $this->agency('Ocean');
        $admin = $this->admin($agencyA);
        $localPage = $this->page($agencyA, 'local-page');
        $foreignPage = $this->page($agencyB, 'foreign-page');
        $foreignRole = $this->role($agencyB, 'Foreign role');
        $foreignUser = $this->user($agencyB, $foreignRole);

        $this->assertTrue($admin->can('update', $localPage));
        $this->assertFalse($admin->can('update', $foreignPage));
        $this->assertFalse($admin->can('update', $foreignRole));
        $this->assertFalse($admin->can('delete', $foreignUser));
    }

    public function test_self_deletion_and_last_admin_demotion_are_blocked(): void
    {
        $agency = $this->agency('Protected');
        $admin = $this->admin($agency);
        $memberRole = $this->role($agency, 'Member');

        $this->actingAs($admin)
            ->delete(route('users.destroy', $admin))
            ->assertForbidden();

        $this->actingAs($admin)
            ->put(route('users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'password' => '',
                'role_id' => $memberRole->id,
            ])
            ->assertSessionHasErrors('role_id');

        $this->assertSame('admin', $admin->fresh()->role->slug);
    }

    public function test_an_admin_can_be_demoted_only_when_another_admin_remains(): void
    {
        $agency = $this->agency('Two admins');
        $admin = $this->admin($agency);
        $this->user($agency, $admin->role, 'second-admin@example.test');
        $memberRole = $this->role($agency, 'Member');

        $this->actingAs($admin)
            ->put(route('users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'password' => '',
                'role_id' => $memberRole->id,
            ])
            ->assertRedirect();

        $this->assertSame($memberRole->id, $admin->fresh()->role_id);
    }

    public function test_admin_role_slug_and_deletion_are_protected(): void
    {
        $agency = $this->agency('Admin role');
        $admin = $this->admin($agency);
        $adminRole = $admin->role;

        $this->actingAs($admin)
            ->put(route('roles.update', $adminRole), [
                'name' => 'Agency owner',
                'slug' => 'owner',
            ])
            ->assertRedirect();

        $this->assertSame('admin', $adminRole->fresh()->slug);

        $this->actingAs($admin)
            ->delete(route('roles.destroy', $adminRole))
            ->assertForbidden();
    }

    public function test_site_templates_require_explicit_capabilities(): void
    {
        $agency = $this->agency('Templates');
        $memberRole = $this->role($agency, 'Template viewer');
        $memberRole->permissions()->attach([
            $this->permission('page.view')->id,
            $this->permission('theme.view')->id,
        ]);
        $viewer = $this->user($agency, $memberRole);
        $template = SiteTemplate::create([
            'name' => 'Protected template',
            'slug' => 'protected-template',
            'pages' => [],
            'status' => SiteTemplate::STATUS_ACTIVE,
        ]);

        $this->actingAs($viewer)
            ->get(route('site-templates.index'))
            ->assertOk();

        $this->actingAs($viewer)
            ->post(route('site-templates.apply', $template))
            ->assertForbidden();
    }

    public function test_agency_context_is_cleared_after_each_request(): void
    {
        $agency = $this->agency('Context');
        $admin = $this->admin($agency);

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertOk();

        $this->assertFalse(AgencyContext::has());
    }

    public function test_registration_suffixes_colliding_agency_slugs(): void
    {
        $this->seed(PermissionSeeder::class);

        $first = app(AuthService::class)->register([
            'agency_name' => 'Atlas & Travel',
            'name' => 'First owner',
            'email' => 'first-owner@example.test',
            'password' => 'StrongPass1!',
        ]);
        $second = app(AuthService::class)->register([
            'agency_name' => 'Atlas Travel',
            'name' => 'Second owner',
            'email' => 'second-owner@example.test',
            'password' => 'StrongPass1!',
        ]);

        $this->assertSame('atlas-travel', $first->agency->slug);
        $this->assertSame('atlas-travel-2', $second->agency->slug);
    }

    public function test_managed_users_require_a_strong_password(): void
    {
        $agency = $this->agency('Passwords');
        $admin = $this->admin($agency);
        $memberRole = $this->role($agency, 'Member');

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Weak user',
                'email' => 'weak-user@example.test',
                'password' => 'password',
                'role_id' => $memberRole->id,
            ])
            ->assertSessionHasErrors('password');

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Strong user',
                'email' => 'strong-user@example.test',
                'password' => 'StrongPass1!',
                'role_id' => $memberRole->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'agency_id' => $agency->id,
            'email' => 'strong-user@example.test',
        ]);
    }

    private function agency(string $name): Agency
    {
        return Agency::create([
            'name' => $name,
            'slug' => str($name)->slug()->append('-', str()->random(6))->toString(),
            'email' => str()->random(8).'@agency.test',
        ]);
    }

    private function admin(Agency $agency): User
    {
        return $this->user($agency, $this->role($agency, 'Admin', 'admin'));
    }

    private function user(Agency $agency, Role $role, ?string $email = null): User
    {
        return tap(User::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'role_id' => $role->id,
            'name' => 'Test user',
            'email' => $email ?? str()->random(8).'@user.test',
            'password' => 'StrongPass1!',
        ]), fn (User $user) => $user->markEmailAsVerified());
    }

    private function role(Agency $agency, string $name, ?string $slug = null): Role
    {
        return Role::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'name' => $name,
            'slug' => $slug,
        ]);
    }

    private function page(Agency $agency, string $slug): Page
    {
        return Page::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'title' => str($slug)->headline()->toString(),
            'slug' => $slug,
            'structure' => [],
            'status' => Page::STATUS_DRAFT,
        ]);
    }

    private function permission(string $slug): Permission
    {
        return Permission::firstOrCreate(
            ['slug' => $slug],
            ['name' => str($slug)->replace('.', ' ')->headline()->toString()]
        );
    }
}
