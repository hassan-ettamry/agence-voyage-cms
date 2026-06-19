<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\Page;
use App\Models\SiteTemplate;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class OnboardingService
{
    public function __construct(
        private SiteTemplateApplicationService $templateApplicationService,
        private AgencyThemeService $themeService
    )
    {
    }

    public function saveProfile(User $user, array $data, ?UploadedFile $logo = null): Agency
    {
        $agency = $this->agencyFor($user);
        $oldLogo = $agency->logo;

        if ($logo !== null) {
            $data['logo'] = $logo->store("agency-branding/{$agency->id}", 'public');
        }

        $agency->forceFill($data + [
            'onboarding_status' => Agency::ONBOARDING_IN_PROGRESS,
            'onboarding_step' => Agency::ONBOARDING_STEP_TEMPLATE,
        ])->save();

        if (
            $logo !== null
            && is_string($oldLogo)
            && str_starts_with($oldLogo, "agency-branding/{$agency->id}/")
            && $oldLogo !== $agency->logo
        ) {
            Storage::disk('public')->delete($oldLogo);
        }

        DashboardStatsService::forgetFor($user);

        return $agency->refresh();
    }

    public function selectTemplate(User $user, SiteTemplate $template): Agency
    {
        $this->ensureActiveTemplate($template);

        $agency = $this->agencyFor($user);
        $data = $agency->onboarding_data ?? [];
        $data['template_id'] = $template->id;
        $data['theme_id'] = $template->theme?->status === Theme::STATUS_ACTIVE
            ? $template->theme_id
            : null;

        $agency->forceFill([
            'onboarding_status' => Agency::ONBOARDING_IN_PROGRESS,
            'onboarding_step' => Agency::ONBOARDING_STEP_THEME,
            'onboarding_data' => $data,
        ])->save();

        return $agency->refresh();
    }

    public function selectTheme(User $user, Theme $theme): Agency
    {
        $this->ensureActiveTheme($theme);

        $agency = $this->agencyFor($user);
        $data = $agency->onboarding_data ?? [];

        if (empty($data['template_id'])) {
            throw ValidationException::withMessages([
                'template_id' => 'Choisissez un template avant de sélectionner un thème.',
            ]);
        }

        $data['theme_id'] = $theme->id;

        $agency->forceFill([
            'onboarding_status' => Agency::ONBOARDING_IN_PROGRESS,
            'onboarding_step' => Agency::ONBOARDING_STEP_REVIEW,
            'onboarding_data' => $data,
        ])->save();

        return $agency->refresh();
    }

    public function dismiss(User $user): Agency
    {
        $agency = $this->agencyFor($user);

        $agency->forceFill([
            'onboarding_auto_start' => false,
        ])->save();

        DashboardStatsService::forgetFor($user);

        return $agency->refresh();
    }

    public function complete(User $user, bool $confirmReplace, bool $confirmThemeReset = false): Agency
    {
        return DB::transaction(function () use ($user, $confirmReplace, $confirmThemeReset) {
            $agency = $this->agencyFor($user);
            $template = $this->selectedTemplate($agency);
            $theme = $this->selectedTheme($agency);
            $changesTemplate = $agency->active_site_template_id !== $template->id;
            $changesTheme = $agency->theme_id !== $theme->id;

            if (
                $agency->active_site_template_id !== $template->id
                && $this->hasMeaningfulSite($agency)
                && ! $confirmReplace
            ) {
                throw ValidationException::withMessages([
                    'confirm_replace' => 'Confirmez le remplacement et l’archivage du site actuel.',
                ]);
            }

            if (
                $this->themeService->hasOverrides($agency)
                && ($changesTemplate || $changesTheme)
                && ! $confirmThemeReset
            ) {
                throw ValidationException::withMessages([
                    'confirm_theme_reset' => 'Confirmez la réinitialisation des personnalisations du thème.',
                ]);
            }

            if (! $changesTemplate) {
                $this->themeService->applyTheme($agency, $theme, $confirmThemeReset);
            } else {
                $this->templateApplicationService->apply(
                    $template,
                    $user,
                    $theme,
                    $confirmThemeReset
                );
                $agency->refresh();
            }

            $agency->forceFill([
                'onboarding_status' => Agency::ONBOARDING_COMPLETED,
                'onboarding_step' => Agency::ONBOARDING_STEP_REVIEW,
                'onboarding_auto_start' => false,
                'onboarding_completed_at' => now(),
            ])->save();

            DashboardStatsService::forgetFor($user);

            return $agency->refresh();
        });
    }

    public function selectedTemplate(Agency $agency): SiteTemplate
    {
        $templateId = $agency->onboarding_data['template_id'] ?? null;
        $template = SiteTemplate::active()->with('theme')->find($templateId);

        if ($template === null) {
            throw ValidationException::withMessages([
                'template_id' => 'Choisissez un template actif pour continuer.',
            ]);
        }

        return $template;
    }

    public function selectedTheme(Agency $agency): Theme
    {
        $themeId = $agency->onboarding_data['theme_id'] ?? null;
        $theme = Theme::active()->find($themeId);

        if ($theme === null) {
            throw ValidationException::withMessages([
                'theme_id' => 'Choisissez un thème actif pour continuer.',
            ]);
        }

        return $theme;
    }

    public function hasMeaningfulSite(Agency $agency): bool
    {
        $hasPages = Page::withoutGlobalScopes()
            ->where('agency_id', $agency->id)
            ->exists();

        if ($hasPages) {
            return true;
        }

        return DB::table('menu_items')
            ->join('menus', 'menus.id', '=', 'menu_items.menu_id')
            ->where('menus.agency_id', $agency->id)
            ->exists();
    }

    public function routeForCurrentStep(Agency $agency): string
    {
        return match ($agency->onboarding_step) {
            Agency::ONBOARDING_STEP_TEMPLATE => 'onboarding.template',
            Agency::ONBOARDING_STEP_THEME => 'onboarding.theme',
            Agency::ONBOARDING_STEP_REVIEW => 'onboarding.review',
            default => 'onboarding.profile',
        };
    }

    private function agencyFor(User $user): Agency
    {
        return Agency::query()->findOrFail($user->agency_id);
    }

    private function ensureActiveTemplate(SiteTemplate $template): void
    {
        if ($template->status !== SiteTemplate::STATUS_ACTIVE) {
            throw ValidationException::withMessages([
                'template_id' => 'Le template sélectionné n’est plus disponible.',
            ]);
        }
    }

    private function ensureActiveTheme(Theme $theme): void
    {
        if ($theme->status !== Theme::STATUS_ACTIVE) {
            throw ValidationException::withMessages([
                'theme_id' => 'Le thème sélectionné n’est plus disponible.',
            ]);
        }
    }
}
