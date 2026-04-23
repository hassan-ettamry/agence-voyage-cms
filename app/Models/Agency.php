<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Agency extends Model
{
    // Désactive l'auto-incrémentation (on utilise UUID)
    public $incrementing = false;

    // Type de la clé primaire (string au lieu d'int)
    protected $keyType = 'string';

    // Champs autorisés pour le remplissage en masse (mass assignment)
    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'logo',
        'settings',
        'plan',
        'status',
    ];

    // Cast automatique des champs
    protected $casts = [
        'settings' => 'array', // Convertit JSON ↔ array automatiquement
    ];

    /**
     * Méthode boot : appelée au démarrage du modèle
     */
    protected static function boot()
    {
        parent::boot();

        // Événement exécuté lors de la création d'une agence
        static::creating(function ($model) {

            // Génération automatique d'un UUID pour l'id
            $model->id = (string) Str::uuid();

            // Génération automatique du slug si non fourni
            if (!$model->slug) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    /**
     * Relation : une agence possède plusieurs utilisateurs
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}