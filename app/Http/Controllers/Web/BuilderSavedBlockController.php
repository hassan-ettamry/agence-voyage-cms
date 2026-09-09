<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Builder\StoreSavedBlockRequest;
use App\Http\Requests\Builder\UpdateSavedBlockRequest;
use App\Models\BuilderSavedBlock;
use App\Services\PageService;

class BuilderSavedBlockController extends Controller
{
    public function __construct(private PageService $pageService) {}

    public function index()
    {
        $this->authorize('viewAny', BuilderSavedBlock::class);

        return BuilderSavedBlock::query()->latest()->get()->map(fn (BuilderSavedBlock $block) => $this->payload($block));
    }

    public function store(StoreSavedBlockRequest $request)
    {
        $structure = $this->pageService->canonicalizeStructure($request->validated('structure'));

        $block = BuilderSavedBlock::create([
            'agency_id' => $request->user()->agency_id,
            'created_by' => $request->user()->id,
            'name' => $request->validated('name'),
            'category' => $request->validated('category'),
            'structure' => $structure,
        ]);

        return response()->json($this->payload($block), 201);
    }

    public function update(UpdateSavedBlockRequest $request, BuilderSavedBlock $savedBlock)
    {
        $savedBlock->update($request->validated());

        return response()->json($this->payload($savedBlock));
    }

    public function destroy(BuilderSavedBlock $savedBlock)
    {
        $this->authorize('delete', $savedBlock);
        $savedBlock->delete();

        return response()->noContent();
    }

    private function payload(BuilderSavedBlock $block): array
    {
        return [
            'id' => $block->id,
            'name' => $block->name,
            'category' => $block->category,
            'structure' => $block->structure,
            'updated_at' => $block->updated_at?->toIso8601String(),
        ];
    }
}
