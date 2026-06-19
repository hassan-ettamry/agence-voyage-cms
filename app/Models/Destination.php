<?php

namespace App\Models;

use App\Scopes\AgencyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Destination extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'agency_id',
        'name',
        'slug',
        'country',
        'description',
        'images',
        'is_featured',
        'status',
    ];

    protected $casts = [
        'images' => 'array',
        'is_featured' => 'boolean',
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
                $model->slug = static::uniqueSlug($model->name, $model->agency_id);
            }

            if (! $model->status) {
                $model->status = self::STATUS_DRAFT;
            }
        });
    }

    protected static function booted()
    {
        static::addGlobalScope(new AgencyScope);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function media()
    {
        return $this->belongsToMany(MediaAsset::class, 'destination_media')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderBy('destination_media.sort_order');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeForAgency($query, $agencyId)
    {
        return $query->where('agency_id', $agencyId);
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public static function uniqueSlug(string $name, string $agencyId, ?string $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'destination';
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
