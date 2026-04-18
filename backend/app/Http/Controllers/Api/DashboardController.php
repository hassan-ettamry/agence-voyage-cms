<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     *  Dashboard stats
     */
    public function index(): JsonResponse
    {

        $totalPages = Page::count();

        $draftPages = Page::where('status', 'draft')->count();

        $publishedPages = Page::where('status', 'published')->count();

        return response()->json([
            'pages' => [
                'total' => $totalPages,
                'draft' => $draftPages,
                'published' => $publishedPages,
            ]
        ]);
    }
}