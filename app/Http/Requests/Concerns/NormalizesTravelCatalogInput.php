<?php

namespace App\Http\Requests\Concerns;

trait NormalizesTravelCatalogInput
{
    private function normalizedStringList(string $key): array
    {
        $values = $this->input($key, []);

        if (! is_array($values)) {
            return [];
        }

        return collect($values)
            ->filter(fn ($value) => is_scalar($value))
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->values()
            ->all();
    }

    private function normalizedIntegerList(string $key): array
    {
        $values = $this->input($key, []);

        if (! is_array($values)) {
            return [];
        }

        return collect($values)
            ->filter(fn ($value) => is_scalar($value) && $value !== '')
            ->map(fn ($value) => is_numeric($value) ? (int) $value : $value)
            ->values()
            ->all();
    }

    private function normalizedItinerary(): array
    {
        $rows = $this->input('itinerary', []);

        if (! is_array($rows)) {
            return [];
        }

        return collect($rows)
            ->filter(fn ($row) => is_array($row))
            ->map(fn (array $row) => [
                'day' => is_numeric($row['day'] ?? null) ? (int) $row['day'] : ($row['day'] ?? null),
                'title' => trim((string) ($row['title'] ?? '')),
                'description' => trim((string) ($row['description'] ?? '')),
            ])
            ->filter(fn (array $row) => $row['day'] !== null || $row['title'] !== '' || $row['description'] !== '')
            ->values()
            ->all();
    }
}
