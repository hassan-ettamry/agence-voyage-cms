<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_id', 'title', 'page_id', 'url', 'parent_id', 'order'
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
