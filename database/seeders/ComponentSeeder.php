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
                        'images' => ['type' => 'text'],
                        'columns' => ['type' => 'number'],
                        'gap' => ['type' => 'number'],
                        'height' => ['type' => 'number'],
                        'borderRadius' => ['type' => 'number'],
                    ],
                    'tabs' => [
                        'content' => [
                            'title' => 'Content',
                            'fields' => [
                                'images' => ['type' => 'textarea', 'label' => 'Images', 'default' => '', 'help' => 'One image per line. Use URL or URL|Alt text.'],
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
                        'address' => ['type' => 'text'],
                        'embedUrl' => ['type' => 'text'],
                        'height' => ['type' => 'number'],
                        'zoom' => ['type' => 'number'],
                        'borderRadius' => ['type' => 'number'],
                    ],
                    'tabs' => [
                        'content' => [
                            'title' => 'Content',
                            'fields' => [
                                'address' => ['type' => 'text', 'label' => 'Address', 'default' => 'Marrakech, Morocco'],
                                'embedUrl' => ['type' => 'text', 'label' => 'Custom Embed URL', 'default' => ''],
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
                        'items' => ['type' => 'text'],
                        'allowMultiple' => ['type' => 'text'],
                    ],
                    'tabs' => [
                        'content' => [
                            'title' => 'Content',
                            'fields' => [
                                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'Frequently asked questions'],
                                'items' => ['type' => 'textarea', 'label' => 'Items', 'default' => "What is included?|Flights, hotels, transfers, and guided activities can be included depending on the offer.\nCan I customize the trip?|Yes. Contact the agency to adapt dates, hotels, and activities."],
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
            ['type' => 'destination-grid', 'name' => 'Destination Grid', 'category' => 'Travel', 'icon' => 'map', 'schema_json' => ['title' => ['type' => 'text', 'label' => 'Title'], 'source' => ['type' => 'text', 'label' => 'Source'], 'limit' => ['type' => 'number', 'label' => 'Limit']]],
            ['type' => 'featured-destinations', 'name' => 'Featured Destinations', 'category' => 'Travel', 'icon' => 'star', 'schema_json' => ['title' => ['type' => 'text', 'label' => 'Title'], 'limit' => ['type' => 'number', 'label' => 'Limit']]],
            ['type' => 'offer-grid', 'name' => 'Offer Grid', 'category' => 'Travel', 'icon' => 'tag', 'schema_json' => ['title' => ['type' => 'text', 'label' => 'Title'], 'source' => ['type' => 'text', 'label' => 'Source'], 'destination_id' => ['type' => 'text', 'label' => 'Destination ID'], 'limit' => ['type' => 'number', 'label' => 'Limit']]],
            ['type' => 'special-offers', 'name' => 'Special Offers', 'category' => 'Travel', 'icon' => 'sparkles', 'schema_json' => ['title' => ['type' => 'text', 'label' => 'Title'], 'limit' => ['type' => 'number', 'label' => 'Limit']]],
            ['type' => 'offer-card', 'name' => 'Offer Card', 'category' => 'Travel', 'icon' => 'ticket', 'schema_json' => ['offer_id' => ['type' => 'text', 'label' => 'Offer ID']]],
        ];

        foreach ($components as $component) {
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
            ]),
            $this->signatureComponent('image-text', 'Image & Text', 'content', 'photo', [
                'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow', 'default' => 'OUR APPROACH'],
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'Travel designed around you'],
                'text' => ['type' => 'textarea', 'label' => 'Text', 'default' => 'We combine local knowledge with personal service to create journeys that feel entirely your own.'],
                'image' => ['type' => 'media', 'label' => 'Image', 'default' => ''],
                'imageAlt' => ['type' => 'text', 'label' => 'Image Alt', 'default' => ''],
                'imagePosition' => ['type' => 'select', 'label' => 'Image Position', 'default' => 'left', 'options' => ['left', 'right']],
                'buttonText' => ['type' => 'text', 'label' => 'Button', 'default' => 'Discover our story'],
                'url' => ['type' => 'text', 'label' => 'URL', 'default' => '/about'],
            ]),
            $this->signatureComponent('feature-grid', 'Feature Grid', 'content', 'squares-2x2', [
                'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow', 'default' => 'WHY TRAVEL WITH US'],
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'Every detail, thoughtfully handled'],
                'items' => ['type' => 'textarea', 'label' => 'Features', 'default' => "compass|Local expertise|Travel with people who know each place deeply.\nheart|Personal service|Every journey is shaped around your interests.\ncheck-circle|Trusted support|We are with you before, during and after your trip."],
                'columns' => ['type' => 'range', 'label' => 'Columns', 'min' => 1, 'max' => 4, 'default' => 3],
            ]),
            $this->signatureComponent('destination-carousel', 'Destination Carousel', 'travel', 'map', [
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'Explore remarkable places'],
                'limit' => ['type' => 'range', 'label' => 'Limit', 'min' => 1, 'max' => 12, 'default' => 6],
                'source' => ['type' => 'select', 'label' => 'Source', 'default' => 'featured', 'options' => ['featured', 'latest']],
            ]),
            $this->signatureComponent('offer-comparison', 'Offer Comparison', 'travel', 'table-cells', [
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'Compare our journeys'],
                'limit' => ['type' => 'range', 'label' => 'Offers', 'min' => 2, 'max' => 4, 'default' => 3],
                'buttonText' => ['type' => 'text', 'label' => 'Button', 'default' => 'View journey'],
            ]),
            $this->signatureComponent('testimonials', 'Testimonials', 'marketing', 'chat-bubble-left-right', [
                'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow', 'default' => 'TRAVELLER STORIES'],
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => 'What our travellers say'],
                'items' => ['type' => 'textarea', 'label' => 'Testimonials', 'default' => "An unforgettable journey from beginning to end.|Amelia R.|London|5\nEvery detail felt personal and effortless.|Daniel M.|Toronto|5\nWe discovered places we would never have found alone.|Sofia K.|Madrid|5"],
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
        $dataKeys = ['searchType', 'source', 'limit'];
        $layoutKeys = ['columns', 'imagePosition', 'align'];
        $styleKeys = ['backgroundImage', 'variant'];
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
                'tabs' => $tabs,
            ],
        ];
    }
}
