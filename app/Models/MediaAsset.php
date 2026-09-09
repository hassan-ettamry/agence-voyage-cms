<?php

namespace App\Models;

use App\Scopes\AgencyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaAsset extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'agency_id',
        'filename',
        'original_name',
        'path',
        'mime_type',
        'size',
        'title',
        'alt_text',
        'copyright_holder',
        'license',
        'source_url',
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

    public function destinations()
    {
        return $this->belongsToMany(Destination::class, 'destination_media');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    public function isUsed(): bool
    {
        return $this->destinations()->exists() || $this->offers()->exists();
    }
}
