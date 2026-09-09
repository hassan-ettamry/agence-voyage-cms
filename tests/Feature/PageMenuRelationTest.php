<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PageMenuRelationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_page_can_be_created_without_menu(): void
    {
        $agency = $this->agency();
        $user = $this->userWithPermissions($agency, ['page.create']);

        $this->actingAs($user)
            ->post(route('pages.store'), [
                'title' => 'No Menu Page',
                'status' => 'draft',
                'menu_selection' => 'none',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('pages', [
            'agency_id' => $agency->id,
            'title' => 'No Menu Page',
        ]);
        $this->assertSame(0, MenuItem::count());
    }

    public function test_page_can_be_created_in_default_menu(): void
    {
        $agency = $this->agency();
        $user = $this->userWithPermissions($agency, ['page.create']);

        $this->actingAs($user)
            ->post(route('pages.store'), [
                'title' => 'Default Menu Page',
                'status' => 'draft',
                'menu_selection' => 'default',
            ])
            ->assertRedirect();

        $menu = Menu::withoutGlobalScopes()
            ->where('agency_id', $agency->id)
            ->where('is_default', true)
            ->firstOrFail();

        $this->assertDatabaseHas('menu_items', [
            'menu_id' => $menu->id,
            'title' => 'Default Menu Page',
        ]);
    }

    public function test_page_can_be_created_in_specific_menu(): void
    {
        $agency = $this->agency();
        $user = $this->userWithPermissions($agency, ['page.create']);
        $menu = $this->menu($agency, 'Footer Menu');

        $this->actingAs($user)
            ->post(route('pages.store'), [
                'title' => 'Footer Page',
                'status' => 'draft',
                'menu_selection' => (string) $menu->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('menu_items', [
            'menu_id' => $menu->id,
            'title' => 'Footer Page',
        ]);
    }

    public function test_page_cannot_use_menu_from_another_agency(): void
    {
        $agencyA = $this->agency();
        $agencyB = $this->agency();
        $user = $this->userWithPermissions($agencyA, ['page.create']);
        $foreignMenu = $this->menu($agencyB, 'Foreign Menu');

        $this->actingAs($user)
            ->post(route('pages.store'), [
                'title' => 'Invalid Menu Page',
                'status' => 'draft',
                'menu_selection' => (string) $foreignMenu->id,
            ])
            ->assertSessionHasErrors('menu_selection');
    }

    public function test_page_menu_can_be_changed_on_update(): void
    {
        $agency = $this->agency();
        $user = $this->userWithPermissions($agency, ['page.create', 'page.update']);
        $firstMenu = $this->menu($agency, 'Main Menu', true);
        $secondMenu = $this->menu($agency, 'Footer Menu');

        $this->actingAs($user)
            ->post(route('pages.store'), [
                'title' => 'Move Me',
                'status' => 'draft',
                'menu_selection' => (string) $firstMenu->id,
            ])
            ->assertRedirect();

        $page = \App\Models\Page::where('title', 'Move Me')->firstOrFail();

        $this->actingAs($user)
            ->put(route('pages.update', $page), [
                'title' => 'Move Me',
                'slug' => $page->slug,
                'status' => 'draft',
                'menu_selection' => (string) $secondMenu->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('menu_items', [
            'menu_id' => $firstMenu->id,
            'page_id' => $page->id,
        ]);
        $this->assertDatabaseHas('menu_items', [
            'menu_id' => $secondMenu->id,
            'page_id' => $page->id,
        ]);
    }

    private function agency(): Agency
    {
        $name = fake()->company().' '.uniqid();

        return Agency::create([
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
            'email' => fake()->unique()->safeEmail(),
        ]);
    }

    private function menu(Agency $agency, string $name, bool $default = false): Menu
    {
        return Menu::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
            'is_default' => $default,
        ]);
    }

    private function userWithPermissions(Agency $agency, array $permissionSlugs): User
    {
        $role = Role::create([
            'agency_id' => $agency->id,
            'name' => 'Member '.uniqid(),
            'slug' => 'member-'.uniqid(),
        ]);

        foreach ($permissionSlugs as $slug) {
            $role->permissions()->attach(
                Permission::firstOrCreate(
                    ['slug' => $slug],
                    ['name' => str($slug)->replace('.', ' ')->title()->toString()]
                )
            );
        }

        return tap(User::create([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'agency_id' => $agency->id,
            'role_id' => $role->id,
        ]), fn (User $user) => $user->markEmailAsVerified());
    }
}
