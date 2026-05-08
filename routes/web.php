<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\RoleController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ComponentController;
use App\Http\Controllers\Web\PermissionController;

use App\Services\Renderer\PageRenderer;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('public.welcome');
})->name('home');

// Public Pages
Route::get('/pages/{slug}', [PageController::class, 'show'])
    ->name('pages.show');


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login']);

    // Register
    Route::get('/register', [AuthController::class, 'showRegisterForm'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register']);
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])
        ->name('dashboard.stats');


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

            Route::post('{page}/publish', [PageController::class, 'publish'])
                ->name('publish');

            Route::get('{page}/versions', [PageController::class, 'versions'])
                ->name('versions');

            Route::post('{page}/versions/{version}/restore', [PageController::class, 'restore'])
                ->name('versions.restore');

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
        return $renderer->render($request->all());
    });
});