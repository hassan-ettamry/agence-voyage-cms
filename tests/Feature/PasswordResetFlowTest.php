<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_reset_request_does_not_reveal_whether_account_exists(): void
    {
        Notification::fake();
        $user = $this->user('known@example.test');

        $known = $this->post(route('password.email'), ['email' => $user->email]);
        $unknown = $this->post(route('password.email'), ['email' => 'missing@example.test']);

        $known->assertSessionHas('status');
        $unknown->assertSessionHas('status', session('status'));
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_user_can_reset_password_once_with_a_valid_token(): void
    {
        Notification::fake();
        $user = $this->user('reset@example.test');
        $token = null;

        $this->post(route('password.email'), ['email' => $user->email]);

        Notification::assertSentTo(
            $user,
            ResetPasswordNotification::class,
            function (ResetPasswordNotification $notification) use (&$token) {
                $token = $notification->token;

                return true;
            }
        );

        $payload = [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewStrongPass1!',
            'password_confirmation' => 'NewStrongPass1!',
        ];

        $this->post(route('password.update'), $payload)
            ->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $user->refresh();
        $this->assertTrue(Hash::check('NewStrongPass1!', $user->password));
        $this->assertNotNull($user->password_changed_at);

        $this->post(route('password.update'), $payload)
            ->assertSessionHasErrors('email');
    }

    public function test_invalid_reset_token_is_rejected(): void
    {
        $user = $this->user('invalid-token@example.test');

        $this->post(route('password.update'), [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'NewStrongPass1!',
            'password_confirmation' => 'NewStrongPass1!',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('OldStrongPass1!', $user->fresh()->password));
    }

    public function test_authentication_endpoints_are_rate_limited(): void
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->post(route('login'), [
                'email' => 'limited-login@example.test',
                'password' => 'wrong-password',
            ])->assertRedirect();
        }

        $this->post(route('login'), [
            'email' => 'limited-login@example.test',
            'password' => 'wrong-password',
        ])->assertTooManyRequests();

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $this->post(route('register'), [])->assertSessionHasErrors();
        }

        $this->post(route('register'), [])->assertTooManyRequests();

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $this->post(route('password.email'), [
                'email' => 'limited-reset@example.test',
            ])->assertRedirect();
        }

        $this->post(route('password.email'), [
            'email' => 'limited-reset@example.test',
        ])->assertTooManyRequests();
    }

    private function user(string $email): User
    {
        return User::withoutGlobalScopes()->create([
            'name' => 'Security User',
            'email' => $email,
            'password' => 'OldStrongPass1!',
        ]);
    }
}
