<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\User;
use App\Support\AgencyContext;
use Illuminate\Support\Facades\DB;

class MenuService
{
    public function getMenu(string $name): array
    {
        if ($name === 'main' && AgencyContext::has()) {
            $this->ensureDefaultForAgency(AgencyContext::get());
        }

        $menu = Menu::with('items.page')
            ->where(function ($query) use ($name) {
                $query->where('slug', $name)
                    ->orWhere('name', $name);
            })
            ->first()
            ?? ($name === 'main'
                ? Menu::with('items.page')->where('is_default', true)->first()
                : null);

        if (! $menu) {
            return [];
        }

        return $this->itemsForMenu($menu, false);
    }

    public function menuForPagePreview(Page $page): array
    {
        $item = $page->menuItems()
            ->with('menu.items.page')
            ->oldest('created_at')
            ->first();

        if (! $item?->menu) {
            return [];
        }

        return $this->itemsForMenu($item->menu, true);
    }

    private function itemsForMenu(Menu $menu, bool $includeDraftPages): array
    {
        return $menu->items
            ->filter(fn ($item) => $item->url || ($item->page && ($includeDraftPages || $item->page->isPublished())))
            ->map(function ($item) {
                return [
                    'title' => $item->title,
                    'url' => $item->page
                        ? route('pages.show', $item->page->slug)
                        : $item->url,
                ];
            })->toArray();
    }

    public function ensureDefaultForAgency(string $agencyId): Menu
    {
        return DB::transaction(function () use ($agencyId) {
            $default = Menu::withoutGlobalScopes()
                ->where('agency_id', $agencyId)
                ->where('is_default', true)
                ->orderBy('id')
                ->first();

            if ($default) {
                return $default;
            }

            $menu = Menu::withoutGlobalScopes()
                ->where('agency_id', $agencyId)
                ->orderBy('id')
                ->first();

            if ($menu) {
                $menu->forceFill([
                    'is_default' => true,
                    'slug' => $menu->slug ?: 'main',
                ])->save();

                return $menu;
            }

            return Menu::withoutGlobalScopes()->create([
                'agency_id' => $agencyId,
                'name' => 'Main Menu',
                'slug' => 'main',
                'is_default' => true,
            ]);
        });
    }

    public function optionsForUser(User $user): array
    {
        $this->ensureDefaultForAgency($user->agency_id);

        return Menu::query()
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get()
            ->map(fn (Menu $menu) => [
                'id' => (string) $menu->id,
                'name' => $menu->name,
                'is_default' => $menu->is_default,
            ])
            ->all();
    }

    public function selectionForPage(Page $page): string
    {
        $item = $page->menuItems()->with('menu')->oldest('created_at')->first();

        return $item?->menu_id ? (string) $item->menu_id : 'none';
    }

    public function syncPageSelection(Page $page, ?string $selection): void
    {
        $selection = $selection ?: 'none';

        if ($selection === 'none') {
            $page->menuItems()->delete();
            return;
        }

        $menu = $selection === 'default'
            ? $this->ensureDefaultForAgency($page->agency_id)
            : Menu::withoutGlobalScopes()
                ->where('agency_id', $page->agency_id)
                ->whereKey($selection)
                ->firstOrFail();

        $page->menuItems()->where('menu_id', '!=', $menu->id)->delete();

        $item = MenuItem::where('menu_id', $menu->id)
            ->where('page_id', $page->id)
            ->first();

        if ($item) {
            $item->update([
                'title' => $page->title,
                'url' => null,
                'parent_id' => null,
            ]);

            return;
        }

        MenuItem::create([
            'menu_id' => $menu->id,
            'page_id' => $page->id,
            'title' => $page->title,
            'url' => null,
            'parent_id' => null,
            'order' => $this->nextOrder($menu),
        ]);
    }

    private function nextOrder(Menu $menu): int
    {
        return ((int) $menu->items()->max('order')) + 1;
    }
}
