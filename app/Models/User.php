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
    /**
     * Traits Laravel utilisés pour les fonctionnalités supplémentaires
     * - HasApiTokens : gestion des tokens API (Sanctum)
     * - HasFactory : support des factories pour les tests
     * - Notifiable : gestion des notifications
     */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Désactiver l'auto-incrément (UUID utilisé comme clé primaire)
     */
    public $incrementing = false;

    /**
     * Type de la clé primaire
     */
    protected $keyType = 'string';

    /**
     * Champs assignables en masse
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'agency_id',
        'role_id',
    ];

    /**
     * Champs masqués lors de la sérialisation (API, JSON)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast automatique des attributs
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Configuration du modèle au démarrage
     */
    protected static function boot()
    {
        parent::boot();

        /**
         * Lors de la création :
         * Génération automatique d'un identifiant UUID si absent
         */
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Mutateur pour le mot de passe
     *
     * Hash automatiquement le mot de passe avant sauvegarde
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Relation : un utilisateur appartient à une agence
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Vérifier si l'utilisateur est administrateur
     */
    public function isAdmin()
    {
        return $this->role?->slug === 'admin';
    }

    /**
     * Relation : un utilisateur appartient à un rôle
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Vérifier si l'utilisateur possède une permission donnée
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->role) return false;

        return in_array($permission, $this->getPermissions());
    }

    /**
     * Récupérer la liste des permissions de l'utilisateur
     *
     * Utilise un cache pour améliorer les performances
     * et éviter les requêtes répétées
     *
     * @return array
     */
    public function getPermissions(): array
    {
        // Charger la relation si elle n'est pas déjà chargée
        $this->loadMissing('role.permissions');

        return \Cache::remember(
            "user_permissions_v1_{$this->id}",
            3600,
            fn() => $this->role?->permissions->pluck('slug')->toArray() ?? []
        );
    }
}