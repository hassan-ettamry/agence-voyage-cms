<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SiteArchive extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'agency_id',
        'name',
        'snapshot',
        'source_template_id',
        'created_by',
    ];

    protected $casts = [
        'snapshot' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function sourceTemplate()
    {
        return $this->belongsTo(SiteTemplate::class, 'source_template_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
