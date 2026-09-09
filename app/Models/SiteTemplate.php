<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SiteTemplate extends Model
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'preview',
        'theme_id',
        'pages',
        'status',
    ];

    protected $casts = [
        'pages' => 'array',
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

    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

    public function archives()
    {
        return $this->hasMany(SiteArchive::class, 'source_template_id');
    }
}
