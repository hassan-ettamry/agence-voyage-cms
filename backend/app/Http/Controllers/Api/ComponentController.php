<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    public function index()
    {
        $components = Component::orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return response()->json([
            'success' => true,
            'data' => $components
        ]);
    }

    public function store(StoreComponentRequest $request)
    {
        return Component::create($request->validated());
    }

    public function update(UpdateComponentRequest $request, Component $component)
    {
        $component->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => $component
        ]);
    }

    public function destroy(Component $component)
    {
        $component->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted'
        ]);
    }
}