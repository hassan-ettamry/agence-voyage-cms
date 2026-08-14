<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Destination;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Offer;
use App\Models\Page;
use App\Models\Theme;
use Illuminate\Database\Seeder;

class PublicTenancyDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ThemeSeeder::class);

        $this->seedAgency(
            'Agence Atlas',
            'agence-atlas',
            'atlas-demo@example.test',
            'sunset-luxe',
            'Atlas: aventures au Maroc',
            'Marrakech Atlas',
            'Summer Offer Atlas',
            'Voyages Atlas'
        );

        $this->seedAgency(
            'Agence Ocean',
            'agence-ocean',
            'ocean-demo@example.test',
            'ocean-blue',
            'Ocean: escapades en bord de mer',
            'Marrakech Ocean',
            'Summer Offer Ocean',
            'Escapades Ocean'
        );
    }

    private function seedAgency(
        string $name,
        string $slug,
        string $email,
        string $themeSlug,
        string $homeTitle,
        string $destinationName,
        string $offerTitle,
        string $menuName
    ): void {
        $theme = Theme::query()->where('slug', $themeSlug)->firstOrFail();
        $agency = Agency::query()->updateOrCreate(
            ['slug' => $slug],
            ['name' => $name, 'email' => $email, 'status' => 'active', 'theme_id' => $theme->id]
        );

        $home = Page::withoutGlobalScopes()->updateOrCreate(
            ['agency_id' => $agency->id, 'slug' => 'home'],
            [
                'title' => $homeTitle,
                'status' => Page::STATUS_PUBLISHED,
                'structure' => [[
                    'type' => 'text',
                    'props' => ['content' => $homeTitle],
                    'children' => [],
                ]],
                'published_at' => now(),
            ]
        );

        $destination = Destination::withoutGlobalScopes()->updateOrCreate(
            ['agency_id' => $agency->id, 'slug' => 'marrakech'],
            [
                'name' => $destinationName,
                'country' => 'Morocco',
                'description' => "Contenu de démonstration propre à {$name}.",
                'is_featured' => true,
                'status' => Destination::STATUS_PUBLISHED,
            ]
        );

        Offer::withoutGlobalScopes()->updateOrCreate(
            ['agency_id' => $agency->id, 'slug' => 'summer-offer'],
            [
                'destination_id' => $destination->id,
                'title' => $offerTitle,
                'description' => "Offre de démonstration propre à {$name}.",
                'price' => $slug === 'agence-atlas' ? 890 : 1290,
                'duration_days' => $slug === 'agence-atlas' ? 5 : 7,
                'is_special' => true,
                'status' => Offer::STATUS_PUBLISHED,
            ]
        );

        $menu = Menu::withoutGlobalScopes()->updateOrCreate(
            ['agency_id' => $agency->id, 'slug' => 'main'],
            ['name' => $menuName, 'is_default' => true]
        );

        MenuItem::query()->updateOrCreate(
            ['menu_id' => $menu->id, 'page_id' => $home->id],
            ['title' => $slug === 'agence-atlas' ? 'Accueil Atlas' : 'Accueil Ocean', 'url' => null, 'order' => 1]
        );
        MenuItem::query()->updateOrCreate(
            ['menu_id' => $menu->id, 'url' => '/destinations'],
            ['title' => $slug === 'agence-atlas' ? 'Circuits Atlas' : 'Destinations Ocean', 'page_id' => null, 'order' => 2]
        );
        MenuItem::query()->updateOrCreate(
            ['menu_id' => $menu->id, 'url' => '/offers'],
            ['title' => $slug === 'agence-atlas' ? 'Offres aventure' : 'Offres plage', 'page_id' => null, 'order' => 3]
        );
    }
}
