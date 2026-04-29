<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Destination;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified'); // Optionnel: email verification
    }

    /**
     * Afficher le dashboard avec statistiques
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $agencyId = $user->agency_id;
    
        $stats = Cache::remember("dashboard_stats_v1_{$user->id}", 300, function () use ($agencyId) {
            return [
                'pages' => [
                    'total' => Page::count(),
                    'draft' => Page::where('status', 'draft')->count(),
                    'published' => Page::where('status', 'published')->count(),
                ],
                'destinations' => [
                    'total' => Destination::count(),
                    'featured' => Destination::where('is_featured', true)->count(),
                ],
                'offers' => [
                    'total' => Offer::count(),
                    'special' => Offer::where('is_special', true)->count(),
                ],
                'users' => [
                    'total' => User::where('agency_id', $agencyId)->count(),
    
                    'admins' => User::where('agency_id', $agencyId)
                        ->whereHas('role', fn($q) => $q->where('slug', 'admin'))
                        ->count(),
                ],
            ];
        });
    
        $recentPages = Page::latest('updated_at')->limit(5)->get();
    
        $recentActivities = $this->getRecentActivities();
    
        return view('dashboard.index', compact('stats', 'recentPages', 'recentActivities'));
    }
    
    /**
     * Récupérer les activités récentes
     */
    private function getRecentActivities()
    {
        // À implémenter avec un package comme spatie/laravel-activitylog
        return collect([
            (object) ['description' => 'Bienvenue sur votre dashboard', 'created_at' => now()],
        ]);
    }

    /**
     * Statistiques en JSON (pour charts AJAX)
     */
    public function stats()
    {
        $stats = [
            'pages_by_month' => Page::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->get(),
            'pages_by_status' => Page::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get(),
        ];

        return response()->json($stats);
    }
}