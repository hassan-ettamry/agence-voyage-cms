<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Theme extends Model
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'slug',
        'preview',
        'variables',
        'status',
    ];

    protected $casts = [
        'variables' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->id) {
                $model->id = (string) Str::uuid();
            }

            if (! $model->slug) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function siteTemplates()
    {
        return $this->hasMany(SiteTemplate::class);
    }

    public function agencies()
    {
        return $this->hasMany(Agency::class);
    }

    public function cssVariables(): string
    {
        return collect($this->variables ?? [])
            ->filter(fn ($value, $key) => is_string($key) && (is_string($value) || is_numeric($value)))
            ->map(fn ($value, $key) => '--site-'.Str::kebab($key).': '.$value.';')
            ->implode("\n");
    }
}
