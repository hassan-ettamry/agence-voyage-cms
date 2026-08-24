<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Role;
use App\Models\User;
use App\Services\AccountSessionService;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AccountSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_change_requires_current_password(): void
    {
        config(['session.driver' => 'file']);
        $user = $this->user();
        $this->actingAs($user);

        $this->put(route('account.security.password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'NewStrongPass1!',
            'password_confirmation' => 'NewStrongPass1!',
        ])->assertSessionHasErrors('current_password');

        $this->put(route('account.security.password.update'), [
            'current_password' => 'OldStrongPass1!',
            'password' => 'NewStrongPass1!',
            'password_confirmation' => 'NewStrongPass1!',
        ])->assertRedirect(route('account.settings.edit'));

        $user->refresh();
        $this->assertTrue(Hash::check('NewStrongPass1!', $user->password));
        $this->assertNotNull($user->password_changed_at);
    }

    public function test_session_service_revokes_others_only_when_current_session_belongs_to_user(): void
    {
        config(['session.driver' => 'database']);
        $user = $this->user();
        $otherUser = $this->user('session-owner@example.test');
        $this->insertSession('current-session', $user, 'Current Browser');
        $this->insertSession('other-session', $user, 'Other Browser');
        $this->insertSession('foreign-session', $otherUser, 'Foreign Browser');

        $service = app(AccountSessionService::class);
        $this->assertSame(0, $service->revokeOthers($user, 'missing-current-session'));
        $this->assertSame(1, $service->revokeOthers($user, 'current-session'));

        $this->assertDatabaseHas('sessions', ['id' => 'current-session']);
        $this->assertDatabaseMissing('sessions', ['id' => 'other-session']);
        $this->assertDatabaseHas('sessions', ['id' => 'foreign-session']);
    }

    public function test_email_change_requires_current_password_and_new_verification(): void
    {
        Notification::fake();
        $user = $this->user();

        $this->actingAs($user)
            ->put(route('account.security.email.update'), [
                'email' => 'new-email@example.test',
                'current_password' => 'wrong-password',
            ])
            ->assertSessionHasErrors('current_password');

        $this->actingAs($user)
            ->put(route('account.security.email.update'), [
                'email' => 'NEW-EMAIL@example.test',
                'current_password' => 'OldStrongPass1!',
            ])
            ->assertRedirect(route('verification.notice'));

        $user->refresh();
        $this->assertSame('new-email@example.test', $user->email);
        $this->assertFalse($user->hasVerifiedEmail());
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_profile_update_cannot_bypass_secure_email_change(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->put(route('account.profile.update'), [
                'name' => 'Updated Name',
                'email' => 'bypass@example.test',
            ])
            ->assertRedirect();

        $user->refresh();
        $this->assertSame('Updated Name', $user->name);
        $this->assertNotSame('bypass@example.test', $user->email);
    }

    public function test_user_can_review_and_revoke_only_their_own_sessions(): void
    {
        config(['session.driver' => 'database']);
        $user = $this->user();
        $otherUser = $this->user('other@example.test');
        $this->actingAs($user);
        $currentId = session()->getId();
        $this->insertSession($currentId, $user, 'Current Browser');
        $this->insertSession('user-other-session', $user, 'Tablet Browser');
        $this->insertSession('foreign-session', $otherUser, 'Foreign Browser');

        $this->get(route('account.security.sessions.index'))
            ->assertOk()
            ->assertSee('Current Browser')
            ->assertSee('Tablet Browser')
            ->assertDontSee('Foreign Browser');

        $fingerprint = app(AccountSessionService::class)->fingerprint('user-other-session');

        $this->delete(route('account.security.sessions.destroy', $fingerprint))
            ->assertRedirect();

        $this->assertDatabaseMissing('sessions', ['id' => 'user-other-session']);
        $this->assertDatabaseHas('sessions', ['id' => 'foreign-session']);
        $this->assertDatabaseHas('sessions', ['id' => $currentId]);
    }

    public function test_file_session_driver_shows_current_session_without_fake_management(): void
    {
        config(['session.driver' => 'file']);
        $user = $this->user();

        $this->actingAs($user)
            ->get(route('account.security.sessions.index'))
            ->assertOk()
            ->assertSee('SESSION_DRIVER=database')
            ->assertSee('Current');
    }

    private function user(?string $email = null): User
    {
        $agency = Agency::create([
            'name' => 'Account Agency '.uniqid(),
            'slug' => 'account-agency-'.uniqid(),
            'email' => uniqid().'@agency.test',
        ]);
        $role = Role::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'name' => 'Admin',
            'slug' => 'admin',
        ]);
        $user = User::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'role_id' => $role->id,
            'name' => 'Account User',
            'email' => $email ?? uniqid().'@user.test',
            'password' => 'OldStrongPass1!',
        ]);
        $user->markEmailAsVerified();

        return $user;
    }

    private function insertSession(string $id, User $user, string $agent): void
    {
        DB::table('sessions')->updateOrInsert(['id' => $id], [
            'id' => $id,
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => $agent,
            'payload' => '',
            'last_activity' => now()->timestamp,
        ]);
    }
}
