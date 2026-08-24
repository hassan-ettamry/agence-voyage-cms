<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\User;
use App\Support\DefaultAgencyRoles;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthService
{
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $agency = Agency::create([
                'name' => $data['agency_name'],
                'email' => $data['email'],
                'slug' => $this->uniqueAgencySlug($data['agency_name']),
                'status' => 'active',
                'plan' => 'free',
                'onboarding_status' => Agency::ONBOARDING_PENDING,
                'onboarding_step' => Agency::ONBOARDING_STEP_PROFILE,
                'onboarding_auto_start' => true,
            ]);

            $adminRole = DefaultAgencyRoles::ensureForAgency($agency);
            app(MenuService::class)->ensureDefaultForAgency($agency->id);

            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'agency_id' => $agency->id,
                'role_id' => $adminRole->id,
            ]);
        });
    }

    private function uniqueAgencySlug(string $name): string
    {
        $baseSlug = Str::slug($name) ?: 'agency';
        $slug = $baseSlug;
        $suffix = 2;

        while (Agency::query()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
