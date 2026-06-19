<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\SiteTemplate;
use App\Services\AgencyThemeService;
use App\Services\SiteTemplateApplicationService;
use Illuminate\Http\Request;

class SiteTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }

    public function index(Request $request, AgencyThemeService $themeService)
    {
        $templates = SiteTemplate::active()
            ->with('theme')
            ->orderBy('name')
            ->get();

        $hasPages = $this->agencyHasPages($request);
        $currentTemplateId = $request->user()->agency?->active_site_template_id;
        $hasThemeOverrides = $themeService->hasOverrides($request->user()->agency);

        return view('site-templates.index', compact(
            'templates',
            'hasPages',
            'currentTemplateId',
            'hasThemeOverrides'
        ));
    }

    public function apply(
        Request $request,
        SiteTemplate $siteTemplate,
        SiteTemplateApplicationService $applicationService,
        AgencyThemeService $themeService
    ) {
        abort_unless($siteTemplate->status === SiteTemplate::STATUS_ACTIVE, 404);

        if ($this->agencyHasPages($request)) {
            $request->validate([
                'confirm_replace' => ['accepted'],
            ], [
                'confirm_replace.accepted' => 'Please confirm that this template will replace the current site.',
            ]);
        }

        $changesTemplate = $request->user()->agency?->active_site_template_id !== $siteTemplate->id;

        if ($changesTemplate && $themeService->hasOverrides($request->user()->agency)) {
            $request->validate([
                'confirm_theme_reset' => ['accepted'],
            ], [
                'confirm_theme_reset.accepted' => 'Please confirm that applying this template will reset theme customizations.',
            ]);
        }

        $homePage = $applicationService->apply(
            $siteTemplate,
            $request->user(),
            null,
            $request->boolean('confirm_theme_reset')
        );

        return redirect()
            ->route('pages.builder', $homePage)
            ->with('success', 'Site template applied. Your previous content was archived.');
    }

    private function agencyHasPages(Request $request): bool
    {
        return Page::withoutGlobalScopes()
            ->where('agency_id', $request->user()->agency_id)
            ->exists();
    }
}
