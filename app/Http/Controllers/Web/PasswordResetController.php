<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use App\Services\SecurityEventLogger;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    private const LINK_STATUS = 'Si un compte correspond à cette adresse, un lien de réinitialisation a été envoyé.';

    public function __construct(private SecurityEventLogger $securityLogger) {}

    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(ForgotPasswordRequest $request)
    {
        Password::sendResetLink($request->only('email'));
        $this->securityLogger->record('auth.password_reset_requested', null, $request, [
            'email_hash' => $this->securityLogger->emailFingerprint($request->input('email')),
        ]);

        return back()->with('status', self::LINK_STATUS);
    }

    public function edit(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function update(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) use ($request) {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                    'password_changed_at' => now(),
                ])->save();

                event(new PasswordReset($user));
                $this->securityLogger->record('auth.password_reset_completed', $user, $request);
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => trans($status)]);
        }

        return redirect()
            ->route('login')
            ->with('status', 'Votre mot de passe a été réinitialisé. Vous pouvez maintenant vous connecter.');
    }
}
