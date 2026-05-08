<?php

namespace App\Services;

use App\Models\Menu;

class MenuService
{
    public function getMenu(string $name): array
    {
        $menu = Menu::with('items.page')->where('name', $name)->first();

        if (!$menu) return [];

        return $menu->items->map(function ($item) {
            return [
                'title' => $item->title,
                'url' => $item->page
                    ? route('pages.show', $item->page->slug)
                    : $item->url
            ];
        })->toArray();
    }
}