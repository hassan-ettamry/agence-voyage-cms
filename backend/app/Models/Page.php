<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\PageVersion;

class Page extends Model
{
    // UUID au lieu d'auto-increment
    public $incrementing = false;

    // Type de clé primaire
    protected $keyType = 'string';

    // Champs remplissables
    protected $fillable = [
        'agency_id',    // clé multi-tenant
        'title',        // titre de la page
        'slug',         // URL unique par agence
        'structure',    // JSON (page builder)
        'meta',         // JSON (SEO / metadata)
        'status',       // draft / published
        'published_at', // date de publication
    ];

    // Casts
    protected $casts = [
        'structure' => 'array',
        'meta' => 'array',
        'published_at' => 'datetime',
    ];

    // Constantes pour status
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';

    /**
     * Boot principal
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            // Sécurité : agency obligatoire
            if (!$model->agency_id) {
                throw new \InvalidArgumentException('Agency ID is required');
            }

            // Génération UUID
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }

            // Valeur par défaut
            if (!$model->status) {
                $model->status = self::STATUS_DRAFT;
            }

            // Définir published_at si publication directe
            if ($model->status === self::STATUS_PUBLISHED && !$model->published_at) {
                $model->published_at = now();
            }

            /**
             * Génération slug UNIQUE PAR AGENCY
             * + bypass du global scope
             */
            if (!$model->slug) {

                $slug = Str::slug($model->title);
                $original = $slug;
                $count = 1;

                while (
                    static::withoutGlobalScopes()
                        ->where('slug', $slug)
                        ->where('agency_id', $model->agency_id)
                        ->exists()
                ) {
                    $slug = $original . '-' . $count++;
                }

                $model->slug = $slug;
            }
        });

        // Gestion mise à jour (ex: publication après création)
        static::updating(function ($model) {
            if (
                $model->status === self::STATUS_PUBLISHED &&
                !$model->published_at
            ) {
                $model->published_at = now();
            }
        });
    }

    /**
     * Global Scope sécurisé (multi-tenant)
     */
    protected static function booted()
    {
        static::addGlobalScope('agency', function ($query) {

            // Vérifie que auth est disponible (évite crash en CLI / queue)
            if (app()->bound('auth') && auth()->hasUser()) {

                $agencyId = auth()->user()->agency_id;

                // Applique le filtre seulement si défini
                if ($agencyId) {
                    $query->where('agency_id', $agencyId);
                }
            }
        });
    }

    /**
     * Relation : page → agence
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Relation : page → versions
     */
    public function versions()
    {
        return $this->hasMany(PageVersion::class);
    }

    /**
     * Relation : dernière version basée sur le numéro de version
     */
    public function latestVersion()
    {
        return $this->hasOne(PageVersion::class)->ofMany('version', 'max');
    }

    /**
     * Scope : pages publiées
     */
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Scope : filtrer par agence
     */
    public function scopeForAgency($query, $agencyId)
    {
        return $query->where('agency_id', $agencyId);
    }

    /**
     * Helper : vérifier si publiée
     */
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }
}