<?php

namespace App\Models;

use App\Scopes\AgencyScope;
use App\Services\PublicContentCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Offer extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'agency_id',
        'destination_id',
        'media_asset_id',
        'title',
        'slug',
        'description',
        'summary',
        'price',
        'duration_days',
        'itinerary',
        'inclusions',
        'exclusions',
        'practical_information',
        'is_special',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'itinerary' => 'array',
        'inclusions' => 'array',
        'exclusions' => 'array',
        'is_special' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->agency_id) {
                throw new \InvalidArgumentException('Agency ID is required');
            }

            if (! $model->id) {
                $model->id = (string) Str::uuid();
            }

            if (! $model->slug) {
                $model->slug = static::uniqueSlug($model->title, $model->agency_id);
            }

            if (! $model->status) {
                $model->status = self::STATUS_DRAFT;
            }
        });
    }

    protected static function booted()
    {
        static::addGlobalScope(new AgencyScope);

        static::updating(fn (Offer $offer) => PublicContentCache::forgetOffer(
            $offer->agency_id,
            $offer->getRawOriginal('slug')
        ));

        static::saved(function (Offer $offer) {
            PublicContentCache::forgetOffer(
                $offer->agency_id,
                $offer->slug
            );
        });

        static::deleted(fn (Offer $offer) => PublicContentCache::forgetOffer(
            $offer->agency_id,
            $offer->slug
        ));
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function media()
    {
        return $this->belongsTo(MediaAsset::class, 'media_asset_id');
    }

    public function scopeSpecial($query)
    {
        return $query->where('is_special', true);
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeForAgency($query, $agencyId)
    {
        return $query->where('agency_id', $agencyId);
    }

    public function isSpecial(): bool
    {
        return (bool) $this->is_special;
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public static function uniqueSlug(string $title, string $agencyId, ?string $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'offer';
        $slug = $base;
        $count = 1;

        while (
            static::withoutGlobalScopes()
                ->where('agency_id', $agencyId)
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$count}";
            $count++;
        }

        return $slug;
    }
}
