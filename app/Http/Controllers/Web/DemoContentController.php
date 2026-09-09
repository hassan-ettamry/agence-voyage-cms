<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\DemoTravelContentService;
use Illuminate\Http\Request;

class DemoContentController extends Controller
{
    public function __construct(private DemoTravelContentService $demoTravelContentService)
    {
    }

    public function index(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        return view('demo-content.index', [
            'preview' => $this->demoTravelContentService->preview($request->user()),
        ]);
    }

    public function apply(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $result = $this->demoTravelContentService->apply($request->user());

        return redirect()
            ->route('demo-content.index')
            ->with('success', 'Contenu demo voyage ajoute avec succes.')
            ->with('demo_result', $result);
    }
}
