<?php

namespace App\Services;

use App\Models\User;
use App\Models\Agency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthService
{
    /**
     * Enregistrer une nouvelle agence + utilisateur admin
     */
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {

            // Création de l'agence
            $agency = Agency::create([
                'name' => $data['agency_name'],
                'email' => $data['email'],
                'slug' => Str::slug($data['agency_name']),
                'status' => 'active',
                'plan' => 'free',
            ]);

            // Création de l'utilisateur admin
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'], // hash déjà géré dans le model
                'agency_id' => $agency->id,
                'role' => 'admin',
            ]);

            return $user;
        });
    }
}