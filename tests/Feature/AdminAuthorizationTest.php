<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_user_administration_requires_matching_permissions(): void
    {
        $agency = $this->agency();
        $actor = $this->userWithPermissions([], $agency);
        $target = $this->userWithPermissions([], $agency);
        $role = $this->role('Managed Role', $agency);

        $this->actingAs($actor)->get(route('users.index'))->assertForbidden();
        $this->actingAs($actor)->post(route('users.store'), $this->userPayload($role))->assertForbidden();
        $this->actingAs($actor)->put(route('users.update', $target), $this->userPayload($role))->assertForbidden();
        $this->actingAs($actor)->delete(route('users.destroy', $target))->assertForbidden();

        $this->actingAs($this->userWithPermissions(['user.view'], $agency))
            ->get(route('users.index'))
            ->assertOk();

        $this->actingAs($this->userWithPermissions(['user.create'], $agency))
            ->post(route('users.store'), $this->userPayload($role, 'created@example.com'))
            ->assertRedirect();

        $this->actingAs($this->userWithPermissions(['user.update'], $agency))
            ->put(route('users.update', $target), $this->userPayload($role, 'updated@example.com'))
            ->assertRedirect();

        $this->actingAs($this->userWithPermissions(['user.delete'], $agency))
            ->delete(route('users.destroy', $target))
            ->assertRedirect();
    }

    public function test_role_administration_requires_matching_permissions(): void
    {
        $agency = $this->agency();
        $actor = $this->userWithPermissions([], $agency);
        $role = $this->role('Editor', $agency);

        $this->actingAs($actor)->get(route('roles.index'))->assertForbidden();
        $this->actingAs($actor)->post(route('roles.store'), $this->rolePayload('Manager'))->assertForbidden();
        $this->actingAs($actor)->put(route('roles.update', $role), $this->rolePayload('Publisher'))->assertForbidden();
        $this->actingAs($actor)->delete(route('roles.destroy', $role))->assertForbidden();

        $this->actingAs($this->userWithPermissions(['role.view'], $agency))
            ->get(route('roles.index'))
            ->assertOk();

        $this->actingAs($this->userWithPermissions(['role.create'], $agency))
            ->post(route('roles.store'), $this->rolePayload('Creator', $agency))
            ->assertRedirect();

        $this->actingAs($this->userWithPermissions(['role.update'], $agency))
            ->put(route('roles.update', $role), $this->rolePayload('Updater', $agency))
            ->assertRedirect();

        $this->actingAs($this->userWithPermissions(['role.delete'], $agency))
            ->delete(route('roles.destroy', $role))
            ->assertRedirect();
    }

    public function test_permission_administration_requires_matching_permissions(): void
    {
        $actor = $this->userWithPermissions();
        $permission = $this->permission('Existing Permission', 'existing.permission');

        $this->actingAs($actor)->get(route('permissions.index'))->assertForbidden();
        $this->actingAs($actor)->post(route('permissions.store'), $this->permissionPayload('Create Permission', 'create.permission'))->assertForbidden();
        $this->actingAs($actor)->put(route('permissions.update', $permission), $this->permissionPayload('Update Permission', 'update.permission'))->assertForbidden();
        $this->actingAs($actor)->delete(route('permissions.destroy', $permission))->assertForbidden();

        $this->actingAs($this->userWithPermissions(['permission.view']))
            ->get(route('permissions.index'))
            ->assertOk();

        $this->actingAs($this->userWithPermissions(['permission.create']))
            ->post(route('permissions.store'), $this->permissionPayload('Created Permission', 'created.permission'))
            ->assertRedirect();

        $this->actingAs($this->userWithPermissions(['permission.update']))
            ->put(route('permissions.update', $permission), $this->permissionPayload('Updated Permission', 'updated.permission'))
            ->assertRedirect();

        $this->actingAs($this->userWithPermissions(['permission.delete']))
            ->delete(route('permissions.destroy', $permission))
            ->assertRedirect();
    }

    public function test_admin_role_bypasses_administration_permissions(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)->get(route('users.index'))->assertOk();
        $this->actingAs($admin)->get(route('roles.index'))->assertOk();
        $this->actingAs($admin)->get(route('permissions.index'))->assertOk();
    }

    private function userWithPermissions(array $permissionSlugs = [], ?Agency $agency = null): User
    {
        $agency ??= $this->agency();
        $role = $this->role('Member ' . uniqid(), $agency);

        foreach ($permissionSlugs as $slug) {
            $role->permissions()->attach($this->permission($slug, $slug));
        }

        return User::create([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'agency_id' => $agency->id,
            'role_id' => $role->id,
        ]);
    }

    private function adminUser(): User
    {
        $agency = $this->agency();
        $role = $this->role('Admin', $agency, 'admin');

        return User::create([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'agency_id' => $agency->id,
            'role_id' => $role->id,
        ]);
    }

    private function agency(): Agency
    {
        $name = fake()->company() . ' ' . uniqid();

        return Agency::create([
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
            'email' => fake()->unique()->safeEmail(),
        ]);
    }

    private function role(string $name = 'Member', ?Agency $agency = null, ?string $slug = null): Role
    {
        $agency ??= $this->agency();

        return Role::create([
            'agency_id' => $agency->id,
            'name' => $name,
            'slug' => $slug,
        ]);
    }

    private function permission(string $name, string $slug): Permission
    {
        return Permission::firstOrCreate(
            ['slug' => $slug],
            ['name' => $name]
        );
    }

    private function userPayload(Role $role, string $email = 'user@example.com'): array
    {
        return [
            'name' => 'Managed User',
            'email' => $email,
            'password' => 'password',
            'role_id' => $role->id,
        ];
    }

    private function rolePayload(string $name, ?Agency $agency = null): array
    {
        $agency ??= $this->agency();

        return [
            'agency_id' => $agency->id,
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
        ];
    }

    private function permissionPayload(string $name, string $slug): array
    {
        return [
            'name' => $name,
            'slug' => $slug,
        ];
    }
}
