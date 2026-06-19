<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RegisterFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        // AuthService::register() attache les permissions globales au rôle admin
        // créé pour la nouvelle agence. Le seeder doit donc avoir tourné.
        $this->seed(PermissionSeeder::class);
    }

    public function test_registration_creates_user_with_admin_role_and_grants_app_access(): void
    {
        $payload = [
            'agency_name' => 'Acme Travel',
            'name' => 'Alice Admin',
            'email' => 'alice@acme.test',
            'password' => 'Passw0rd!',
            'password_confirmation' => 'Passw0rd!',
        ];

        // 1. Une nouvelle agence commence directement son onboarding guidé.
        $response = $this->post('/register', $payload);
        $response->assertRedirect(route('onboarding.profile'));

        // 2. L'utilisateur existe et est lié à un rôle (régression sur B1 :
        //    auparavant role_id restait null parce qu'AuthService passait
        //    une clé "role" inexistante).
        $user = User::where('email', 'alice@acme.test')->firstOrFail();
        $this->assertNotNull($user->role_id, 'User must be linked to a role after registration.');
        $this->assertNotNull($user->agency_id, 'User must be linked to an agency after registration.');

        // 3. Le rôle assigné est bien un rôle admin scopé à la nouvelle agence.
        $this->assertSame('admin', $user->role->slug);
        $this->assertSame($user->agency_id, $user->role->agency_id);
        $this->assertTrue($user->agency->onboarding_auto_start);
        $this->assertSame('pending', $user->agency->onboarding_status);

        // 4. Test du symptôme réel : l'utilisateur peut accéder à la zone
        //    protégée. /pages applique 'auth' + PagePolicy::viewAny, qui
        //    exigerait normalement la permission 'page.view'. Le passage est
        //    garanti soit par les permissions attachées au rôle admin, soit
        //    par le Gate::before admin-bypass — peu importe, ce que valide
        //    le test est qu'un nouveau compte ne reste pas verrouillé hors
        //    de son propre back-office.
        $this->actingAs($user)
            ->get(route('pages.index'))
            ->assertOk();
    }
}
