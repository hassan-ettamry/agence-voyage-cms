<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\Theme;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AgencyThemeService
{
    public function defaults(): array
    {
        return config('site-theme.defaults', []);
    }

    public function presetVariables(?Theme $theme): array
    {
        return array_replace(
            $this->defaults(),
            $this->normalize($theme?->variables ?? [])
        );
    }

    public function effectiveVariables(?Agency $agency): array
    {
        if ($agency === null) {
            return $this->defaults();
        }

        $agency->loadMissing('theme');

        return array_replace(
            $this->presetVariables($agency->theme),
            $this->normalize($agency->theme_overrides ?? [])
        );
    }

    public function updateOverrides(Agency $agency, array $variables): Agency
    {
        $preset = $this->presetVariables($agency->theme);
        $normalized = $this->normalize($variables);
        $overrides = [];

        foreach ($normalized as $key => $value) {
            if (($preset[$key] ?? null) !== $value) {
                $overrides[$key] = $value;
            }
        }

        $agency->forceFill([
            'theme_overrides' => $overrides === [] ? null : $overrides,
        ])->save();

        return $agency->refresh();
    }

    public function resetOverrides(Agency $agency): Agency
    {
        $agency->forceFill(['theme_overrides' => null])->save();

        return $agency->refresh();
    }

    public function applyTheme(Agency $agency, Theme $theme, bool $confirmReset = false): Agency
    {
        if ($theme->status !== Theme::STATUS_ACTIVE) {
            throw ValidationException::withMessages([
                'theme' => 'The selected theme is not active.',
            ]);
        }

        if ($agency->theme_id === $theme->id) {
            return $agency;
        }

        if ($this->hasOverrides($agency) && ! $confirmReset) {
            throw ValidationException::withMessages([
                'confirm_reset' => 'Confirm that changing the preset will reset agency theme customizations.',
            ]);
        }

        $agency->forceFill([
            'theme_id' => $theme->id,
            'theme_overrides' => null,
        ])->save();

        return $agency->refresh();
    }

    public function hasOverrides(?Agency $agency): bool
    {
        return $agency !== null && $this->normalize($agency->theme_overrides ?? []) !== [];
    }

    public function cssVariables(?Agency $agency): string
    {
        return collect($this->effectiveVariables($agency))
            ->map(function (string $value, string $key) {
                if ($key === 'shadow') {
                    $value = config("site-theme.shadows.{$value}", config('site-theme.shadows.soft'));
                }

                return '--site-'.Str::kebab($key).': '.$value.';';
            })
            ->implode(' ');
    }

    public function normalize(array $variables): array
    {
        $normalized = [];

        foreach (config('site-theme.colors', []) as $key) {
            $value = $variables[$key] ?? null;

            if (is_string($value) && preg_match('/^#[0-9a-fA-F]{6}$/', $value) === 1) {
                $normalized[$key] = strtolower($value);
            }
        }

        $fontOptions = array_values(config('site-theme.fonts', []));

        foreach (['bodyFont', 'headingFont'] as $key) {
            $value = $variables[$key] ?? null;

            if (is_string($value) && in_array($value, $fontOptions, true)) {
                $normalized[$key] = $value;
            }
        }

        $radius = $variables['radius'] ?? null;

        if (is_string($radius) && in_array($radius, config('site-theme.radii', []), true)) {
            $normalized['radius'] = $radius;
        }

        $shadow = $variables['shadow'] ?? null;

        if (is_string($shadow) && array_key_exists($shadow, config('site-theme.shadows', []))) {
            $normalized['shadow'] = $shadow;
        }

        return $normalized;
    }
}
