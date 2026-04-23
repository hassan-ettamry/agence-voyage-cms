<?php

namespace App\Models;

// Importation des classes nécessaires
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    // Traits Laravel utilisés pour les fonctionnalités supplémentaires
    use HasApiTokens, HasFactory, Notifiable;

    // Désactive l'auto-incrémentation de l'ID (car on utilise UUID)
    public $incrementing = false;

    // Définit le type de clé primaire comme string
    protected $keyType = 'string';

    // Champs autorisés pour le mass assignment
    protected $fillable = [
        'name',
        'email',
        'password',
        'agency_id',
        'role',
    ];

    // Champs cachés lors de la sérialisation (ex: API)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Cast des attributs (conversion automatique)
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Méthode boot : exécutée automatiquement au démarrage du modèle
     */
    protected static function boot()
    {
        parent::boot();

        // Événement déclenché lors de la création d'un utilisateur
        static::creating(function ($model) {

            // Si aucun ID n'est défini, on génère un UUID
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Mutateur pour le mot de passe
     * Hash automatiquement le mot de passe avant de le sauvegarder
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Relation avec le modèle Agency
     * Un utilisateur appartient à une agence
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Vérifie si l'utilisateur est un admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}