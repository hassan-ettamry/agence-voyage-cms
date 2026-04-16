<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Inscription (Register)
     * Crée une agence + un utilisateur admin
     */
    public function register(Request $request)
    {
        // Validation des données entrantes
        $request->validate([
            'agency_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        // Création de l'agence
        $agency = Agency::create([
            'name' => $request->agency_name,
            'email' => $request->email,
        ]);

        // Création de l'utilisateur (admin de l'agence)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // sera hashé automatiquement via le mutator
            'agency_id' => $agency->id,
            'role' => 'admin',
        ]);

        // Génération d’un token API avec Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        // Réponse JSON
        return response()->json([
            'user' => $user,
            'agency' => $agency,
            'token' => $token,
        ]);
    }

    /**
     * Connexion (Login)
     */
    public function login(Request $request)
    {
        // Validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Recherche de l'utilisateur par email
        $user = User::where('email', $request->email)->first();

        // Vérification des identifiants
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Création du token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Retour utilisateur + agence liée
        return response()->json([
            'user' => $user->load('agency'),
            'token' => $token,
        ]);
    }

    /**
     * Récupérer l'utilisateur connecté
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('agency'),
        ]);
    }

    /**
     * Déconnexion (Logout)
     * Supprime tous les tokens de l'utilisateur
     */
    public function logout(Request $request)
    {
        // Supprime tous les tokens (logout sur tous les appareils)
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out'
        ]);
    }
}