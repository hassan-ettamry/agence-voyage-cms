<?php

namespace App\Models;

// Importation des classes nécessaires
use App\Scopes\AgencyScope;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmailContract
{
    /**
     * Traits Laravel utilisés pour les fonctionnalités supplémentaires
     * - HasApiTokens : gestion des tokens API (Sanctum)
     * - HasFactory : support des factories pour les tests
     * - Notifiable : gestion des notifications
     */
    use HasApiTokens, HasFactory, MustVerifyEmail, Notifiable;

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
        'display_name',
        'email',
        'phone',
        'bio',
        'password',
        'agency_id',
        'role_id',
        'avatar_path',
        'language',
        'timezone',
        'date_format',
        'time_format',
        'profile_preferences',
        'last_login_at',
        'password_changed_at',
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
        'profile_preferences' => 'array',
        'last_login_at' => 'datetime',
        'password_changed_at' => 'datetime',
    ];

    protected $appends = [
        'avatar_url',
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
            if (! $model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected static function booted()
    {
        static::addGlobalScope(new AgencyScope);
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
    public function isAdmin(): bool
    {
        $role = $this->relationLoaded('role')
            ? $this->getRelation('role')
            : $this->role()->withoutGlobalScopes()->first();

        return $role?->agency_id === $this->agency_id
            && $role->slug === 'admin';
    }

    /**
     * Relation : un utilisateur appartient à un rôle
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar_path ? Storage::disk('public')->url($this->avatar_path) : null;
    }

    public function displayName(): string
    {
        return $this->display_name ?: $this->name;
    }

    /**
     * Vérifier si l'utilisateur possède une permission donnée
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (! $this->role) {
            return false;
        }

        return in_array($permission, $this->getPermissions());
    }

    public function isOnlyAgencyAdmin(): bool
    {
        if (! $this->isAdmin()) {
            return false;
        }

        return static::withoutGlobalScopes()
            ->where('agency_id', $this->agency_id)
            ->whereHas('role', function ($query) {
                $query->withoutGlobalScopes()
                    ->where('agency_id', $this->agency_id)
                    ->where('slug', 'admin');
            })
            ->count() === 1;
    }

    /**
     * Récupérer la liste des permissions de l'utilisateur
     *
     * Utilise un cache pour améliorer les performances
     * et éviter les requêtes répétées
     */
    public function getPermissions(): array
    {
        // Charger la relation si elle n'est pas déjà chargée
        $this->loadMissing('role.permissions');

        return \Cache::remember(
            "user_permissions_v1_{$this->id}",
            3600,
            fn () => $this->role?->permissions->pluck('slug')->toArray() ?? []
        );
    }
}
