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
        // Utiliser le cache pour optimiser
        $stats = Cache::remember('dashboard_stats_' . auth()->id(), 300, function () {
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
                    'total' => User::where('agency_id', auth()->user()->agency_id)->count(),
                    'admins' => User::where('agency_id', auth()->user()->agency_id)
                                   ->where('role', 'admin')->count(),
                ],
            ];
        });

        // Récupérer les dernières pages modifiées
        $recentPages = Page::latest('updated_at')->limit(5)->get();

        // Récupérer les activités récentes (si vous avez une table d'activités)
        $recentActivities = $this->getRecentActivities();

        return view('dashboard', compact('stats', 'recentPages', 'recentActivities'));
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