<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_registration_requires_email_verification_before_admin_access(): void
    {
        Notification::fake();
        $this->seed(PermissionSeeder::class);

        $this->post(route('register'), [
            'agency_name' => 'Verify Travel',
            'name' => 'Verify Admin',
            'email' => 'verify@example.test',
            'password' => 'Passw0rd!',
            'password_confirmation' => 'Passw0rd!',
        ])->assertRedirect(route('verification.notice'));

        $user = User::withoutGlobalScopes()->where('email', 'verify@example.test')->firstOrFail();

        $this->assertFalse($user->hasVerifiedEmail());
        Notification::assertSentTo($user, VerifyEmail::class);

        $this->get(route('dashboard'))->assertRedirect(route('verification.notice'));
        $this->get(route('onboarding.profile'))->assertRedirect(route('verification.notice'));
    }

    public function test_valid_signed_link_verifies_user_and_starts_onboarding(): void
    {
        $user = $this->user(verified: false, autoStart: true);
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $this->actingAs($user)
            ->get($url)
            ->assertRedirect(route('onboarding.profile'));

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_invalid_verification_signature_is_rejected(): void
    {
        $user = $this->user(verified: false, autoStart: true);

        $this->actingAs($user)
            ->get(route('verification.verify', [
                'id' => $user->id,
                'hash' => sha1($user->getEmailForVerification()),
            ]))
            ->assertForbidden();

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_verification_resend_is_rate_limited(): void
    {
        Notification::fake();
        $user = $this->user(verified: false, autoStart: true);

        for ($attempt = 1; $attempt <= 6; $attempt++) {
            $this->actingAs($user)
                ->post(route('verification.send'))
                ->assertRedirect();
        }

        $this->actingAs($user)
            ->post(route('verification.send'))
            ->assertTooManyRequests();
    }

    private function user(bool $verified, bool $autoStart): User
    {
        $agency = Agency::create([
            'name' => 'Agency '.uniqid(),
            'slug' => 'agency-'.uniqid(),
            'email' => uniqid().'@agency.test',
            'onboarding_status' => Agency::ONBOARDING_PENDING,
            'onboarding_step' => Agency::ONBOARDING_STEP_PROFILE,
            'onboarding_auto_start' => $autoStart,
        ]);
        $role = Role::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'name' => 'Admin',
            'slug' => 'admin',
        ]);

        $user = User::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'role_id' => $role->id,
            'name' => 'Verify User',
            'email' => uniqid().'@user.test',
            'password' => 'Passw0rd!',
        ]);

        if ($verified) {
            $user->markEmailAsVerified();
        }

        return $user;
    }
}
