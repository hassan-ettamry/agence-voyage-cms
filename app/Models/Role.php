<?php

namespace App\Models;

use App\Support\AgencyContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class Role extends Model
{
    /**
     * Désactiver l'auto-incrément (UUID utilisé)
     */
    public $incrementing = false;

    /**
     * Type de clé primaire
     */
    protected $keyType = 'string';

    /**
     * Champs assignables en masse
     */
    protected $fillable = ['agency_id', 'name', 'slug'];

    /**
     * Configuration du modèle au démarrage
     */
    protected static function booted()
    {
        /**
         * Lors de la création :
         * - Génération automatique d'un UUID
         * - Génération du slug si absent
         */
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();

            if (!$model->slug) {
                $model->slug = Str::slug($model->name);
            }
        });

        /**
         * Global scope multi-tenant :
         * Filtre automatiquement les rôles selon l'agence courante
         */
        static::addGlobalScope('agency', function ($q) {
            if (AgencyContext::has()) {
                $q->where('agency_id', AgencyContext::get());
            }
        });

        /**
         * Après sauvegarde :
         * Invalidation du cache des permissions des utilisateurs liés
         */
        static::saved(fn($role) => $role->invalidateUserCache());
    }

    /**
     * Relation plusieurs à plusieurs avec les permissions
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Relation un rôle possède plusieurs utilisateurs
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Synchroniser les permissions du rôle
     *
     * @param array $permissionIds
     * @throws ValidationException
     */
    public function syncPermissions(array $permissionIds): void
    {
        DB::transaction(function () use ($permissionIds) {

            /**
             * Vérifier que toutes les permissions existent
             */
            $valid = Permission::whereIn('id', $permissionIds)->pluck('id')->all();

            if (count($valid) !== count($permissionIds)) {
                throw ValidationException::withMessages([
                    'permissions' => 'Invalid permissions'
                ]);
            }

            /**
             * Synchronisation des permissions
             */
            $this->permissions()->sync($valid);

            /**
             * Invalidation du cache des utilisateurs concernés
             */
            $this->invalidateUserCache();
        });
    }

    /**
     * Invalider le cache des permissions des utilisateurs liés au rôle
     *
     * Utilisé après modification des permissions ou du rôle
     */
    public function invalidateUserCache(): void
    {
        $this->users()
            ->select('id')
            ->chunk(100, function ($users) {

                foreach ($users as $user) {
                    Cache::forget("user_permissions_v1_{$user->id}");
                }

            });
    }
}