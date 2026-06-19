<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Permission extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['name', 'slug'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function getModuleKeyAttribute(): string
    {
        return Str::before($this->slug, '.');
    }

    public function getModuleLabelAttribute(): string
    {
        return Str::headline($this->module_key);
    }

    public function getActionKeyAttribute(): string
    {
        return Str::afterLast($this->slug, '.');
    }

    public function getActionLabelAttribute(): string
    {
        return Str::headline($this->action_key);
    }
}
