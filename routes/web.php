<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ComponentController;

/*
|--------------------------------------------------------------------------
| Routes Publiques
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Pages publiques (front)
Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');

/*
|--------------------------------------------------------------------------
| Routes d'Authentification
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Routes Protégées (Authentification requise)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
    
    // Pages - Gestion complète
    Route::resource('pages', PageController::class)->except(['show']);
    
    // Routes supplémentaires pour les pages
    Route::prefix('pages')->name('pages.')->group(function () {
        Route::post('{page}/publish', [PageController::class, 'publish'])->name('publish');
        Route::get('{page}/versions', [PageController::class, 'versions'])->name('versions');
        Route::post('{page}/versions/{version}/restore', [PageController::class, 'restore'])->name('versions.restore');
        Route::post('{page}/duplicate', [PageController::class, 'duplicate'])->name('duplicate');
    });

    // COMPONENTS MODULE
    Route::prefix('components')->name('components.')->group(function () {

        Route::get('/', [ComponentController::class, 'index'])->name('index');

        Route::get('/builder', [ComponentController::class, 'builder'])->name('builder');

        Route::get('/grouped', [ComponentController::class, 'grouped'])->name('grouped');

    });

});