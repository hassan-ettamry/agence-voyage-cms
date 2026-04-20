<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ComponentController;

/**
 * Test route
 */
Route::get('/test', function () {
    return response()->json([
        'message' => 'API OK'
    ]);
});

/**
 * Auth routes (public)
 */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/**
 * Protected routes (auth:sanctum)
 */
Route::middleware('auth:sanctum')->group(function () {

    /**
     * User
     */
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    /**
     * Components
     */
    Route::apiResource('components', ComponentController::class)->only([
        'index',
        'store',
        'update',
        'destroy'
    ]);

    /**
     * Pages CRUD
     */
    Route::apiResource('pages', PageController::class)->only([
        'index',
        'show',
        'store',
        'update',
        'destroy'
    ]);

    /**
     * Page versions (history)
     */
    Route::get('/pages/{page}/versions', [PageController::class, 'versions']);

    /**
     * Restore version (rollback)
     */
    Route::post('/pages/{page}/versions/{version}/restore', [PageController::class, 'restore']);

    /**
     * Publish page
     */
    Route::patch('/pages/{page}/publish', [PageController::class, 'publish']);

    /**
     * Dashboard
     */
    Route::get('/dashboard', [DashboardController::class, 'index']);
});