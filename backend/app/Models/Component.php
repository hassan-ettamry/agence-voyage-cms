<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'type',
        'name',
        'category',
        'schema_json',
        'preview_image'
    ];

    protected $casts = [
        'schema_json' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) \Str::uuid();
            }
        });
    }
}