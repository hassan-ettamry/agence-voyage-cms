<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\SiteTemplate;
use App\Models\Theme;
use App\Services\OnboardingService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{
    public function __construct(private OnboardingService $onboardingService)
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $agency = $this->agencyForAdmin($request);

        if ($agency->onboardingIsComplete()) {
            return redirect()->route('dashboard');
        }

        return redirect()->route($this->onboardingService->routeForCurrentStep($agency));
    }

    public function profile(Request $request)
    {
        $agency = $this->agencyForAdmin($request);

        return view('onboarding.profile', [
            'agency' => $agency,
            'currentStep' => Agency::ONBOARDING_STEP_PROFILE,
        ]);
    }

    public function storeProfile(Request $request)
    {
        $agency = $this->agencyForAdmin($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        unset($validated['logo']);

        $this->onboardingService->saveProfile(
            $request->user(),
            $validated,
            $request->file('logo')
        );

        return redirect()
            ->route('onboarding.template')
            ->with('success', 'Profil de l’agence enregistré.');
    }

    public function template(Request $request)
    {
        $agency = $this->agencyForAdmin($request);

        return view('onboarding.template', [
            'agency' => $agency,
            'templates' => SiteTemplate::active()->with('theme')->orderBy('name')->get(),
            'selectedTemplateId' => $agency->onboarding_data['template_id'] ?? null,
            'currentStep' => Agency::ONBOARDING_STEP_TEMPLATE,
        ]);
    }

    public function storeTemplate(Request $request)
    {
        $this->agencyForAdmin($request);

        $validated = $request->validate([
            'template_id' => [
                'required',
                'uuid',
                Rule::exists('site_templates', 'id')->where('status', SiteTemplate::STATUS_ACTIVE),
            ],
        ]);

        $template = SiteTemplate::active()->with('theme')->findOrFail($validated['template_id']);
        $this->onboardingService->selectTemplate($request->user(), $template);

        return redirect()
            ->route('onboarding.theme')
            ->with('success', 'Template sélectionné. Son thème recommandé est présélectionné.');
    }

    public function theme(Request $request)
    {
        $agency = $this->agencyForAdmin($request);
        $template = $this->findSelectedTemplate($agency);

        if ($template === null) {
            return redirect()
                ->route('onboarding.template')
                ->withErrors(['template_id' => 'Choisissez un template avant de sélectionner un thème.']);
        }

        return view('onboarding.theme', [
            'agency' => $agency,
            'template' => $template,
            'themes' => Theme::active()->orderBy('name')->get(),
            'selectedThemeId' => $agency->onboarding_data['theme_id'] ?? $template->theme_id,
            'currentStep' => Agency::ONBOARDING_STEP_THEME,
        ]);
    }

    public function storeTheme(Request $request)
    {
        $agency = $this->agencyForAdmin($request);

        if ($this->findSelectedTemplate($agency) === null) {
            return redirect()
                ->route('onboarding.template')
                ->withErrors(['template_id' => 'Choisissez un template avant de sélectionner un thème.']);
        }

        $validated = $request->validate([
            'theme_id' => [
                'required',
                'uuid',
                Rule::exists('themes', 'id')->where('status', Theme::STATUS_ACTIVE),
            ],
        ]);

        $theme = Theme::active()->findOrFail($validated['theme_id']);
        $this->onboardingService->selectTheme($request->user(), $theme);

        return redirect()
            ->route('onboarding.review')
            ->with('success', 'Thème sélectionné.');
    }

    public function review(Request $request)
    {
        $agency = $this->agencyForAdmin($request);
        $template = $this->findSelectedTemplate($agency);
        $theme = $this->findSelectedTheme($agency);

        if ($template === null) {
            return redirect()
                ->route('onboarding.template')
                ->withErrors(['template_id' => 'Choisissez un template actif pour continuer.']);
        }

        if ($theme === null) {
            return redirect()
                ->route('onboarding.theme')
                ->withErrors(['theme_id' => 'Choisissez un thème actif pour continuer.']);
        }

        return view('onboarding.review', [
            'agency' => $agency,
            'template' => $template,
            'theme' => $theme,
            'requiresReplacementConfirmation' => $agency->active_site_template_id !== $template->id
                && $this->onboardingService->hasMeaningfulSite($agency),
            'requiresThemeResetConfirmation' => ! empty($agency->theme_overrides)
                && (
                    $agency->active_site_template_id !== $template->id
                    || $agency->theme_id !== $theme->id
                ),
            'currentStep' => Agency::ONBOARDING_STEP_REVIEW,
        ]);
    }

    public function complete(Request $request)
    {
        $this->agencyForAdmin($request);

        $this->onboardingService->complete(
            $request->user(),
            $request->boolean('confirm_replace'),
            $request->boolean('confirm_theme_reset')
        );

        return redirect()
            ->route('dashboard')
            ->with('onboarding_completed', true)
            ->with('success', 'Votre site est configuré. Bienvenue sur votre tableau de bord.');
    }

    public function dismiss(Request $request)
    {
        $this->agencyForAdmin($request);
        $this->onboardingService->dismiss($request->user());

        return redirect()
            ->route('dashboard')
            ->with('success', 'Configuration reportée. Vous pourrez la reprendre depuis le tableau de bord.');
    }

    private function agencyForAdmin(Request $request): Agency
    {
        abort_unless($request->user()?->isAdmin(), 403);

        return Agency::query()->findOrFail($request->user()->agency_id);
    }

    private function findSelectedTemplate(Agency $agency): ?SiteTemplate
    {
        return SiteTemplate::active()
            ->with('theme')
            ->find($agency->onboarding_data['template_id'] ?? null);
    }

    private function findSelectedTheme(Agency $agency): ?Theme
    {
        return Theme::active()->find($agency->onboarding_data['theme_id'] ?? null);
    }
}
