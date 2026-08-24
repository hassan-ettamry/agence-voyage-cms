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
        $this->actingAs($actor)->put(route('permissions.modules.update', 'existing'), ['actions' => ['view']])->assertForbidden();
        $this->actingAs($actor)->put(route('permissions.update', $permission), $this->permissionPayload('Update Permission', 'update.permission'))->assertForbidden();
        $this->actingAs($actor)->delete(route('permissions.destroy', $permission))->assertForbidden();

        $this->actingAs($this->userWithPermissions(['permission.view']))
            ->get(route('permissions.index'))
            ->assertOk();

        $this->actingAs($this->userWithPermissions(['permission.create']))
            ->post(route('permissions.store'), ['modules' => ['page']])
            ->assertRedirect(route('permissions.index'));

        $this->actingAs($this->userWithPermissions(['permission.update']))
            ->put(route('permissions.modules.update', 'page'), ['actions' => ['view']])
            ->assertRedirect();

        $this->actingAs($this->userWithPermissions(['permission.update']))
            ->put(route('permissions.update', $permission), $this->permissionPayload('Updated Permission', 'updated.permission'))
            ->assertRedirect();

        $this->actingAs($this->userWithPermissions(['permission.delete']))
            ->delete(route('permissions.destroy', $permission))
            ->assertRedirect();
    }

    public function test_permission_index_groups_permissions_by_module(): void
    {
        $actor = $this->userWithPermissions(['permission.view']);
        $this->permission('View Pages', 'page.view');
        $this->permission('Create Page', 'page.create');
        $this->permission('View Components', 'component.view');
        $this->permission('View Dashboard', 'dashboard.view');

        $response = $this->actingAs($actor)->get(route('permissions.index'));

        $response->assertOk();

        $modules = collect($response->viewData('permissionModules')->items());
        $page = $modules->firstWhere('key', 'page');

        $this->assertNotNull($page);
        $this->assertSame(['create', 'view'], collect($page['permissions'])->pluck('action_key')->sort()->values()->all());
        $this->assertFalse($modules->pluck('key')->contains('component'));
        $this->assertFalse($modules->pluck('key')->contains('dashboard'));
    }

    public function test_permission_index_exposes_only_modules_that_do_not_exist_for_creation(): void
    {
        $actor = $this->userWithPermissions(['permission.view']);
        $this->permission('View Pages', 'page.view');
        $this->permission('Create Pages', 'page.create');

        $response = $this->actingAs($actor)->get(route('permissions.index'));

        $response->assertOk();

        $newModules = collect($response->viewData('newPermissionModules'))->pluck('key');

        $this->assertFalse($newModules->contains('page'));
        $this->assertTrue($newModules->contains('user'));
        $this->assertTrue($newModules->contains('role'));
        $this->assertFalse($newModules->contains('permission'));
    }

    public function test_permission_create_can_create_selected_missing_modules(): void
    {
        $actor = $this->userWithPermissions(['permission.create']);

        $this->actingAs($actor)
            ->post(route('permissions.store'), [
                'modules' => [
                    'page',
                ],
            ])
            ->assertRedirect(route('permissions.index'));

        $this->assertDatabaseHas('permissions', [
            'slug' => 'page.view',
            'name' => 'View Pages',
        ]);

        $this->assertDatabaseHas('permissions', [
            'slug' => 'page.create',
            'name' => 'Create Page',
        ]);
    }

    public function test_permission_create_rejects_unknown_catalog_modules(): void
    {
        $actor = $this->userWithPermissions(['permission.create']);

        $this->actingAs($actor)
            ->post(route('permissions.store'), [
                'modules' => [
                    'unknown',
                ],
            ])
            ->assertSessionHasErrors('modules');

        $this->assertDatabaseMissing('permissions', [
            'slug' => 'unknown.view',
        ]);
    }

    public function test_permission_module_update_syncs_existing_actions(): void
    {
        $actor = $this->userWithPermissions(['permission.update']);
        $this->permission('View Pages', 'page.view');
        $this->permission('Create Page', 'page.create');
        $this->permission('Publish Page', 'page.publish');
        $this->permission('View Components', 'component.view');

        $this->actingAs($actor)
            ->put(route('permissions.modules.update', 'page'), [
                'module_key' => 'page',
                'actions' => ['view', 'delete'],
            ])
            ->assertRedirect(route('permissions.index'));

        $this->assertDatabaseHas('permissions', [
            'slug' => 'page.view',
        ]);
        $this->assertDatabaseHas('permissions', [
            'slug' => 'page.delete',
            'name' => 'Delete Page',
        ]);
        $this->assertDatabaseMissing('permissions', [
            'slug' => 'page.create',
        ]);
        $this->assertDatabaseHas('permissions', [
            'slug' => 'page.publish',
            'name' => 'Publish Page',
        ]);
        $this->assertDatabaseHas('permissions', [
            'slug' => 'component.view',
        ]);
    }

    public function test_permission_module_update_rejects_unknown_actions(): void
    {
        $actor = $this->userWithPermissions(['permission.update']);

        $this->actingAs($actor)
            ->put(route('permissions.modules.update', 'page'), [
                'module_key' => 'page',
                'actions' => ['publish'],
            ])
            ->assertSessionHasErrors('actions');
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

        return tap(User::create([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'agency_id' => $agency->id,
            'role_id' => $role->id,
        ]), fn (User $user) => $user->markEmailAsVerified());
    }

    private function adminUser(): User
    {
        $agency = $this->agency();
        $role = $this->role('Admin', $agency, 'admin');

        return tap(User::create([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'agency_id' => $agency->id,
            'role_id' => $role->id,
        ]), fn (User $user) => $user->markEmailAsVerified());
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
