<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Role;
use App\Models\SiteArchive;
use App\Models\SiteTemplate;
use App\Models\Theme;
use App\Models\User;
use App\Support\AgencyContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

class SiteTemplateApplicationTest extends TestCase
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

    public function test_applying_template_to_empty_agency_creates_pages_menu_and_theme(): void
    {
        $agency = $this->agency('Empty Agency');
        $user = $this->user($agency);
        $theme = $this->theme('Ocean Blue');
        $template = $this->siteTemplate($theme);

        $response = $this->actingAs($user)
            ->post(route('site-templates.apply', $template));

        $home = Page::withoutGlobalScopes()
            ->where('agency_id', $agency->id)
            ->where('slug', 'home')
            ->firstOrFail();

        $response->assertRedirect(route('pages.builder', $home));

        $this->assertSame(2, Page::withoutGlobalScopes()->where('agency_id', $agency->id)->count());
        $this->assertSame(0, SiteArchive::where('agency_id', $agency->id)->count());

        $menu = Menu::withoutGlobalScopes()
            ->where('agency_id', $agency->id)
            ->where('is_default', true)
            ->firstOrFail();

        $this->assertSame(2, $menu->items()->count());

        $agency->refresh();
        $this->assertSame($theme->id, $agency->theme_id);
        $this->assertSame($template->id, $agency->active_site_template_id);
        $this->assertNotNull($agency->template_applied_at);
    }

    public function test_applying_template_with_existing_pages_requires_confirmation_then_archives_and_replaces(): void
    {
        $agency = $this->agency('Agency With Site');
        $otherAgency = $this->agency('Other Agency');
        $user = $this->user($agency);
        $theme = $this->theme('Sunset');
        $template = $this->siteTemplate($theme, 'premium-template');

        $oldPage = Page::create([
            'agency_id' => $agency->id,
            'title' => 'Old Home',
            'slug' => 'old-home',
            'structure' => [$this->node('section')],
            'meta' => ['description' => 'Old content'],
            'status' => Page::STATUS_DRAFT,
        ]);

        Page::create([
            'agency_id' => $otherAgency->id,
            'title' => 'Other Home',
            'slug' => 'other-home',
            'structure' => [$this->node('section')],
            'status' => Page::STATUS_DRAFT,
        ]);

        $oldMenu = Menu::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'name' => 'Old Menu',
            'slug' => 'old-menu',
            'is_default' => true,
        ]);

        MenuItem::create([
            'menu_id' => $oldMenu->id,
            'page_id' => $oldPage->id,
            'title' => 'Old Home',
            'order' => 1,
        ]);

        $this->actingAs($user)
            ->post(route('site-templates.apply', $template))
            ->assertSessionHasErrors('confirm_replace');

        $response = $this->actingAs($user)
            ->post(route('site-templates.apply', $template), [
                'confirm_replace' => '1',
            ]);

        $home = Page::withoutGlobalScopes()
            ->where('agency_id', $agency->id)
            ->where('slug', 'home')
            ->firstOrFail();

        $response->assertRedirect(route('pages.builder', $home));

        $this->assertDatabaseMissing('pages', [
            'agency_id' => $agency->id,
            'slug' => 'old-home',
        ]);

        $this->assertDatabaseHas('pages', [
            'agency_id' => $otherAgency->id,
            'slug' => 'other-home',
        ]);

        $archive = SiteArchive::where('agency_id', $agency->id)->firstOrFail();

        $this->assertSame($template->id, $archive->source_template_id);
        $this->assertSame('old-home', $archive->snapshot['pages'][0]['slug']);
        $this->assertSame('Old Menu', $archive->snapshot['menus'][0]['name']);

        $agency->refresh();
        $this->assertSame($theme->id, $agency->theme_id);
        $this->assertSame($template->id, $agency->active_site_template_id);
        $this->assertNotNull($agency->template_applied_at);
    }

    public function test_dashboard_cta_disappears_after_template_application(): void
    {
        $agency = $this->agency('CTA Agency');
        $user = $this->user($agency);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Complete the agency workspace');

        $agency->forceFill([
            'template_applied_at' => now(),
            'onboarding_status' => Agency::ONBOARDING_COMPLETED,
            'onboarding_completed_at' => now(),
        ])->save();
        Cache::flush();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Complete the agency workspace');
    }

    public function test_site_templates_and_themes_are_separate_admin_pages(): void
    {
        $agency = $this->agency('Design Agency');
        $user = $this->user($agency);
        $theme = $this->theme('Clean Theme');
        $template = $this->siteTemplate($theme, 'clean-site');

        $this->actingAs($user)
            ->get(route('themes.index'))
            ->assertOk()
            ->assertSee('Clean Theme')
            ->assertDontSee($template->name);

        $this->actingAs($user)
            ->get(route('site-templates.index'))
            ->assertOk()
            ->assertSee($template->name)
            ->assertDontSee('Apply theme');
    }

    public function test_applying_new_template_requires_theme_reset_confirmation_and_archives_overrides(): void
    {
        $agency = $this->agency('Customized Agency');
        $user = $this->user($agency);
        $currentTheme = $this->theme('Current Theme');
        $nextTheme = $this->theme('Next Theme');
        $template = $this->siteTemplate($nextTheme, 'next-site');

        $agency->forceFill([
            'theme_id' => $currentTheme->id,
            'theme_overrides' => ['primary' => '#123456'],
        ])->save();
        Page::create([
            'agency_id' => $agency->id,
            'title' => 'Old Theme Page',
            'slug' => 'old-theme-page',
            'structure' => [$this->node('section')],
            'status' => Page::STATUS_DRAFT,
        ]);

        $this->actingAs($user)
            ->post(route('site-templates.apply', $template), [
                'confirm_replace' => '1',
            ])
            ->assertSessionHasErrors('confirm_theme_reset');

        $this->actingAs($user)
            ->post(route('site-templates.apply', $template), [
                'confirm_theme_reset' => '1',
                'confirm_replace' => '1',
            ])
            ->assertRedirect();

        $archive = SiteArchive::where('agency_id', $agency->id)->firstOrFail();

        $this->assertSame(['primary' => '#123456'], $archive->snapshot['agency']['theme_overrides']);
        $this->assertNull($agency->fresh()->theme_overrides);
    }

    private function agency(string $name): Agency
    {
        return Agency::create([
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(6),
            'email' => Str::slug($name).'-'.Str::random(6).'@example.com',
        ]);
    }

    private function user(Agency $agency): User
    {
        $role = Role::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'name' => 'Admin',
            'slug' => 'admin',
        ]);

        return User::withoutGlobalScopes()->create([
            'name' => 'Template Admin',
            'email' => Str::random(8).'@example.com',
            'password' => 'password',
            'agency_id' => $agency->id,
            'role_id' => $role->id,
        ]);
    }

    private function theme(string $name): Theme
    {
        return Theme::create([
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(6),
            'variables' => [
                'primary' => '#2563eb',
                'background' => '#ffffff',
            ],
            'status' => Theme::STATUS_ACTIVE,
        ]);
    }

    private function siteTemplate(Theme $theme, string $slug = 'starter-site'): SiteTemplate
    {
        return SiteTemplate::create([
            'name' => 'Starter Site '.Str::random(4),
            'slug' => $slug.'-'.Str::random(6),
            'description' => 'Starter template for travel agencies.',
            'theme_id' => $theme->id,
            'status' => SiteTemplate::STATUS_ACTIVE,
            'pages' => [
                [
                    'title' => 'Home',
                    'slug' => 'home',
                    'status' => Page::STATUS_DRAFT,
                    'include_in_menu' => true,
                    'structure' => [
                        $this->node('section', [], [
                            $this->node('container', ['containerRole' => 'layout'], [
                                $this->node('text', ['text' => 'Home hero']),
                            ]),
                        ]),
                    ],
                    'meta' => ['description' => 'Home page'],
                ],
                [
                    'title' => 'Contact',
                    'slug' => 'contact',
                    'status' => Page::STATUS_DRAFT,
                    'include_in_menu' => true,
                    'structure' => [
                        $this->node('section', [], [
                            $this->node('contact-form'),
                        ]),
                    ],
                    'meta' => ['description' => 'Contact page'],
                ],
            ],
        ]);
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
