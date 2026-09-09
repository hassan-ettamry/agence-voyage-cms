<?php

namespace App\Services;

use App\Models\Page;
use Illuminate\Validation\ValidationException;

class CatalogPageStructure
{
    private const COMPONENTS = [
        'destinations' => 'destination-grid',
        'offers' => 'offer-grid',
    ];

    public function componentForSlug(string $slug): ?string
    {
        return self::COMPONENTS[$slug] ?? null;
    }

    public function catalogNode(array $structure, string $slug): ?array
    {
        $type = $this->componentForSlug($slug);

        if ($type === null) {
            return null;
        }

        return collect($this->catalogNodes($structure, $type))->first();
    }

    public function assertPublishable(Page $page, array $structure): void
    {
        $type = $this->componentForSlug($page->slug);

        if ($type === null) {
            return;
        }

        $count = count($this->catalogNodes($structure, $type));

        if ($count !== 1) {
            $label = $page->slug === 'destinations' ? 'Destination Grid' : 'Offer Grid';

            throw ValidationException::withMessages([
                'structure' => "The {$page->title} catalog must contain exactly one {$label} with Catalog Page Mode enabled before it can be published.",
            ]);
        }
    }

    private function catalogNodes(array $nodes, string $type): array
    {
        $matches = [];

        foreach ($nodes as $node) {
            if (! is_array($node)) {
                continue;
            }

            if (
                ($node['type'] ?? null) === $type
                && $this->enabled(data_get($node, 'props.catalogMode'))
            ) {
                $matches[] = $node;
            }

            $children = $node['children'] ?? [];

            if (is_array($children)) {
                $matches = [...$matches, ...$this->catalogNodes($children, $type)];
            }
        }

        return $matches;
    }

    private function enabled(mixed $value): bool
    {
        return in_array($value, ['yes', 'true', true, 1, '1'], true);
    }
}
