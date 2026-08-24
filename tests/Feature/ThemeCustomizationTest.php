<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Page;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Theme;
use App\Models\User;
use App\Services\AgencyThemeService;
use App\Support\AgencyContext;
use Database\Seeders\ThemeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ThemeCustomizationTest extends TestCase
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

    public function test_effective_variables_merge_defaults_preset_and_agency_overrides(): void
    {
        $theme = $this->theme('Ocean', [
            'primary' => '#112233',
            'radius' => '18px',
        ]);
        $agency = $this->agency('Ocean Agency', $theme, [
            'primary' => '#abcdef',
            'accent' => '#fedcba',
        ]);
        $service = app(AgencyThemeService::class);

        $effective = $service->effectiveVariables($agency);

        $this->assertSame('#abcdef', $effective['primary']);
        $this->assertSame('#fedcba', $effective['accent']);
        $this->assertSame('18px', $effective['radius']);
        $this->assertSame(config('site-theme.defaults.text'), $effective['text']);

        $service->updateOverrides($agency, [
            ...$effective,
            'primary' => '#112233',
            'accent' => '#010203',
        ]);

        $this->assertSame(['accent' => '#010203'], $agency->fresh()->theme_overrides);
    }

    public function test_signature_travel_theme_is_seeded_idempotently_with_expected_tokens(): void
    {
        $this->seed(ThemeSeeder::class);
        $this->seed(ThemeSeeder::class);

        $theme = Theme::query()->where('slug', 'signature-travel')->sole();

        $this->assertSame('Signature Travel', $theme->name);
        $this->assertSame(Theme::STATUS_ACTIVE, $theme->status);
        $this->assertSame('#c84c2f', $theme->variables['primary']);
        $this->assertSame('#102a2f', $theme->variables['secondary']);
        $this->assertSame('#f7f4ed', $theme->variables['background']);
        $this->assertSame('6px', $theme->variables['radius']);
        $this->assertSame('soft', $theme->variables['shadow']);
        $this->assertSame('Georgia, "Times New Roman", serif', $theme->variables['headingFont']);
        $this->assertSame(1, Theme::query()->where('slug', 'signature-travel')->count());
    }

    public function test_theme_overrides_are_isolated_between_agencies(): void
    {
        $theme = $this->theme('Shared');
        $first = $this->agency('First Agency', $theme, ['primary' => '#123456']);
        $second = $this->agency('Second Agency', $theme);
        $service = app(AgencyThemeService::class);

        $this->assertSame('#123456', $service->effectiveVariables($first)['primary']);
        $this->assertSame($theme->variables['primary'], $service->effectiveVariables($second)['primary']);
        $this->assertNull($second->fresh()->theme_overrides);
    }

    public function test_changing_preset_requires_confirmation_and_same_preset_preserves_overrides(): void
    {
        $current = $this->theme('Current');
        $next = $this->theme('Next', ['primary' => '#654321']);
        $agency = $this->agency('Preset Agency', $current, ['primary' => '#123456']);
        $service = app(AgencyThemeService::class);

        $service->applyTheme($agency, $current);
        $this->assertSame(['primary' => '#123456'], $agency->fresh()->theme_overrides);

        try {
            $service->applyTheme($agency, $next);
            $this->fail('Changing a customized preset must require confirmation.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('confirm_reset', $exception->errors());
        }

        $service->applyTheme($agency, $next, true);

        $agency->refresh();
        $this->assertSame($next->id, $agency->theme_id);
        $this->assertNull($agency->theme_overrides);
    }

    public function test_theme_permissions_are_separate(): void
    {
        $theme = $this->theme('Permissions');
        $agency = $this->agency('Permission Agency', $theme);
        $none = $this->userWithPermissions($agency);
        $viewer = $this->userWithPermissions($agency, ['theme.view']);
        $applier = $this->userWithPermissions($agency, ['theme.apply']);
        $updater = $this->userWithPermissions($agency, ['theme.view', 'theme.update']);
        $otherTheme = $this->theme('Other');

        $this->actingAs($none)->get(route('themes.index'))->assertForbidden();

        $this->actingAs($viewer)->get(route('themes.index'))->assertOk();
        $this->actingAs($viewer)
            ->get(route('themes.customize'))
            ->assertOk()
            ->assertSee('--site-primary:', false);
        $this->actingAs($viewer)
            ->post(route('themes.apply', $otherTheme))
            ->assertForbidden();
        $this->actingAs($viewer)
            ->put(route('themes.customize.update'), $this->themePayload())
            ->assertForbidden();

        $this->actingAs($applier)
            ->post(route('themes.apply', $otherTheme))
            ->assertRedirect(route('themes.index'));

        $this->actingAs($updater)
            ->put(route('themes.customize.update'), $this->themePayload('#010203'))
            ->assertRedirect(route('themes.customize'));

        $this->assertSame('#010203', $agency->fresh()->theme_overrides['primary']);
    }

    public function test_builder_and_public_page_receive_theme_variables_while_local_style_wins(): void
    {
        $theme = $this->theme('Rendered');
        $agency = $this->agency('Rendered Agency', $theme, ['primary' => '#abcdef']);
        $admin = $this->admin($agency);
        $page = Page::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'title' => 'Theme Page',
            'slug' => 'theme-page',
            'status' => Page::STATUS_PUBLISHED,
            'structure' => [
                $this->node('section', [], [
                    $this->node('container', [], [
                        $this->node('button', [
                            'text' => 'Local button',
                            'backgroundColor' => '#ff0000',
                        ]),
                    ]),
                ]),
            ],
        ]);

        $this->actingAs($admin)
            ->get(route('pages.builder', $page))
            ->assertOk()
            ->assertSee('--site-primary: #abcdef;', false);

        $this->get(route('public.site.pages.show', [$agency->slug, $page->slug]))
            ->assertOk()
            ->assertSee('--site-primary: #abcdef;', false);

        $localButton = view('components.builder.button', [
            'props' => ['text' => 'Local button', 'backgroundColor' => '#ff0000'],
            'nodeId' => 'local-button',
            'isEditor' => false,
        ])->render();
        $themeButton = view('components.builder.button', [
            'props' => ['text' => 'Theme button'],
            'nodeId' => 'theme-button',
            'isEditor' => false,
        ])->render();
        $themeHeading = view('components.builder.heading', [
            'props' => ['text' => 'Theme heading'],
            'nodeId' => 'theme-heading',
            'isEditor' => false,
        ])->render();

        $this->assertStringContainsString('#ff0000', $localButton);
        $this->assertStringContainsString('var(--site-primary', $themeButton);
        $this->assertStringContainsString('var(--site-text', $themeHeading);
        $this->assertStringContainsString('var(--site-heading-font', $themeHeading);
    }

    private function theme(string $name, array $variables = []): Theme
    {
        return Theme::create([
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(5),
            'variables' => [
                ...config('site-theme.defaults'),
                ...$variables,
            ],
            'status' => Theme::STATUS_ACTIVE,
        ]);
    }

    private function agency(string $name, Theme $theme, ?array $overrides = null): Agency
    {
        return Agency::create([
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(5),
            'email' => Str::random(8).'@agency.test',
            'theme_id' => $theme->id,
            'theme_overrides' => $overrides,
        ]);
    }

    private function userWithPermissions(Agency $agency, array $slugs = []): User
    {
        $role = Role::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'name' => 'Member '.Str::random(5),
            'slug' => 'member-'.Str::random(5),
        ]);

        foreach ($slugs as $slug) {
            $permission = Permission::firstOrCreate(
                ['slug' => $slug],
                ['name' => Str::headline($slug)]
            );
            $role->permissions()->attach($permission);
        }

        return tap(User::withoutGlobalScopes()->create([
            'name' => 'Theme User',
            'email' => Str::random(8).'@user.test',
            'password' => 'password',
            'agency_id' => $agency->id,
            'role_id' => $role->id,
        ]), fn (User $user) => $user->markEmailAsVerified());
    }

    private function admin(Agency $agency): User
    {
        $role = Role::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'name' => 'Admin',
            'slug' => 'admin',
        ]);

        return tap(User::withoutGlobalScopes()->create([
            'name' => 'Theme Admin',
            'email' => Str::random(8).'@admin.test',
            'password' => 'password',
            'agency_id' => $agency->id,
            'role_id' => $role->id,
        ]), fn (User $user) => $user->markEmailAsVerified());
    }

    private function themePayload(string $primary = '#112233'): array
    {
        return [
            ...config('site-theme.defaults'),
            'primary' => $primary,
        ];
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
