<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Destination extends Model
{
    // UUID au lieu d’auto-increment
    public $incrementing = false;

    // Type de la clé primaire
    protected $keyType = 'string';

    // Champs autorisés en mass assignment
    protected $fillable = [
        'agency_id',    // lien multi-tenant
        'name',         // nom de la destination
        'country',      // pays
        'description',  // description texte
        'images',       // tableau d’images (JSON)
        'is_featured'   // mise en avant (true/false)
    ];

    // Cast automatique
    protected $casts = [
        'images' => 'array',     // JSON → array
        'is_featured' => 'boolean', // booléen propre
    ];

    /**
     * Boot principal
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            // 🔒 Sécurité multi-tenant
            if (!$model->agency_id) {
                throw new \InvalidArgumentException('Agency ID is required');
            }

            // Génération UUID
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Global Scope (multi-tenant sécurisé)
     */
    protected static function booted()
    {
        static::addGlobalScope('agency', function ($query) {

            if (app()->bound('auth') && auth()->hasUser()) {

                $agencyId = auth()->user()->agency_id;

                if ($agencyId) {
                    $query->where('agency_id', $agencyId);
                }
            }
        });
    }

    /**
     * Relation : destination → agence
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Relation : destination → offres
     */
    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    /**
     * Scope : destinations mises en avant
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope : filtrer par agence
     */
    public function scopeForAgency($query, $agencyId)
    {
        return $query->where('agency_id', $agencyId);
    }
}