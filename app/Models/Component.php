<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Component extends Model
{
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
        'type',
        'name',
        'category',
        'schema_json',
        'preview_image',
        'is_active'
    ];

    /**
     * Cast automatique des attributs
     */
    protected $casts = [
        'schema_json' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Boot du modèle : gestion automatique des valeurs par défaut
     */
    protected static function booted()
    {
        static::creating(function ($model) {

            // Générer un UUID si non fourni
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }

            // Activer le composant par défaut
            if (is_null($model->is_active)) {
                $model->is_active = true;
            }
        });
    }
}