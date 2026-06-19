<?php

namespace App\Services\Renderer;

use App\Models\Component;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class ComponentRegistry
{
    private array $components = [];
    private array $views = [];
    private array $builtIns = [
        'container' => [
            'schema_json' => [
                'props' => [
                    'containerRole' => ['type' => 'text'],
                    'display' => ['type' => 'text'],
                    'gridSpan' => ['type' => 'number'],
                    'gridColumns' => ['type' => 'number'],
                    'gap' => ['type' => 'number'],
                    'justifyContent' => ['type' => 'text'],
                    'alignItems' => ['type' => 'text'],
                    'flexDirection' => ['type' => 'text'],
                ],
            ],
        ],
        'icon' => [
            'schema_json' => [
                'props' => [
                    'icon' => ['type' => 'text'],
                    'size' => ['type' => 'number'],
                    'color' => ['type' => 'color'],
                    'backgroundColor' => ['type' => 'color'],
                    'padding' => ['type' => 'number'],
                    'borderRadius' => ['type' => 'number'],
                    'align' => ['type' => 'text'],
                ],
            ],
        ],
        'icon-text' => [
            'schema_json' => [
                'props' => [
                    'icon' => ['type' => 'text'],
                    'title' => ['type' => 'text'],
                    'text' => ['type' => 'text'],
                    'layout' => ['type' => 'text'],
                    'size' => ['type' => 'number'],
                    'gap' => ['type' => 'number'],
                    'iconColor' => ['type' => 'color'],
                    'titleColor' => ['type' => 'color'],
                    'textColor' => ['type' => 'color'],
                    'align' => ['type' => 'text'],
                ],
            ],
        ],
        'link' => [
            'schema_json' => [
                'props' => [
                    'text' => ['type' => 'text'],
                    'url' => ['type' => 'text'],
                    'target' => ['type' => 'text'],
                    'color' => ['type' => 'color'],
                    'fontSize' => ['type' => 'number'],
                    'fontWeight' => ['type' => 'number'],
                    'underline' => ['type' => 'text'],
                    'align' => ['type' => 'text'],
                ],
            ],
        ],
        'video' => [
            'schema_json' => [
                'props' => [
                    'url' => ['type' => 'text'],
                    'title' => ['type' => 'text'],
                    'height' => ['type' => 'number'],
                    'controls' => ['type' => 'text'],
                    'autoplay' => ['type' => 'text'],
                    'borderRadius' => ['type' => 'number'],
                ],
            ],
        ],
        'iframe' => [
            'schema_json' => [
                'props' => [
                    'url' => ['type' => 'text'],
                    'title' => ['type' => 'text'],
                    'height' => ['type' => 'number'],
                    'allowFullscreen' => ['type' => 'text'],
                    'borderRadius' => ['type' => 'number'],
                ],
            ],
        ],
        'gallery' => [
            'schema_json' => [
                'props' => [
                    'images' => ['type' => 'text'],
                    'columns' => ['type' => 'number'],
                    'gap' => ['type' => 'number'],
                    'height' => ['type' => 'number'],
                    'borderRadius' => ['type' => 'number'],
                ],
            ],
        ],
        'map' => [
            'schema_json' => [
                'props' => [
                    'address' => ['type' => 'text'],
                    'embedUrl' => ['type' => 'text'],
                    'height' => ['type' => 'number'],
                    'zoom' => ['type' => 'number'],
                    'borderRadius' => ['type' => 'number'],
                ],
            ],
        ],
        'contact-form' => [
            'schema_json' => [
                'props' => [
                    'title' => ['type' => 'text'],
                    'subtitle' => ['type' => 'text'],
                    'nameLabel' => ['type' => 'text'],
                    'emailLabel' => ['type' => 'text'],
                    'messageLabel' => ['type' => 'text'],
                    'buttonText' => ['type' => 'text'],
                    'buttonColor' => ['type' => 'color'],
                    'textColor' => ['type' => 'color'],
                    'actionUrl' => ['type' => 'text'],
                ],
            ],
        ],
        'faq' => [
            'schema_json' => [
                'props' => [
                    'title' => ['type' => 'text'],
                    'items' => ['type' => 'text'],
                    'allowMultiple' => ['type' => 'text'],
                ],
            ],
        ],
        'countdown' => [
            'schema_json' => [
                'props' => [
                    'label' => ['type' => 'text'],
                    'targetDate' => ['type' => 'text'],
                    'expiredText' => ['type' => 'text'],
                    'backgroundColor' => ['type' => 'color'],
                    'textColor' => ['type' => 'color'],
                    'accentColor' => ['type' => 'color'],
                ],
            ],
        ],
        'destination-grid' => [
            'schema_json' => [
                'title' => ['type' => 'text'],
                'source' => ['type' => 'text'],
                'limit' => ['type' => 'number'],
            ],
        ],
        'featured-destinations' => [
            'schema_json' => [
                'title' => ['type' => 'text'],
                'limit' => ['type' => 'number'],
            ],
        ],
        'offer-grid' => [
            'schema_json' => [
                'title' => ['type' => 'text'],
                'source' => ['type' => 'text'],
                'destination_id' => ['type' => 'text'],
                'limit' => ['type' => 'number'],
            ],
        ],
        'special-offers' => [
            'schema_json' => [
                'title' => ['type' => 'text'],
                'limit' => ['type' => 'number'],
            ],
        ],
        'offer-card' => [
            'schema_json' => [
                'offer_id' => ['type' => 'text'],
            ],
        ],
    ];

    public function __construct()
    {
        $this->components = Cache::remember('components.registry', 3600, function () {
            return Component::where('is_active', true)
                ->get()
                ->keyBy('type')
                ->toArray();
        });
    }

    // vérifier si composant existe
    public function exists(string $type): bool
    {
        return isset($this->components[$type]) || isset($this->builtIns[$type]);
    }

    // récupérer schema
    public function getSchema(string $type): ?array
    {
        return $this->components[$type]['schema_json'] ?? $this->builtIns[$type]['schema_json'] ?? null;
    }

    // récupérer vue avec cache
    public function resolveView(string $type): ?string
    {
        if (isset($this->views[$type])) {
            return $this->views[$type];
        }

        $view = "components.builder.".$type;
        return $this->views[$type] = View::exists($view) ? $view : null;
    }
}
