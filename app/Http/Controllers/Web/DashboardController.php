<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\DashboardStatsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private DashboardStatsService $dashboardStatsService)
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }

    /**
     * Afficher le dashboard avec statistiques
     */
    public function index(Request $request)
    {
        return view('dashboard.index', $this->dashboardStatsService->build($request->user()));
    }

    /**
     * Statistiques en JSON (pour charts AJAX)
     */
    public function stats(Request $request)
    {
        return response()->json($this->dashboardStatsService->chartStats($request->user()));
    }
}
