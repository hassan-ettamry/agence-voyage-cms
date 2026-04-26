<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\PageVersion;
use App\Scopes\AgencyScope;

class Page extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'agency_id',
        'title',
        'slug',
        'structure',
        'meta',
        'status',
        'published_at',
    ];

    protected $casts = [
        'structure' => 'array',
        'meta' => 'array',
        'published_at' => 'datetime',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';

    /**
     * Boot principal
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            if (!$model->agency_id) {
                throw new \InvalidArgumentException('Agency ID is required');
            }

            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }

            if (!$model->status) {
                $model->status = self::STATUS_DRAFT;
            }

            if ($model->status === self::STATUS_PUBLISHED && !$model->published_at) {
                $model->published_at = now();
            }

            // slug unique par agency (bypass global scope)
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
     *  Global Scope
     */
    protected static function booted()
    {
        static::addGlobalScope(new AgencyScope);
    }

    /**
     * Relations
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function versions()
    {
        return $this->hasMany(PageVersion::class);
    }

    public function latestVersion()
    {
        return $this->hasOne(PageVersion::class)->ofMany('version', 'max');
    }

    /**
     * Scopes
     */
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeForAgency($query, $agencyId)
    {
        return $query->where('agency_id', $agencyId);
    }

    /**
     * Helper
     */
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }
}