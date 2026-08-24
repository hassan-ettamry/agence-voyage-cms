<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Page;
use App\Models\Role;
use App\Models\SiteArchive;
use App\Models\SiteTemplate;
use App\Models\Theme;
use App\Models\User;
use App\Support\AgencyContext;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

class OnboardingFlowTest extends TestCase
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

    public function test_new_registration_starts_onboarding(): void
    {
        $this->seed(PermissionSeeder::class);

        $response = $this->post(route('register'), [
            'agency_name' => 'Fresh Travel',
            'name' => 'Fresh Admin',
            'email' => 'fresh@example.test',
            'password' => 'Passw0rd!',
            'password_confirmation' => 'Passw0rd!',
        ]);

        $response->assertRedirect(route('verification.notice'));

        $agency = Agency::where('email', 'fresh@example.test')->firstOrFail();
        $user = User::withoutGlobalScopes()->where('email', 'fresh@example.test')->firstOrFail();
        $this->assertSame(Agency::ONBOARDING_PENDING, $agency->onboarding_status);
        $this->assertSame(Agency::ONBOARDING_STEP_PROFILE, $agency->onboarding_step);
        $this->assertTrue($agency->onboarding_auto_start);
        $this->assertFalse($user->hasVerifiedEmail());

        $user->markEmailAsVerified();
        $this->actingAs($user)
            ->get(route('onboarding.index'))
            ->assertRedirect(route('onboarding.profile'));
    }

    public function test_progress_is_restored_after_logout_and_login(): void
    {
        [$agency, $user] = $this->adminAgency(autoStart: true);
        $theme = $this->theme('Ocean');
        $template = $this->siteTemplate($theme);

        $this->actingAs($user)
            ->post(route('onboarding.profile.store'), [
                'name' => 'Updated Travel',
                'email' => 'contact@updated.test',
                'phone' => '+212600000000',
                'address' => 'Marrakech',
            ])
            ->assertRedirect(route('onboarding.template'));

        $this->actingAs($user)
            ->post(route('onboarding.template.store'), ['template_id' => $template->id])
            ->assertRedirect(route('onboarding.theme'));

        $this->post(route('logout'));

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'Passw0rd!',
        ])->assertRedirect(route('onboarding.index'));

        $this->get(route('onboarding.index'))
            ->assertRedirect(route('onboarding.theme'));

        $agency->refresh();
        $this->assertSame('Updated Travel', $agency->name);
        $this->assertSame($template->id, $agency->onboarding_data['template_id']);
    }

    public function test_dismiss_disables_future_automatic_redirects(): void
    {
        [$agency, $user] = $this->adminAgency(autoStart: true);

        $this->actingAs($user)
            ->post(route('onboarding.dismiss'))
            ->assertRedirect(route('dashboard'));

        $this->post(route('logout'));

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'Passw0rd!',
        ])->assertRedirect(route('dashboard'));

        $this->assertFalse($agency->fresh()->onboarding_auto_start);
    }

    public function test_existing_agency_is_not_redirected_automatically(): void
    {
        [, $user] = $this->adminAgency(autoStart: false);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'Passw0rd!',
        ])->assertRedirect(route('dashboard'));
    }

    public function test_non_admin_cannot_access_onboarding(): void
    {
        $agency = $this->agency('Editor Agency', true);
        $role = Role::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'name' => 'Editor',
            'slug' => 'editor',
        ]);
        $user = $this->user($agency, $role);

        $this->actingAs($user)
            ->get(route('onboarding.profile'))
            ->assertForbidden();
    }

    public function test_template_theme_is_preselected_and_can_be_changed(): void
    {
        [$agency, $user] = $this->adminAgency();
        $recommended = $this->theme('Recommended');
        $alternative = $this->theme('Alternative');
        $template = $this->siteTemplate($recommended);

        $this->actingAs($user)
            ->post(route('onboarding.template.store'), ['template_id' => $template->id])
            ->assertRedirect(route('onboarding.theme'));

        $this->assertSame($recommended->id, $agency->fresh()->onboarding_data['theme_id']);

        $this->actingAs($user)
            ->post(route('onboarding.theme.store'), ['theme_id' => $alternative->id])
            ->assertRedirect(route('onboarding.review'));

        $this->assertSame($alternative->id, $agency->fresh()->onboarding_data['theme_id']);
    }

    public function test_existing_site_requires_confirmation_then_archives_and_replaces(): void
    {
        [$agency, $user] = $this->adminAgency();
        $theme = $this->theme('Ocean');
        $template = $this->siteTemplate($theme);
        $oldPage = $this->page($agency, 'Old Home', 'old-home');

        $this->setSelections($agency, $template, $theme);

        $this->actingAs($user)
            ->post(route('onboarding.complete'))
            ->assertSessionHasErrors('confirm_replace');

        $this->actingAs($user)
            ->post(route('onboarding.complete'), ['confirm_replace' => '1'])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('onboarding_completed');

        $this->assertDatabaseMissing('pages', ['id' => $oldPage->id]);
        $this->assertDatabaseHas('pages', ['agency_id' => $agency->id, 'slug' => 'home']);
        $this->assertSame(1, SiteArchive::where('agency_id', $agency->id)->count());
        $this->assertSame(Agency::ONBOARDING_COMPLETED, $agency->fresh()->onboarding_status);
    }

    public function test_active_template_is_not_reapplied_during_completion(): void
    {
        [$agency, $user] = $this->adminAgency();
        $originalTheme = $this->theme('Original');
        $selectedTheme = $this->theme('Selected');
        $template = $this->siteTemplate($originalTheme);
        $page = $this->page($agency, 'Customized Home', 'home');

        $agency->forceFill([
            'active_site_template_id' => $template->id,
            'theme_id' => $originalTheme->id,
            'template_applied_at' => now(),
        ])->save();
        $this->setSelections($agency, $template, $selectedTheme);

        $this->actingAs($user)
            ->post(route('onboarding.complete'))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('pages', ['id' => $page->id, 'title' => 'Customized Home']);
        $this->assertSame(1, Page::withoutGlobalScopes()->where('agency_id', $agency->id)->count());
        $this->assertSame(0, SiteArchive::where('agency_id', $agency->id)->count());
        $this->assertSame($selectedTheme->id, $agency->fresh()->theme_id);
    }

    public function test_completed_onboarding_hides_dashboard_cta(): void
    {
        [$agency, $user] = $this->adminAgency();
        $agency->forceFill([
            'onboarding_status' => Agency::ONBOARDING_COMPLETED,
            'onboarding_auto_start' => false,
            'onboarding_completed_at' => now(),
        ])->save();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Complete the agency workspace');
    }

    private function adminAgency(bool $autoStart = false): array
    {
        $agency = $this->agency('Agency '.Str::random(5), $autoStart);
        $role = Role::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'name' => 'Admin',
            'slug' => 'admin',
        ]);

        return [$agency, $this->user($agency, $role)];
    }

    private function agency(string $name, bool $autoStart = false): Agency
    {
        return Agency::create([
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(5),
            'email' => Str::random(8).'@agency.test',
            'onboarding_status' => Agency::ONBOARDING_PENDING,
            'onboarding_step' => Agency::ONBOARDING_STEP_PROFILE,
            'onboarding_auto_start' => $autoStart,
        ]);
    }

    private function user(Agency $agency, Role $role): User
    {
        $user = User::withoutGlobalScopes()->create([
            'name' => 'Agency User',
            'email' => Str::lower(Str::random(8)).'@user.test',
            'password' => 'Passw0rd!',
            'agency_id' => $agency->id,
            'role_id' => $role->id,
        ]);

        $user->markEmailAsVerified();

        return $user;
    }

    private function theme(string $name): Theme
    {
        return Theme::create([
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(5),
            'variables' => ['primary' => '#2563eb', 'secondary' => '#0f172a'],
            'status' => Theme::STATUS_ACTIVE,
        ]);
    }

    private function siteTemplate(Theme $theme): SiteTemplate
    {
        return SiteTemplate::create([
            'name' => 'Starter '.Str::random(4),
            'slug' => 'starter-'.Str::random(6),
            'description' => 'Complete travel website.',
            'theme_id' => $theme->id,
            'status' => SiteTemplate::STATUS_ACTIVE,
            'pages' => [
                [
                    'title' => 'Home',
                    'slug' => 'home',
                    'status' => Page::STATUS_DRAFT,
                    'include_in_menu' => true,
                    'structure' => [$this->node('section', [], [$this->node('container')])],
                    'meta' => ['description' => 'Home'],
                ],
                [
                    'title' => 'Contact',
                    'slug' => 'contact',
                    'status' => Page::STATUS_DRAFT,
                    'include_in_menu' => true,
                    'structure' => [$this->node('section')],
                    'meta' => ['description' => 'Contact'],
                ],
            ],
        ]);
    }

    private function page(Agency $agency, string $title, string $slug): Page
    {
        return Page::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'title' => $title,
            'slug' => $slug,
            'structure' => [$this->node('section')],
            'status' => Page::STATUS_DRAFT,
        ]);
    }

    private function setSelections(Agency $agency, SiteTemplate $template, Theme $theme): void
    {
        $agency->forceFill([
            'onboarding_status' => Agency::ONBOARDING_IN_PROGRESS,
            'onboarding_step' => Agency::ONBOARDING_STEP_REVIEW,
            'onboarding_data' => [
                'template_id' => $template->id,
                'theme_id' => $theme->id,
            ],
        ])->save();
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
