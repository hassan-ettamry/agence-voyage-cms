<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Support\DefaultAgencyRoles;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        $this->call(ComponentSeeder::class);
        $this->call(ThemeSeeder::class);
        $this->call(SiteTemplateSeeder::class);

        Agency::query()
            ->each(fn (Agency $agency) => DefaultAgencyRoles::ensureForAgency($agency));
    }
}
