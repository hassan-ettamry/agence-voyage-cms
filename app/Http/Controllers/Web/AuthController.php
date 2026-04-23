<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
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
     * Traiter la connexion avec FormRequest
     */
    public function login(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Traiter l'inscription avec FormRequest
     */
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        // Création de l'agence
        $agency = Agency::create([
            'name' => $validated['agency_name'],
            'email' => $validated['email'],
            'slug' => \Illuminate\Support\Str::slug($validated['agency_name']),
            'status' => 'active',
            'plan' => 'free',
        ]);

        // Création de l'utilisateur admin
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'agency_id' => $agency->id,
            'role' => 'admin',
        ]);

        // Connecter l'utilisateur
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Bienvenue ! Votre agence a été créée.');
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