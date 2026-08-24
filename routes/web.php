<?php

use App\Http\Controllers\Web\AccountController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\ComponentController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DemoContentController;
use App\Http\Controllers\Web\DestinationController;
use App\Http\Controllers\Web\EmailVerificationController;
use App\Http\Controllers\Web\MediaController;
use App\Http\Controllers\Web\OfferController;
use App\Http\Controllers\Web\OnboardingController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\PasswordResetController;
use App\Http\Controllers\Web\PermissionController;
use App\Http\Controllers\Web\PublicContentController;
use App\Http\Controllers\Web\RoleController;
use App\Http\Controllers\Web\SiteTemplateController;
use App\Http\Controllers\Web\ThemeController;
use App\Http\Controllers\Web\UserController;
use App\Http\Middleware\ResolvePublicAgency;
use App\Services\Renderer\PageRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('public.welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login');

    /*
    |--------------------------------------------------------------------------
    | Register
    |--------------------------------------------------------------------------
    */

    Route::get('/register', [AuthController::class, 'showRegisterForm'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:register');

    Route::get('/forgot-password', [PasswordResetController::class, 'create'])
        ->name('password.request');

    Route::post('/forgot-password', [PasswordResetController::class, 'store'])
        ->middleware('throttle:password-reset')
        ->name('password.email');

    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])
        ->name('password.reset');

    Route::post('/reset-password', [PasswordResetController::class, 'update'])
        ->middleware('throttle:password-reset')
        ->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:email-verification'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:email-verification')
        ->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    Route::prefix('onboarding')
        ->name('onboarding.')
        ->group(function () {
            Route::get('/', [OnboardingController::class, 'index'])->name('index');
            Route::get('/profile', [OnboardingController::class, 'profile'])->name('profile');
            Route::post('/profile', [OnboardingController::class, 'storeProfile'])->name('profile.store');
            Route::get('/template', [OnboardingController::class, 'template'])->name('template');
            Route::post('/template', [OnboardingController::class, 'storeTemplate'])->name('template.store');
            Route::get('/theme', [OnboardingController::class, 'theme'])->name('theme');
            Route::post('/theme', [OnboardingController::class, 'storeTheme'])->name('theme.store');
            Route::get('/review', [OnboardingController::class, 'review'])->name('review');
            Route::post('/complete', [OnboardingController::class, 'complete'])->name('complete');
            Route::post('/dismiss', [OnboardingController::class, 'dismiss'])->name('dismiss');
        });

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::prefix('account')
        ->name('account.')
        ->group(function () {
            Route::get('/profile', [AccountController::class, 'editProfile'])
                ->name('profile.edit');

            Route::put('/profile', [AccountController::class, 'updateProfile'])
                ->name('profile.update');

            Route::get('/settings', [AccountController::class, 'editSettings'])
                ->name('settings.edit');

            Route::put('/settings', [AccountController::class, 'updateSettings'])
                ->name('settings.update');

            Route::get('/security/password', [AccountController::class, 'editPassword'])
                ->name('security.password.edit');

            Route::put('/security/password', [AccountController::class, 'updatePassword'])
                ->name('security.password.update');

            Route::get('/security/email', [AccountController::class, 'editEmail'])
                ->name('security.email.edit');

            Route::put('/security/email', [AccountController::class, 'updateEmail'])
                ->name('security.email.update');

            Route::get('/security/sessions', [AccountController::class, 'sessions'])
                ->name('security.sessions.index');

            Route::delete('/security/sessions', [AccountController::class, 'destroyOtherSessions'])
                ->name('security.sessions.destroy-others');

            Route::delete('/security/sessions/{session}', [AccountController::class, 'destroySession'])
                ->name('security.sessions.destroy');
        });

    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])
        ->name('dashboard.stats');

    Route::get('/demo-content', [DemoContentController::class, 'index'])
        ->name('demo-content.index');

    Route::post('/demo-content/apply', [DemoContentController::class, 'apply'])
        ->name('demo-content.apply');

    Route::get('/site-templates', [SiteTemplateController::class, 'index'])
        ->name('site-templates.index');

    Route::post('/site-templates/{siteTemplate}/apply', [SiteTemplateController::class, 'apply'])
        ->name('site-templates.apply');

    Route::get('/themes', [ThemeController::class, 'index'])
        ->name('themes.index');

    Route::get('/themes/customize', [ThemeController::class, 'customize'])
        ->name('themes.customize');

    Route::put('/themes/customize', [ThemeController::class, 'updateCustomization'])
        ->name('themes.customize.update');

    Route::delete('/themes/customize', [ThemeController::class, 'resetCustomization'])
        ->name('themes.customize.reset');

    Route::post('/themes/{theme}/apply', [ThemeController::class, 'apply'])
        ->name('themes.apply');

    /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    */

    Route::resource('pages', PageController::class)
        ->except(['show']);

    Route::prefix('pages')
        ->name('pages.')
        ->group(function () {

            /*
            |--------------------------------------------------------------------------
            | Builder
            |--------------------------------------------------------------------------
            */

            Route::get('{page}/builder', [PageController::class, 'builder'])
                ->name('builder');

            Route::get('{page}/preview', [PageController::class, 'preview'])
                ->name('preview');

            /*
            |--------------------------------------------------------------------------
            | Publish
            |--------------------------------------------------------------------------
            */

            Route::post('{page}/publish', [PageController::class, 'publish'])
                ->name('publish');

            /*
            |--------------------------------------------------------------------------
            | Versions
            |--------------------------------------------------------------------------
            */

            Route::get('{page}/versions', [PageController::class, 'versions'])
                ->name('versions');

            Route::post('{page}/versions/{version}/restore', [PageController::class, 'restore'])
                ->name('versions.restore');

            /*
            |--------------------------------------------------------------------------
            | Duplicate
            |--------------------------------------------------------------------------
            */

            Route::post('{page}/duplicate', [PageController::class, 'duplicate'])
                ->name('duplicate');
        });

    /*
    |--------------------------------------------------------------------------
    | Components
    |--------------------------------------------------------------------------
    */

    Route::prefix('components')
        ->name('components.')
        ->group(function () {

            Route::get('/', [ComponentController::class, 'index'])
                ->name('index');

            Route::get('/builder', [ComponentController::class, 'builder'])
                ->name('builder');

            Route::get('/grouped', [ComponentController::class, 'grouped'])
                ->name('grouped');
        });

    /*
    |--------------------------------------------------------------------------
    | Users / Roles / Permissions
    |--------------------------------------------------------------------------
    */

    Route::resource('users', UserController::class);

    Route::resource('roles', RoleController::class);

    Route::resource('admin/destinations', DestinationController::class)
        ->names('destinations')
        ->except(['show']);

    Route::resource('admin/offers', OfferController::class)
        ->names('offers')
        ->except(['show']);

    Route::get('media/picker', [MediaController::class, 'picker'])
        ->name('media.picker');

    Route::resource('media', MediaController::class)
        ->except(['show']);

    Route::put('permissions/modules/{module}', [PermissionController::class, 'updateModule'])
        ->name('permissions.modules.update');

    Route::resource('permissions', PermissionController::class);

    /*
    |--------------------------------------------------------------------------
    | Builder Renderer
    |--------------------------------------------------------------------------
    */

    Route::post('/builder/render', function (
        Request $request,
        PageRenderer $renderer
    ) {

        $structure = $request->input('structure', []);

        $mode = $request->input(
            'mode',
            'editor'
        );

        if (! is_string($mode)) {
            $mode = 'editor';
        }

        if (! is_array($structure) || ! array_is_list($structure)) {
            abort(422, 'Invalid builder structure root.');
        }

        return $renderer->render(
            $structure,
            $mode
        );

    });
});

