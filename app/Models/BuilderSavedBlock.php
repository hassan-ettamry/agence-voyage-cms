<?php

namespace App\Models;

use App\Scopes\AgencyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BuilderSavedBlock extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['agency_id', 'created_by', 'name', 'category', 'structure'];

    protected $casts = ['structure' => 'array'];

    protected static function booted(): void
    {
        static::addGlobalScope(new AgencyScope);
        static::creating(function (BuilderSavedBlock $block) {
            $block->id ??= (string) Str::uuid();
        });
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
