<?php

namespace App\Models;

use App\Scopes\AgencyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Menu extends Model
{
    protected $fillable = [
        'agency_id',
        'name',
        'slug',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->slug) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    protected static function booted()
    {
        static::addGlobalScope(new AgencyScope);
    }

    public function items()
    {
        return $this->hasMany(MenuItem::class)->orderBy('order');
    }
}
