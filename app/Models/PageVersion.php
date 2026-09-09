<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PageVersion extends Model
{
    protected $fillable = [
        'page_id',
        'structure',
        'meta',
        'version',
        'created_by',
    ];

    protected $casts = [
        'structure' => 'array',
        'meta' => 'array',
    ];

    /**
     * Page relation
     */
    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Author relation
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}