<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Offer extends Model
{
    // UUID au lieu d’auto-increment
    public $incrementing = false;

    // Clé primaire en string
    protected $keyType = 'string';

    // Champs autorisés
    protected $fillable = [
        'agency_id',       // multi-tenant
        'destination_id',  // relation vers destination
        'title',           // titre de l’offre
        'description',     // description
        'price',           // prix (decimal)
        'duration_days',   // durée en jours
        'is_special'       // offre spéciale (promo)
    ];

    // Cast automatique
    protected $casts = [
        'price' => 'decimal:2', 
        'is_special' => 'boolean',
    ];

    /**
     * Boot : avant création
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            // Sécurité : agency obligatoire
            if (!$model->agency_id) {
                throw new \InvalidArgumentException('Agency ID is required');
            }

            // UUID
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Global Scope multi-tenant
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
     * Relation : offre → agence
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Relation : offre → destination
     */
    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    /**
     * Scope : offres spéciales
     */
    public function scopeSpecial($query)
    {
        return $query->where('is_special', true);
    }

    /**
     * Scope : filtrer par agence
     */
    public function scopeForAgency($query, $agencyId)
    {
        return $query->where('agency_id', $agencyId);
    }

    /**
     * Helper : vérifier si spécial
     */
    public function isSpecial(): bool
    {
        return $this->is_special;
    }
}