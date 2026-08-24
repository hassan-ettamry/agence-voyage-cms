<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Support\AgencyContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UserTenantScopingTest extends TestCase
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

    public function test_user_index_only_shows_users_from_current_agency(): void
    {
        $agencyA = $this->agency('Agency A');
        $agencyB = $this->agency('Agency B');
        $viewer = $this->userWithPermissions($agencyA, ['user.view']);
        $userA = $this->user($agencyA, 'visible@example.com');
        $userB = $this->user($agencyB, 'hidden@example.com');

        $response = $this->actingAs($viewer)->get(route('users.index'));

        $response->assertOk();
        $response->assertSee($viewer->email);
        $response->assertSee($userA->email);
        $response->assertDontSee($userB->email);
    }

    public function test_user_stats_only_count_current_agency(): void
    {
        $agencyA = $this->agency('Agency A');
        $agencyB = $this->agency('Agency B');
        $adminA = $this->adminUser($agencyA, 'admin-a@example.com');

        $this->user($agencyA, 'member-a@example.com');
        $this->adminUser($agencyB, 'admin-b@example.com');

        $response = $this->actingAs($adminA)->get(route('users.index'));
        $stats = $response->viewData('stats');

        $response->assertOk();
        $this->assertSame(2, $stats[0]['value']);
        $this->assertSame(1, $stats[1]['value']);
    }

    public function test_created_user_receives_creator_agency(): void
    {
        $agencyA = $this->agency('Agency A');
        $creator = $this->userWithPermissions($agencyA, ['user.create']);
        $role = $this->role($agencyA, 'Editor');

        $this->actingAs($creator)
            ->post(route('users.store'), [
                'name' => 'Created User',
                'email' => 'created@example.com',
                'password' => 'password',
                'role_id' => $role->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'created@example.com',
            'agency_id' => $agencyA->id,
            'role_id' => $role->id,
        ]);
    }

    public function test_role_from_another_agency_is_rejected_for_create_and_update(): void
    {
        $agencyA = $this->agency('Agency A');
        $agencyB = $this->agency('Agency B');
        $actor = $this->userWithPermissions($agencyA, ['user.create', 'user.update']);
        $target = $this->user($agencyA, 'target@example.com');
        $foreignRole = $this->role($agencyB, 'Foreign Role');

        $this->actingAs($actor)
            ->post(route('users.store'), [
                'name' => 'Invalid Role User',
                'email' => 'invalid-role@example.com',
                'password' => 'password',
                'role_id' => $foreignRole->id,
            ])
            ->assertSessionHasErrors('role_id');

        $this->actingAs($actor)
            ->put(route('users.update', $target), [
                'name' => 'Target User',
                'email' => 'target@example.com',
                'password' => '',
                'role_id' => $foreignRole->id,
            ])
            ->assertSessionHasErrors('role_id');
    }

    public function test_user_update_and_delete_cannot_target_another_agency(): void
    {
        $agencyA = $this->agency('Agency A');
        $agencyB = $this->agency('Agency B');
        $adminA = $this->adminUser($agencyA, 'admin-a@example.com');
        $targetB = $this->user($agencyB, 'target-b@example.com');
        $roleA = $this->role($agencyA, 'Editor');

        $this->actingAs($adminA)
            ->put(route('users.update', $targetB), [
                'name' => 'Cross Agency Update',
                'email' => 'target-b@example.com',
                'password' => '',
                'role_id' => $roleA->id,
            ])
            ->assertNotFound();

        $this->actingAs($adminA)
            ->delete(route('users.destroy', $targetB))
            ->assertNotFound();
    }

    private function agency(string $name): Agency
    {
        $name = $name . ' ' . uniqid();

        return Agency::create([
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
            'email' => fake()->unique()->safeEmail(),
        ]);
    }

    private function userWithPermissions(Agency $agency, array $permissionSlugs): User
    {
        $role = $this->role($agency, 'Member ' . uniqid());

        foreach ($permissionSlugs as $slug) {
            $role->permissions()->attach($this->permission($slug));
        }

        return $this->user($agency, fake()->unique()->safeEmail(), $role);
    }

    private function adminUser(Agency $agency, string $email): User
    {
        return $this->user($agency, $email, $this->role($agency, 'Admin', 'admin'));
    }

    private function user(Agency $agency, string $email, ?Role $role = null): User
    {
        $role ??= $this->role($agency, 'Member ' . uniqid());

        return tap(User::create([
            'name' => fake()->name(),
            'email' => $email,
            'password' => 'password',
            'agency_id' => $agency->id,
            'role_id' => $role->id,
        ]), fn (User $user) => $user->markEmailAsVerified());
    }

    private function role(Agency $agency, string $name, ?string $slug = null): Role
    {
        return Role::create([
            'agency_id' => $agency->id,
            'name' => $name,
            'slug' => $slug,
        ]);
    }

    private function permission(string $slug): Permission
    {
        return Permission::firstOrCreate(
            ['slug' => $slug],
            ['name' => str($slug)->replace('.', ' ')->title()->toString()]
        );
    }
}