/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/

Route::prefix('/sites/{agencySlug}')
    ->middleware(ResolvePublicAgency::class)
    ->group(function () {
        Route::get('/', [PageController::class, 'siteHome'])
            ->name('public.site.home');

        Route::get('/pages/{pageSlug}', [PageController::class, 'siteShow'])
            ->name('public.site.pages.show');

        Route::get('/destinations', [PublicContentController::class, 'destinations'])
            ->name('public.site.destinations.index');

        Route::get('/destinations/{destinationSlug}', [PublicContentController::class, 'destination'])
            ->name('public.site.destinations.show');

        Route::get('/offers', [PublicContentController::class, 'offers'])
            ->name('public.site.offers.index');

        Route::get('/offers/{offerSlug}', [PublicContentController::class, 'offer'])
            ->name('public.site.offers.show');
    });

Route::get('/pages/{slug}', [PageController::class, 'show'])
    ->name('pages.show');

Route::get('/destinations', [PublicContentController::class, 'legacyDestinations'])
    ->name('public.destinations.index');

Route::get('/destinations/{slug}', [PublicContentController::class, 'legacyDestination'])
    ->name('public.destinations.show');

Route::get('/offers', [PublicContentController::class, 'legacyOffers'])
    ->name('public.offers.index');

Route::get('/offers/{slug}', [PublicContentController::class, 'legacyOffer'])
    ->name('public.offers.show');
