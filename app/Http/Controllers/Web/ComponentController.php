<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ComponentService;

class ComponentController extends Controller
{
    private ComponentService $service;

    public function __construct(ComponentService $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    /**
     * Liste simple (debug / admin)
     */
    public function index()
    {
        $components = $this->service->getActive();

        return view('components.index', compact('components'));
    }

    /**
     * Format pour builder
     */
    public function builder()
    {
        $components = $this->service->getForBuilder();

        return response()->json($components);
    }

    /**
     * Groupé par catégorie
     */
    public function grouped()
    {
        $components = $this->service->getGrouped();

        return response()->json($components);
    }
}