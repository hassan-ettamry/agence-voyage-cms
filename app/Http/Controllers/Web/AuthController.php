<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Afficher formulaire de connexion
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Afficher formulaire d'inscription
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Traiter la connexion
     */
    public function login(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();
        $request->user()->forceFill(['last_login_at' => now()])->save();

        if (! $request->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if ($request->user()->agency?->shouldAutoStartOnboarding()) {
            return redirect()->route('onboarding.index');
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Inscription via service
     */
    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register($request->validated());
        Auth::login($user);
        $request->session()->regenerate();
        $user->sendEmailVerificationNotification();

        return redirect()
            ->route('verification.notice')
            ->with('success', 'Votre agence a été créée. Vérifiez maintenant votre adresse email.');
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
