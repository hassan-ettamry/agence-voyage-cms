<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Theme\ApplyThemeRequest;
use App\Http\Requests\Theme\UpdateThemeCustomizationRequest;
use App\Models\Agency;
use App\Models\Theme;
use App\Services\AgencyThemeService;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function __construct(private AgencyThemeService $themeService)
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Theme::class);

        $themes = Theme::active()
            ->orderBy('name')
            ->get();

        $agency = $this->agencyFor($request);
        $currentThemeId = $agency->theme_id;
        $hasOverrides = $this->themeService->hasOverrides($agency);
        $effectiveVariables = $this->themeService->effectiveVariables($agency);

        return view('themes.index', compact('themes', 'currentThemeId', 'hasOverrides', 'effectiveVariables'));
    }

    public function apply(ApplyThemeRequest $request, Theme $theme)
    {
        $this->authorize('apply', $theme);
        abort_unless($theme->status === Theme::STATUS_ACTIVE, 404);

        $this->themeService->applyTheme(
            $this->agencyFor($request),
            $theme,
            $request->boolean('confirm_reset')
        );

        return redirect()
            ->route('themes.index')
            ->with('success', 'Theme applied.');
    }

    public function customize(Request $request)
    {
        $this->authorize('viewAny', Theme::class);

        $agency = $this->agencyFor($request);
        $agency->load('theme');

        if ($agency->theme === null) {
            return redirect()
                ->route('themes.index')
                ->withErrors(['theme' => 'Apply a theme preset before customizing it.']);
        }

        return view('themes.customize', [
            'agency' => $agency,
            'theme' => $agency->theme,
            'effectiveVariables' => $this->themeService->effectiveVariables($agency),
            'presetVariables' => $this->themeService->presetVariables($agency->theme),
            'previewThemeCss' => $this->themeService->cssVariables($agency),
            'hasOverrides' => $this->themeService->hasOverrides($agency),
            'fontOptions' => config('site-theme.fonts', []),
            'radiusOptions' => config('site-theme.radii', []),
            'shadowOptions' => config('site-theme.shadows', []),
        ]);
    }

    public function updateCustomization(UpdateThemeCustomizationRequest $request)
    {
        $this->authorize('update', Theme::class);

        $this->themeService->updateOverrides(
            $this->agencyFor($request),
            $request->validated()
        );

        return redirect()
            ->route('themes.customize')
            ->with('success', 'Theme customizations saved.');
    }

    public function resetCustomization(Request $request)
    {
        $this->authorize('update', Theme::class);

        $this->themeService->resetOverrides($this->agencyFor($request));

        return redirect()
            ->route('themes.customize')
            ->with('success', 'Theme customizations reset to the active preset.');
    }

    private function agencyFor(Request $request): Agency
    {
        return Agency::query()->findOrFail($request->user()->agency_id);
    }
}
