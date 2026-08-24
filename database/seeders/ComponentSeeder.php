<?php

namespace Database\Seeders;

use App\Models\Component;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class ComponentSeeder extends Seeder
{
    public function run(): void
    {
        $components = [
            ...$this->signatureComponents(),
            ...$this->catalogComponents(),
            [
                'type' => 'hero',
                'name' => 'Hero',
                'category' => 'marketing',
                'icon' => 'sparkles',
                'schema_json' => ['props' => [
                    'eyebrow' => ['type' => 'text'],
                    'title' => ['type' => 'text'],
                    'description' => ['type' => 'text'],
                    'buttonText' => ['type' => 'text'],
                    'url' => ['type' => 'text'],
                    'backgroundMode' => ['type' => 'text'],
                    'backgroundColor' => ['type' => 'color'],
                    'backgroundImage' => ['type' => 'image'],
                    'textColor' => ['type' => 'color'],
                ]],
            ],
            [
                'type' => 'section',
                'name' => 'Section',
                'category' => 'layout',
                'icon' => 'rectangle-stack',
                'schema_json' => ['props' => [
                    'title' => ['type' => 'text'],
                    'backgroundMode' => ['type' => 'text'],
                    'backgroundColor' => ['type' => 'color'],
                    'backgroundImage' => ['type' => 'image'],
                    'paddingTop' => ['type' => 'number'],
                    'paddingBottom' => ['type' => 'number'],
                    'maxWidth' => ['type' => 'number'],
                ]],
            ],
            [
                'type' => 'heading',
                'name' => 'Heading',
                'category' => 'content',
                'icon' => 'h1',
                'schema_json' => ['props' => [
                    'text' => ['type' => 'text'],
                    'tag' => ['type' => 'text'],
                    'color' => ['type' => 'color'],
                    'fontSize' => ['type' => 'number'],
                    'fontWeight' => ['type' => 'number'],
                    'lineHeight' => ['type' => 'number'],
                    'align' => ['type' => 'text'],
                ]],
            ],
            [
                'type' => 'text',
                'name' => 'Text',
                'category' => 'content',
                'icon' => 'bars-3-bottom-left',
                'schema_json' => ['props' => [
                    'text' => ['type' => 'text'],
                    'content' => ['type' => 'text'],
                    'color' => ['type' => 'color'],
                    'fontSize' => ['type' => 'number'],
                    'fontWeight' => ['type' => 'number'],
                    'lineHeight' => ['type' => 'number'],
                    'align' => ['type' => 'text'],
                ]],
            ],
            [
                'type' => 'button',
                'name' => 'Button',
                'category' => 'content',
                'icon' => 'cursor-arrow-rays',
                'schema_json' => ['props' => [
                    'text' => ['type' => 'text'],
                    'url' => ['type' => 'text'],
                    'target' => ['type' => 'text'],
                    'backgroundColor' => ['type' => 'color'],
                    'textColor' => ['type' => 'color'],
                    'borderRadius' => ['type' => 'number'],
                    'align' => ['type' => 'text'],
                ]],
            ],
            [
                'type' => 'image',
                'name' => 'Image',
                'category' => 'media',
                'icon' => 'photo',
                'schema_json' => ['props' => [
                    'src' => ['type' => 'image'],
                    'altText' => ['type' => 'text'],
                    'title' => ['type' => 'text'],
                    'caption' => ['type' => 'text'],
                    'height' => ['type' => 'number'],
                    'borderRadius' => ['type' => 'number'],
                ]],
            ],
            [
                'type' => 'container',
                'name' => 'Container',
                'category' => 'layout',
                'icon' => 'square-3-stack-3d',
                'schema_json' => [
                    'props' => [
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
            [
                'type' => 'richtext',
                'name' => 'RichText',
                'category' => 'content',
                'icon' => 'pencil-square',
                'schema_json' => [
                    'controlTabs' => ['Default', 'Advanced'],
                    'tabs' => [
                        'content' => [
                            'title' => 'Content',
                            'fields' => [
                                'html' => [
                                    'type' => 'richtext',
                                    'label' => 'RichText',
                                    'default' => '<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Mollitia, quis! Iste debitis id nulla, nesciunt minus enim, ea sed, doloremque inventore tenetur magnam minima fugit sint consequatur repudiandae! Numquam, perspiciatis.</p>',
                                ],
                            ],
                        ],
                        'typography' => [
                            'title' => 'Typography',
                            'fields' => [
                                'color' => [
                                    'type' => 'color',
                                    'label' => 'Text Color',
                                    'default' => '#111827',
                                ],
                                'fontSize' => [
                                    'type' => 'range',
                                    'label' => 'Font Size',
                                    'min' => 12,
                                    'max' => 72,
                                    'default' => 16,
                                ],
                                'fontWeight' => [
                                    'type' => 'range',
                                    'label' => 'Font Weight',
                                    'min' => 100,
                                    'max' => 900,
                                    'default' => 400,
                                ],
                                'lineHeight' => [
                                    'type' => 'range',
                                    'label' => 'Line Height',
                                    'min' => 1,
                                    'max' => 3,
                                    'default' => 1.65,
                                ],
                                'padding' => [
                                    'type' => 'range',
                                    'label' => 'Padding',
                                    'min' => 0,
                                    'max' => 100,
                                    'default' => 16,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'icon',
                'name' => 'Icon',
                'category' => 'content',
                'icon' => 'sparkles',
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
                    'tabs' => [
                        'content' => [
                            'title' => 'Content',
                            'fields' => [
                                'icon' => ['type' => 'select', 'label' => 'Icon', 'default' => 'star', 'options' => ['star', 'map-pin', 'phone', 'envelope', 'globe', 'compass', 'heart', 'camera', 'check-circle', 'sparkles']],
                                'align' => ['type' => 'select', 'label' => 'Align', 'default' => 'left', 'options' => ['left', 'center', 'right']],
                            ],
                        ],
                        'style' => [
                            'title' => 'Style',
                            'fields' => [
                                'size' => ['type' => 'range', 'label' => 'Size', 'min' => 16, 'max' => 120, 'default' => 40],
                                'color' => ['type' => 'color', 'label' => 'Icon Color', 'default' => '#2563eb'],
                                'backgroundColor' => ['type' => 'color', 'label' => 'Background', 'default' => '#ffffff'],
                                'padding' => ['type' => 'range', 'label' => 'Padding', 'min' => 0, 'max' => 60, 'default' => 0],
                                'borderRadius' => ['type' => 'range', 'label' => 'Radius', 'min' => 0, 'max' => 80, 'default' => 0],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'icon-text',
                'name' => 'Icon With Text',
                'category' => 'content',
                'icon' => 'chat-bubble-left-right',
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
                    'tabs' => [
                        'content' => [
                            'title' => 'Content',
                            'fields' => [
                                'icon' => ['type' => 'select', 'label' => 'Icon', 'default' => 'map-pin', 'options' => ['star', 'map-pin', 'phone', 'envelope', 'globe', 'compass', 'heart', 'camera', 'check-circle', 'sparkles']],
                                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'Icon title'],
                                'text' => ['type' => 'textarea', 'label' => 'Text', 'default' => 'Short supporting text.'],
                            ],
                        ],
                        'style' => [
                            'title' => 'Style',
                            'fields' => [
                                'layout' => ['type' => 'select', 'label' => 'Layout', 'default' => 'horizontal', 'options' => ['horizontal', 'vertical']],
                                'align' => ['type' => 'select', 'label' => 'Align', 'default' => 'left', 'options' => ['left', 'center', 'right']],
                                'size' => ['type' => 'range', 'label' => 'Icon Size', 'min' => 16, 'max' => 96, 'default' => 36],
                                'gap' => ['type' => 'range', 'label' => 'Gap', 'min' => 0, 'max' => 48, 'default' => 12],
                                'iconColor' => ['type' => 'color', 'label' => 'Icon Color', 'default' => '#2563eb'],
                                'titleColor' => ['type' => 'color', 'label' => 'Title Color', 'default' => '#111827'],
                                'textColor' => ['type' => 'color', 'label' => 'Text Color', 'default' => '#64748b'],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'link',
                'name' => 'Link',
                'category' => 'content',
                'icon' => 'link',
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
                    'tabs' => [
                        'content' => [
                            'title' => 'Content',
                            'fields' => [
                                'text' => ['type' => 'text', 'label' => 'Text', 'default' => 'Link text'],
                                'url' => ['type' => 'text', 'label' => 'URL', 'default' => '#'],
                                'target' => ['type' => 'select', 'label' => 'Open In', 'default' => 'same-tab', 'options' => ['same-tab', 'new-tab']],
                            ],
                        ],
                        'style' => [
                            'title' => 'Style',
                            'fields' => [
                                'align' => ['type' => 'select', 'label' => 'Align', 'default' => 'left', 'options' => ['left', 'center', 'right']],
                                'color' => ['type' => 'color', 'label' => 'Color', 'default' => '#2563eb'],
                                'fontSize' => ['type' => 'range', 'label' => 'Font Size', 'min' => 12, 'max' => 48, 'default' => 16],
                                'fontWeight' => ['type' => 'range', 'label' => 'Font Weight', 'min' => 100, 'max' => 900, 'default' => 500],
                                'underline' => ['type' => 'select', 'label' => 'Underline', 'default' => 'yes', 'options' => ['yes', 'no']],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'video',
                'name' => 'Video',
                'category' => 'media',
                'icon' => 'video-camera',
                'schema_json' => [
                    'props' => [
                        'url' => ['type' => 'text'],
                        'title' => ['type' => 'text'],
                        'height' => ['type' => 'number'],
                        'controls' => ['type' => 'text'],
                        'autoplay' => ['type' => 'text'],
                        'borderRadius' => ['type' => 'number'],
                    ],
                    'tabs' => [
                        'content' => [
                            'title' => 'Content',
                            'fields' => [
                                'url' => ['type' => 'text', 'label' => 'Video URL', 'default' => ''],
                                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'Video'],
                            ],
                        ],
                        'style' => [
                            'title' => 'Style',
                            'fields' => [
                                'height' => ['type' => 'range', 'label' => 'Height', 'min' => 160, 'max' => 720, 'default' => 360],
                                'borderRadius' => ['type' => 'range', 'label' => 'Radius', 'min' => 0, 'max' => 48, 'default' => 12],
                                'controls' => ['type' => 'select', 'label' => 'Controls', 'default' => 'yes', 'options' => ['yes', 'no']],
                                'autoplay' => ['type' => 'select', 'label' => 'Autoplay', 'default' => 'no', 'options' => ['yes', 'no']],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'iframe',
                'name' => 'iFrame',
                'category' => 'media',
                'icon' => 'code-bracket-square',
                'schema_json' => [
                    'props' => [
                        'url' => ['type' => 'text'],
                        'title' => ['type' => 'text'],
                        'height' => ['type' => 'number'],
                        'allowFullscreen' => ['type' => 'text'],
                        'borderRadius' => ['type' => 'number'],
                    ],
                    'tabs' => [
                        'content' => [
                            'title' => 'Content',
                            'fields' => [
                                'url' => ['type' => 'text', 'label' => 'Embed URL', 'default' => ''],
                                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'Embedded content'],
                            ],
                        ],
                        'style' => [
                            'title' => 'Style',
                            'fields' => [
                                'height' => ['type' => 'range', 'label' => 'Height', 'min' => 160, 'max' => 900, 'default' => 420],
                                'borderRadius' => ['type' => 'range', 'label' => 'Radius', 'min' => 0, 'max' => 48, 'default' => 12],
                                'allowFullscreen' => ['type' => 'select', 'label' => 'Fullscreen', 'default' => 'yes', 'options' => ['yes', 'no']],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'gallery',
                'name' => 'Gallery',
                'category' => 'media',
                'icon' => 'photo',
                'schema_json' => [
                    'props' => [
                        'images' => ['type' => 'text_or_array'],
                        'columns' => ['type' => 'number'],
                        'gap' => ['type' => 'number'],
                        'height' => ['type' => 'number'],
                        'borderRadius' => ['type' => 'number'],
                    ],
                    'tabs' => [
                        'content' => [
                            'title' => 'Content',
                            'fields' => [
                                'images' => ['type' => 'repeater', 'label' => 'Images', 'default' => [], 'maxItems' => 24, 'itemFields' => [
                                    ['key' => 'url', 'label' => 'Image', 'type' => 'media'],
                                    ['key' => 'alt', 'label' => 'Alt Text', 'type' => 'text'],
                                ]],
                            ],
                        ],
                        'style' => [
                            'title' => 'Style',
                            'fields' => [
                                'columns' => ['type' => 'range', 'label' => 'Columns', 'min' => 1, 'max' => 6, 'default' => 3],
                                'gap' => ['type' => 'range', 'label' => 'Gap', 'min' => 0, 'max' => 48, 'default' => 16],
                                'height' => ['type' => 'range', 'label' => 'Image Height', 'min' => 120, 'max' => 520, 'default' => 220],
                                'borderRadius' => ['type' => 'range', 'label' => 'Radius', 'min' => 0, 'max' => 48, 'default' => 12],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'map',
                'name' => 'Map',
                'category' => 'media',
                'icon' => 'map',
                'schema_json' => [
                    'props' => [
                        'presentation' => ['type' => 'text'],
                        'address' => ['type' => 'text'],
                        'embedUrl' => ['type' => 'text'],
                        'markerLabel' => ['type' => 'text'],
                        'showDirections' => ['type' => 'text'],
                        'height' => ['type' => 'number'],
                        'zoom' => ['type' => 'number'],
                        'borderRadius' => ['type' => 'number'],
                    ],
                    'tabs' => [
                        'content' => [
                            'title' => 'Content',
                            'fields' => [
                                'presentation' => ['type' => 'select', 'label' => 'Presentation', 'default' => 'embedded', 'options' => ['embedded', 'standalone']],
                                'address' => ['type' => 'text', 'label' => 'Address', 'default' => 'Marrakech, Morocco'],
                                'embedUrl' => ['type' => 'text', 'label' => 'Custom Embed URL', 'default' => ''],
                                'markerLabel' => ['type' => 'text', 'label' => 'Heading', 'default' => 'Find us'],
                                'showDirections' => ['type' => 'toggle', 'label' => 'Show Open in Maps Link', 'default' => 'yes'],
                            ],
                        ],
                        'style' => [
                            'title' => 'Style',
                            'fields' => [
                                'height' => ['type' => 'range', 'label' => 'Height', 'min' => 160, 'max' => 720, 'default' => 360],
                                'zoom' => ['type' => 'range', 'label' => 'Zoom', 'min' => 1, 'max' => 20, 'default' => 12],
                                'borderRadius' => ['type' => 'range', 'label' => 'Radius', 'min' => 0, 'max' => 48, 'default' => 12],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'contact-form',
                'name' => 'Contact Form',
                'category' => 'forms',
                'icon' => 'envelope',
                'schema_json' => [
                    'props' => [
                        'presentation' => ['type' => 'text'],
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
                    'tabs' => [
                        'content' => [
                            'title' => 'Content',
                            'fields' => [
                                'presentation' => ['type' => 'select', 'label' => 'Presentation', 'default' => 'standalone', 'options' => ['standalone', 'embedded']],
                                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'Contact us'],
                                'subtitle' => ['type' => 'textarea', 'label' => 'Subtitle', 'default' => 'Send us a message and we will reply soon.'],
                                'nameLabel' => ['type' => 'text', 'label' => 'Name Label', 'default' => 'Name'],
                                'emailLabel' => ['type' => 'text', 'label' => 'Email Label', 'default' => 'Email'],
                                'messageLabel' => ['type' => 'text', 'label' => 'Message Label', 'default' => 'Message'],
                                'buttonText' => ['type' => 'text', 'label' => 'Button Text', 'default' => 'Send message'],
                                'actionUrl' => ['type' => 'text', 'label' => 'Action URL', 'default' => ''],
                            ],
                        ],
                        'style' => [
                            'title' => 'Style',
                            'fields' => [
                                'buttonColor' => ['type' => 'color', 'label' => 'Button Color', 'default' => '#2563eb'],
                                'textColor' => ['type' => 'color', 'label' => 'Text Color', 'default' => '#111827'],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'faq',
                'name' => 'FAQ',
                'category' => 'content',
                'icon' => 'question-mark-circle',
                'schema_json' => [
                    'props' => [
                        'title' => ['type' => 'text'],
                        'items' => ['type' => 'text_or_array'],
                        'allowMultiple' => ['type' => 'text'],
                    ],
                    'tabs' => [
                        'content' => [
                            'title' => 'Content',
                            'fields' => [
                                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'Frequently asked questions'],
                                'items' => ['type' => 'repeater', 'label' => 'Items', 'default' => [
                                    ['question' => 'What is included?', 'answer' => 'Flights, hotels, transfers, and guided activities can be included depending on the offer.'],
                                    ['question' => 'Can I customize the trip?', 'answer' => 'Yes. Contact the agency to adapt dates, hotels, and activities.'],
                                ], 'maxItems' => 24, 'itemFields' => [
                                    ['key' => 'question', 'label' => 'Question', 'type' => 'text'],
                                    ['key' => 'answer', 'label' => 'Answer', 'type' => 'text'],
                                ]],
                                'allowMultiple' => ['type' => 'select', 'label' => 'Allow Multiple Open', 'default' => 'yes', 'options' => ['yes', 'no']],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'countdown',
                'name' => 'Countdown',
                'category' => 'marketing',
                'icon' => 'clock',
                'schema_json' => [
                    'props' => [
                        'label' => ['type' => 'text'],
                        'targetDate' => ['type' => 'text'],
                        'expiredText' => ['type' => 'text'],
                        'backgroundColor' => ['type' => 'color'],
                        'textColor' => ['type' => 'color'],
                        'accentColor' => ['type' => 'color'],
                    ],
                    'tabs' => [
                        'content' => [
                            'title' => 'Content',
                            'fields' => [
                                'label' => ['type' => 'text', 'label' => 'Label', 'default' => 'Offer ends in'],
                                'targetDate' => ['type' => 'text', 'label' => 'Target Date', 'default' => '2026-12-31T23:59'],
                                'expiredText' => ['type' => 'text', 'label' => 'Expired Text', 'default' => 'Offer expired'],
                            ],
                        ],
                        'style' => [
                            'title' => 'Style',
                            'fields' => [
                                'backgroundColor' => ['type' => 'color', 'label' => 'Background', 'default' => '#111827'],
                                'textColor' => ['type' => 'color', 'label' => 'Text Color', 'default' => '#ffffff'],
                                'accentColor' => ['type' => 'color', 'label' => 'Accent Color', 'default' => '#38bdf8'],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($components as $component) {
            $component['schema_json'] = ['schema_version' => 2] + ($component['schema_json'] ?? []);
            Component::updateOrCreate(
                ['type' => $component['type']],
                $component + ['is_active' => true]
            );
        }

        Component::whereIn('type', ['row', 'column'])->delete();
        Cache::forget('components.registry');
    }

    private function signatureComponents(): array
    {
        return [
            $this->signatureComponent('search-hero', 'Search Hero', 'marketing', 'magnifying-glass', [
                'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow', 'default' => 'YOUR JOURNEY STARTS HERE'],
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'Where will you go next?'],
                'description' => ['type' => 'textarea', 'label' => 'Description', 'default' => 'Discover thoughtful journeys created by local travel experts.'],
                'searchType' => ['type' => 'select', 'label' => 'Search', 'default' => 'destinations', 'options' => ['destinations', 'offers']],
                'placeholder' => ['type' => 'text', 'label' => 'Placeholder', 'default' => 'Search destinations'],
                'buttonText' => ['type' => 'text', 'label' => 'Button', 'default' => 'Explore'],
                'backgroundImage' => ['type' => 'media', 'label' => 'Background Image', 'default' => ''],
                'backgroundColor' => ['type' => 'color', 'label' => 'Background Color', 'default' => '#12372f'],
                'backgroundPosition' => ['type' => 'select', 'label' => 'Image Position', 'default' => 'center', 'options' => ['center', 'top', 'bottom', 'left', 'right']],
                'overlayOpacity' => ['type' => 'range', 'label' => 'Overlay', 'min' => 0, 'max' => 90, 'default' => 55],
                'minHeight' => ['type' => 'range', 'label' => 'Minimum Height', 'min' => 360, 'max' => 900, 'default' => 620],
                'contentAlign' => ['type' => 'select', 'label' => 'Content Alignment', 'default' => 'left', 'options' => ['left', 'center']],
            ]),
            $this->signatureComponent('image-text', 'Image & Text', 'content', 'photo', [
                'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow', 'default' => 'OUR APPROACH'],
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'Travel designed around you'],
                'text' => ['type' => 'textarea', 'label' => 'Text', 'default' => 'We combine local knowledge with personal service to create journeys that feel entirely your own.'],
                'image' => ['type' => 'media', 'label' => 'Image', 'default' => ''],
                'imageAlt' => ['type' => 'text', 'label' => 'Image Alt', 'default' => ''],
                'imagePosition' => ['type' => 'select', 'label' => 'Image Position', 'default' => 'left', 'options' => ['left', 'right']],
                'imageRatio' => ['type' => 'select', 'label' => 'Image Ratio', 'default' => '4/3', 'options' => ['square', '4/3', '16/9']],
                'objectFit' => ['type' => 'select', 'label' => 'Object Fit', 'default' => 'cover', 'options' => ['cover', 'contain']],
                'backgroundColor' => ['type' => 'color', 'label' => 'Background Color', 'default' => 'transparent'],
                'buttonText' => ['type' => 'text', 'label' => 'Button', 'default' => 'Discover our story'],
                'url' => ['type' => 'text', 'label' => 'URL', 'default' => '/about'],
            ]),
            $this->signatureComponent('feature-grid', 'Feature Grid', 'content', 'squares-2x2', [
                'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow', 'default' => 'WHY TRAVEL WITH US'],
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'Every detail, thoughtfully handled'],
                'items' => ['type' => 'repeater', 'label' => 'Features', 'default' => [
                    ['icon' => 'compass', 'title' => 'Local expertise', 'text' => 'Travel with people who know each place deeply.'],
                    ['icon' => 'heart', 'title' => 'Personal service', 'text' => 'Every journey is shaped around your interests.'],
                    ['icon' => 'check-circle', 'title' => 'Trusted support', 'text' => 'We are with you before, during and after your trip.'],
                ], 'maxItems' => 12, 'itemFields' => [
                    ['key' => 'icon', 'label' => 'Icon', 'type' => 'text'],
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text'],
                    ['key' => 'text', 'label' => 'Text', 'type' => 'text'],
                ]],
                'columns' => ['type' => 'range', 'label' => 'Columns', 'min' => 1, 'max' => 4, 'default' => 3],
            ]),
            $this->signatureComponent('destination-carousel', 'Destination Carousel', 'travel', 'map', [
                'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow', 'default' => 'FEATURED DESTINATIONS'],
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'Explore remarkable places'],
                'intro' => ['type' => 'textarea', 'label' => 'Introduction', 'default' => 'Handpicked places with the character, culture and experiences that make a journey memorable.'],
                'showViewAll' => ['type' => 'toggle', 'label' => 'Show View All', 'default' => 'yes'],
                'viewAllLabel' => ['type' => 'text', 'label' => 'View All Label', 'default' => 'View all destinations'],
                'fallbackImage' => ['type' => 'media', 'label' => 'Fallback Image', 'default' => '/images/site-templates/culture-journey.png'],
                'buttonText' => ['type' => 'text', 'label' => 'CTA Text', 'default' => 'View destination'],
                'limit' => ['type' => 'range', 'label' => 'Limit', 'min' => 1, 'max' => 12, 'default' => 6],
                'source' => ['type' => 'select', 'label' => 'Source', 'default' => 'featured', 'options' => ['latest', 'featured', 'manual']],
                'manual_ids' => ['type' => 'entity-multiselect', 'entity' => 'destinations', 'label' => 'Manual Selection', 'default' => [], 'when' => ['key' => 'source', 'is' => 'manual']],
                'continent' => ['type' => 'select', 'label' => 'Continent', 'default' => '', 'options' => ['', 'africa', 'asia', 'europe', 'north-america', 'south-america', 'oceania', 'antarctica']],
                'travelType' => ['type' => 'select', 'label' => 'Travel Type', 'default' => '', 'options' => ['', 'beach', 'mountain', 'cultural', 'adventure', 'city', 'desert', 'nature', 'wellness', 'family']],
                'idealMonth' => ['type' => 'number', 'label' => 'Ideal Month', 'default' => ''],
                'sort' => ['type' => 'select', 'label' => 'Sort', 'default' => 'latest', 'options' => ['latest', 'oldest', 'name']],
                'cardVariant' => ['type' => 'select', 'label' => 'Card Variant', 'default' => 'overlay', 'options' => ['overlay', 'compact']],
                'sectionTone' => ['type' => 'select', 'label' => 'Section Tone', 'default' => 'default', 'options' => ['default', 'surface', 'dark']],
                'gap' => ['type' => 'range', 'label' => 'Gap', 'min' => 8, 'max' => 48, 'default' => 20],
                'showImage' => ['type' => 'toggle', 'label' => 'Show Image', 'default' => 'yes'],
                'showTitle' => ['type' => 'toggle', 'label' => 'Show Title', 'default' => 'yes'],
                'showDescription' => ['type' => 'toggle', 'label' => 'Show Description', 'default' => 'yes'],
                'showLocation' => ['type' => 'toggle', 'label' => 'Show Location', 'default' => 'yes'],
                'showTravelTypes' => ['type' => 'toggle', 'label' => 'Show Travel Types', 'default' => 'yes'],
                'showCta' => ['type' => 'toggle', 'label' => 'Show CTA', 'default' => 'yes'],
            ]),
            $this->signatureComponent('offer-comparison', 'Offer Comparison', 'travel', 'table-cells', [
                'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow', 'default' => 'COMPARE JOURNEYS'],
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'Compare our journeys'],
                'intro' => ['type' => 'textarea', 'label' => 'Introduction', 'default' => 'Compare pace, destination and price before opening the full itinerary.'],
                'showViewAll' => ['type' => 'toggle', 'label' => 'Show View All', 'default' => 'yes'],
                'viewAllLabel' => ['type' => 'text', 'label' => 'View All Label', 'default' => 'View all journeys'],
                'fallbackImage' => ['type' => 'media', 'label' => 'Fallback Image', 'default' => '/images/site-templates/sunset-luxe.png'],
                'limit' => ['type' => 'range', 'label' => 'Offers', 'min' => 2, 'max' => 4, 'default' => 3],
                'buttonText' => ['type' => 'text', 'label' => 'Button', 'default' => 'View journey'],
                'source' => ['type' => 'select', 'label' => 'Source', 'default' => 'latest', 'options' => ['latest', 'special', 'manual']],
                'manual_ids' => ['type' => 'entity-multiselect', 'entity' => 'offers', 'label' => 'Manual Selection', 'default' => [], 'when' => ['key' => 'source', 'is' => 'manual']],
                'destination_id' => ['type' => 'entity-select', 'entity' => 'destinations', 'label' => 'Destination Filter', 'default' => ''],
                'continent' => ['type' => 'select', 'label' => 'Continent', 'default' => '', 'options' => ['', 'africa', 'asia', 'europe', 'north-america', 'south-america', 'oceania', 'antarctica']],
                'travelType' => ['type' => 'select', 'label' => 'Travel Type', 'default' => '', 'options' => ['', 'beach', 'mountain', 'cultural', 'adventure', 'city', 'desert', 'nature', 'wellness', 'family']],
                'idealMonth' => ['type' => 'number', 'label' => 'Ideal Month', 'default' => ''],
                'sort' => ['type' => 'select', 'label' => 'Sort', 'default' => 'latest', 'options' => ['latest', 'oldest', 'name', 'price_low', 'price_high']],
                'sectionTone' => ['type' => 'select', 'label' => 'Section Tone', 'default' => 'surface', 'options' => ['default', 'surface', 'dark']],
                'showImage' => ['type' => 'toggle', 'label' => 'Show Image', 'default' => 'yes'],
                'showTitle' => ['type' => 'toggle', 'label' => 'Show Title', 'default' => 'yes'],
                'showDescription' => ['type' => 'toggle', 'label' => 'Show Description', 'default' => 'yes'],
                'showDestination' => ['type' => 'toggle', 'label' => 'Show Destination', 'default' => 'yes'],
                'showPrice' => ['type' => 'toggle', 'label' => 'Show Price', 'default' => 'yes'],
                'showDuration' => ['type' => 'toggle', 'label' => 'Show Duration', 'default' => 'yes'],
                'showCta' => ['type' => 'toggle', 'label' => 'Show CTA', 'default' => 'yes'],
            ]),
            $this->signatureComponent('testimonials', 'Testimonials', 'marketing', 'chat-bubble-left-right', [
                'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow', 'default' => 'TRAVELLER STORIES'],
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'What our travellers say'],
                'items' => ['type' => 'repeater', 'label' => 'Testimonials', 'default' => [
                    ['quote' => 'An unforgettable journey from beginning to end.', 'name' => 'Amelia R.', 'role' => 'London', 'rating' => 5],
                    ['quote' => 'Every detail felt personal and effortless.', 'name' => 'Daniel M.', 'role' => 'Toronto', 'rating' => 5],
                    ['quote' => 'We discovered places we would never have found alone.', 'name' => 'Sofia K.', 'role' => 'Madrid', 'rating' => 5],
                ], 'maxItems' => 12, 'itemFields' => [
                    ['key' => 'quote', 'label' => 'Quote', 'type' => 'text'],
                    ['key' => 'name', 'label' => 'Name', 'type' => 'text'],
                    ['key' => 'role', 'label' => 'Location / Role', 'type' => 'text'],
                    ['key' => 'rating', 'label' => 'Rating', 'type' => 'number', 'default' => 5],
                ]],
            ]),
            $this->signatureComponent('trust-logos', 'Trust Logos', 'marketing', 'shield-check', [
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'Trusted by travellers and partners'],
                'items' => ['type' => 'textarea', 'label' => 'Logos', 'default' => "IATA|\nTravelife|\nTripadvisor|"],
            ]),
            $this->signatureComponent('stats-counter', 'Stats Counter', 'marketing', 'chart-bar', [
                'items' => ['type' => 'textarea', 'label' => 'Statistics', 'default' => "15+|Years of experience\n40+|Destinations\n2,500+|Happy travellers\n24/7|Local support"],
            ]),
            $this->signatureComponent('cta-banner', 'CTA Banner', 'marketing', 'megaphone', [
                'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow', 'default' => 'START PLANNING'],
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'Your next story starts here'],
                'text' => ['type' => 'textarea', 'label' => 'Text', 'default' => 'Tell us where you want to go and we will shape the journey with you.'],
                'buttonText' => ['type' => 'text', 'label' => 'Button', 'default' => 'Plan my journey'],
                'url' => ['type' => 'text', 'label' => 'URL', 'default' => '/contact'],
                'backgroundColor' => ['type' => 'color', 'label' => 'Background Color', 'default' => '#059669'],
                'contentAlign' => ['type' => 'select', 'label' => 'Content Alignment', 'default' => 'left', 'options' => ['left', 'center']],
            ]),
            $this->signatureComponent('newsletter', 'Newsletter', 'forms', 'envelope', [
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'Ideas for your next journey'],
                'text' => ['type' => 'textarea', 'label' => 'Text', 'default' => 'Occasional travel inspiration, destination stories and new departures.'],
                'placeholder' => ['type' => 'text', 'label' => 'Placeholder', 'default' => 'Email address'],
                'buttonText' => ['type' => 'text', 'label' => 'Button', 'default' => 'Subscribe'],
                'consentText' => ['type' => 'text', 'label' => 'Consent', 'default' => 'Design preview only. No data is submitted.'],
            ]),
            $this->signatureComponent('accordion', 'Accordion', 'content', 'bars-arrow-down', [
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'Helpful information'],
                'items' => ['type' => 'textarea', 'label' => 'Items', 'default' => "How does planning work?|Share your ideas and we will propose a tailored journey.\nCan the itinerary change?|Yes. Every itinerary can be adapted before confirmation."],
            ]),
            $this->signatureComponent('tabs', 'Tabs', 'content', 'rectangle-group', [
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'Know before you go'],
                'items' => ['type' => 'textarea', 'label' => 'Tabs', 'default' => "Overview|A concise introduction to the experience.\nBest time to visit|Choose the season that matches your travel style.\nGood to know|Practical details prepared by our local team."],
            ]),
            $this->signatureComponent('contact-info', 'Contact Information', 'forms', 'phone', [
                'presentation' => ['type' => 'select', 'label' => 'Presentation', 'default' => 'standalone', 'options' => ['standalone', 'embedded']],
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'Talk to a travel designer'],
                'text' => ['type' => 'textarea', 'label' => 'Text', 'default' => 'We would love to hear where you are dreaming of going.'],
                'email' => ['type' => 'text', 'label' => 'Email Override', 'default' => ''],
                'phone' => ['type' => 'text', 'label' => 'Phone Override', 'default' => ''],
                'address' => ['type' => 'text', 'label' => 'Address Override', 'default' => ''],
            ]),
            $this->signatureComponent('social-links', 'Social Links', 'content', 'share', [
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'Follow the journey'],
                'items' => ['type' => 'textarea', 'label' => 'Links', 'default' => "Instagram|https://instagram.com\nFacebook|https://facebook.com\nYouTube|https://youtube.com"],
                'align' => ['type' => 'select', 'label' => 'Alignment', 'default' => 'left', 'options' => ['left', 'center', 'right']],
            ]),
        ];
    }

    private function signatureComponent(string $type, string $name, string $category, string $icon, array $fields): array
    {
        $dataKeys = ['searchType', 'source', 'manual_ids', 'destination_id', 'continent', 'travelType', 'idealMonth', 'limit', 'sort'];
        $layoutKeys = ['columns', 'gap', 'imagePosition', 'contentAlign', 'minHeight', 'align'];
        $styleKeys = [
            'backgroundImage', 'backgroundColor', 'backgroundPosition', 'overlayOpacity', 'objectFit',
            'variant', 'cardVariant', 'imageRatio', 'sectionTone', 'showImage', 'showTitle', 'showDescription',
            'showLocation', 'showTravelTypes', 'showDestination', 'showPrice', 'showDuration', 'showCta',
        ];
        $tabs = [
            'content' => ['title' => 'Content', 'fields' => []],
            'data' => ['title' => 'Data', 'fields' => []],
            'layout' => ['title' => 'Layout', 'fields' => []],
            'style' => ['title' => 'Style', 'fields' => []],
        ];

        foreach ($fields as $key => $field) {
            $tab = in_array($key, $dataKeys, true) ? 'data'
                : (in_array($key, $layoutKeys, true) ? 'layout'
                    : (in_array($key, $styleKeys, true) ? 'style' : 'content'));
            $tabs[$tab]['fields'][$key] = $field;
        }

        $tabs = array_filter($tabs, fn (array $tab) => $tab['fields'] !== []);
        $tabs['responsive'] = [
            'title' => 'Responsive',
            'fields' => [
                'hideOnMobile' => ['type' => 'toggle', 'label' => 'Hide on Mobile', 'default' => 'no'],
                'hideOnTablet' => ['type' => 'toggle', 'label' => 'Hide on Tablet', 'default' => 'no'],
            ],
        ];

        return [
            'type' => $type,
            'name' => $name,
            'category' => $category,
            'icon' => $icon,
            'schema_json' => [
                'props' => $this->propsFromFields($fields),
                'tabs' => $tabs,
            ],
        ];
    }

    private function propsFromFields(array $fields): array
    {
        return collect($fields)->map(function (array $field) {
            $type = match ($field['type'] ?? 'text') {
                'number', 'range' => 'number',
                'color' => 'color',
                'media' => 'image',
                'entity-multiselect' => 'array',
                'repeater' => 'text_or_array',
                default => 'text',
            };

            $default = $field['default'] ?? null;
            if ($type === 'number' && $default === '') {
                $default = null;
            }

            return array_filter([
                'type' => $type,
                'default' => $default,
            ], fn ($value) => $value !== null);
        })->all();
    }

    private function catalogComponents(): array
    {
        $continents = ['', 'africa', 'asia', 'europe', 'north-america', 'south-america', 'oceania', 'antarctica'];
        $travelTypes = ['', 'beach', 'mountain', 'cultural', 'adventure', 'city', 'desert', 'nature', 'wellness', 'family'];
        $destinationSort = ['latest', 'oldest', 'name'];
        $offerSort = [...$destinationSort, 'price_low', 'price_high'];
        $cardFields = [
            'columns' => ['type' => 'range', 'label' => 'Columns', 'min' => 1, 'max' => 4, 'default' => 3],
            'gap' => ['type' => 'range', 'label' => 'Gap', 'min' => 8, 'max' => 64, 'default' => 24],
            'imageRatio' => ['type' => 'select', 'label' => 'Image Ratio', 'default' => '16/9', 'options' => ['square', '4/3', '16/9']],
            'showImage' => ['type' => 'toggle', 'label' => 'Show Image', 'default' => 'yes'],
            'showTitle' => ['type' => 'toggle', 'label' => 'Show Title', 'default' => 'yes'],
            'showDescription' => ['type' => 'toggle', 'label' => 'Show Description', 'default' => 'yes'],
            'showCta' => ['type' => 'toggle', 'label' => 'Show CTA', 'default' => 'yes'],
            'buttonText' => ['type' => 'text', 'label' => 'CTA Text', 'default' => 'View details'],
        ];
        $destinationCardFields = $cardFields + [
            'showLocation' => ['type' => 'toggle', 'label' => 'Show Location', 'default' => 'yes'],
            'showTravelTypes' => ['type' => 'toggle', 'label' => 'Show Travel Types', 'default' => 'yes'],
        ];
        $offerCardFields = $cardFields + [
            'showDestination' => ['type' => 'toggle', 'label' => 'Show Destination', 'default' => 'yes'],
            'showPrice' => ['type' => 'toggle', 'label' => 'Show Price', 'default' => 'yes'],
            'showDuration' => ['type' => 'toggle', 'label' => 'Show Duration', 'default' => 'yes'],
        ];

        $destinationData = [
            'source' => ['type' => 'select', 'label' => 'Source', 'default' => 'latest', 'options' => ['latest', 'featured', 'manual'], 'when' => ['key' => 'catalogMode', 'isNot' => 'yes']],
            'manual_ids' => ['type' => 'entity-multiselect', 'entity' => 'destinations', 'label' => 'Manual Selection', 'default' => [], 'when' => ['all' => [['key' => 'catalogMode', 'isNot' => 'yes'], ['key' => 'source', 'is' => 'manual']]]],
            'continent' => ['type' => 'select', 'label' => 'Continent', 'default' => '', 'options' => $continents, 'when' => ['key' => 'catalogMode', 'isNot' => 'yes']],
            'travelType' => ['type' => 'select', 'label' => 'Travel Type', 'default' => '', 'options' => $travelTypes, 'when' => ['key' => 'catalogMode', 'isNot' => 'yes']],
            'idealMonth' => ['type' => 'number', 'label' => 'Ideal Month', 'min' => 1, 'max' => 12, 'default' => '', 'when' => ['key' => 'catalogMode', 'isNot' => 'yes']],
            'limit' => ['type' => 'range', 'label' => 'Limit', 'min' => 1, 'max' => 12, 'default' => 6, 'when' => ['key' => 'catalogMode', 'isNot' => 'yes']],
            'sort' => ['type' => 'select', 'label' => 'Sort', 'default' => 'latest', 'options' => $destinationSort, 'when' => ['key' => 'catalogMode', 'isNot' => 'yes']],
        ];
        $offerData = $destinationData;
        $offerData['source']['options'] = ['latest', 'special', 'manual'];
        $offerData['manual_ids']['entity'] = 'offers';
        $offerData['destination_id'] = ['type' => 'entity-select', 'entity' => 'destinations', 'label' => 'Destination Filter', 'default' => '', 'when' => ['key' => 'catalogMode', 'isNot' => 'yes']];
        $offerData['sort']['options'] = $offerSort;
        $filter = static fn (string $label) => ['type' => 'toggle', 'label' => $label, 'default' => 'yes', 'when' => ['key' => 'catalogMode', 'is' => 'yes']];
        $catalogMode = static fn (string $entity) => [
            'catalogMode' => ['type' => 'toggle', 'label' => 'Catalog Page Mode', 'default' => 'no', 'help' => 'Adds visitor filters, sorting, Grid/List and pagination for a catalog page.'],
            'defaultView' => ['type' => 'select', 'label' => 'Default View', 'default' => 'grid', 'options' => ['grid', 'list'], 'when' => ['key' => 'catalogMode', 'is' => 'yes']],
            'defaultSort' => ['type' => 'select', 'label' => 'Default Sort', 'default' => $entity === 'destinations' ? 'featured' : 'special', 'options' => $entity === 'destinations' ? ['featured', 'latest', 'name_asc', 'name_desc'] : ['special', 'latest', 'price_asc', 'price_desc', 'duration_asc', 'duration_desc', 'name_asc'], 'when' => ['key' => 'catalogMode', 'is' => 'yes']],
            'itemsPerPage' => ['type' => 'select', 'label' => 'Items Per Page', 'default' => '9', 'options' => ['6', '9', '12'], 'when' => ['key' => 'catalogMode', 'is' => 'yes']],
            'showSearchFilter' => $filter('Search Filter'),
            ...($entity === 'destinations' ? [
                'showCountryFilter' => $filter('Country Filter'),
            ] : [
                'showDestinationFilter' => $filter('Destination Filter'),
                'showPriceFilter' => $filter('Price Filter'),
                'showDurationFilter' => $filter('Duration Filter'),
            ]),
            'showContinentFilter' => $filter('Continent Filter'),
            'showTravelTypeFilter' => $filter('Travel Style Filter'),
            'showMonthFilter' => $filter('Month Filter'),
            ...($entity === 'destinations' ? ['showFeaturedFilter' => $filter('Featured Filter')] : ['showSpecialFilter' => $filter('Special Filter')]),
        ];
        $fixedDestinationData = array_diff_key($destinationData, array_flip(['source', 'manual_ids']));
        $fixedOfferData = array_diff_key($offerData, array_flip(['source', 'manual_ids']));
        $sectionContent = static fn (string $eyebrow, string $title, string $intro, string $viewAllLabel) => [
            'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow', 'default' => $eyebrow],
            'title' => ['type' => 'text', 'label' => 'Title', 'default' => $title],
            'intro' => ['type' => 'textarea', 'label' => 'Introduction', 'default' => $intro],
            'showViewAll' => ['type' => 'toggle', 'label' => 'Show View All', 'default' => 'yes'],
            'viewAllLabel' => ['type' => 'text', 'label' => 'View All Label', 'default' => $viewAllLabel],
        ];
        $sectionStyle = [
            'sectionTone' => ['type' => 'select', 'label' => 'Section Tone', 'default' => 'default', 'options' => ['default', 'surface', 'dark']],
        ];

        $make = function (string $type, string $name, string $icon, array $content, array $data = [], array $style = []) {
            $fields = $content + $data + $style;
            return [
                'type' => $type,
                'name' => $name,
                'category' => 'Travel',
                'icon' => $icon,
                'schema_json' => [
                    'props' => $this->propsFromFields($fields),
                    'tabs' => array_filter([
                        'content' => ['title' => 'Content', 'fields' => $content],
                        'data' => ['title' => 'Data', 'fields' => $data],
                        'style' => ['title' => 'Style', 'fields' => $style],
                    ], fn ($tab) => $tab['fields'] !== []),
                ],
            ];
        };

        return [
            $make('destination-grid', 'Destination Grid', 'map', $sectionContent(
                'EXPLORE THE WORLD',
                'Destinations worth the journey',
                'Discover places selected for their character, culture and unforgettable landscapes.',
                'View all destinations'
            ) + [
                'fallbackImage' => ['type' => 'media', 'label' => 'Fallback Image', 'default' => '/images/site-templates/culture-journey.png'],
            ], $catalogMode('destinations') + $destinationData, $destinationCardFields + $sectionStyle + ['cardVariant' => ['type' => 'select', 'label' => 'Card Variant', 'default' => 'standard', 'options' => ['standard', 'compact', 'featured']]]),
            $make('featured-destinations', 'Featured Destinations', 'star', $sectionContent(
                'FEATURED DESTINATIONS',
                'Places that stay with you',
                'A considered selection of remarkable destinations for your next story.',
                'View all destinations'
            ) + [
                'fallbackImage' => ['type' => 'media', 'label' => 'Fallback Image', 'default' => '/images/site-templates/culture-journey.png'],
            ], $fixedDestinationData, $destinationCardFields + $sectionStyle + ['cardVariant' => ['type' => 'select', 'label' => 'Card Variant', 'default' => 'featured', 'options' => ['standard', 'compact', 'featured']]]),
            $make('offer-grid', 'Offer Grid', 'tag', $sectionContent(
                'CURATED JOURNEYS',
                'Journeys designed around discovery',
                'Thoughtful itineraries with room to make the experience entirely your own.',
                'View all journeys'
            ) + [
                'fallbackImage' => ['type' => 'media', 'label' => 'Fallback Image', 'default' => '/images/site-templates/sunset-luxe.png'],
            ], $catalogMode('offers') + $offerData, $offerCardFields + $sectionStyle + ['cardVariant' => ['type' => 'select', 'label' => 'Card Variant', 'default' => 'standard', 'options' => ['standard', 'compact', 'deal']]]),
            $make('special-offers', 'Special Offers', 'sparkles', $sectionContent(
                'LIMITED-TIME INSPIRATION',
                'Journeys worth taking now',
                'Seasonal ideas and signature experiences selected by our travel designers.',
                'View all journeys'
            ) + [
                'fallbackImage' => ['type' => 'media', 'label' => 'Fallback Image', 'default' => '/images/site-templates/sunset-luxe.png'],
            ], $fixedOfferData, $offerCardFields + $sectionStyle + ['cardVariant' => ['type' => 'select', 'label' => 'Card Variant', 'default' => 'deal', 'options' => ['standard', 'compact', 'deal']]]),
            $make('offer-card', 'Offer Card', 'ticket', [
                'offer_id' => ['type' => 'entity-select', 'entity' => 'offers', 'label' => 'Offer', 'default' => ''],
                'fallbackImage' => ['type' => 'media', 'label' => 'Fallback Image', 'default' => '/images/site-templates/sunset-luxe.png'],
                'title' => ['type' => 'text', 'label' => 'Fallback Title', 'default' => 'Offer title'],
                'description' => ['type' => 'textarea', 'label' => 'Fallback Description', 'default' => ''],
                'price' => ['type' => 'number', 'label' => 'Fallback Price', 'default' => ''],
                'buttonText' => ['type' => 'text', 'label' => 'CTA Text', 'default' => 'View offer'],
            ], [], $offerCardFields + ['cardVariant' => ['type' => 'select', 'label' => 'Card Variant', 'default' => 'standard', 'options' => ['standard', 'compact', 'deal']]]),
        ];
    }
}
