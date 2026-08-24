const BuilderControlOptions = {
    alignment: [
        { value: 'left', label: 'Left' },
        { value: 'center', label: 'Center' },
        { value: 'right', label: 'Right' }
    ],
    yesNo: [
        { value: 'yes', label: 'Yes' },
        { value: 'no', label: 'No' }
    ],
    linkType: [
        { value: 'none', label: 'None' },
        { value: 'external', label: 'External URL' },
        { value: 'email', label: 'Email' },
        { value: 'phone', label: 'Phone' },
        { value: 'anchor', label: 'Section anchor' }
    ],
    icon: [
        { value: 'star', label: 'Star' },
        { value: 'map-pin', label: 'Map Pin' },
        { value: 'phone', label: 'Phone' },
        { value: 'envelope', label: 'Envelope' },
        { value: 'globe', label: 'Globe' },
        { value: 'compass', label: 'Compass' },
        { value: 'heart', label: 'Heart' },
        { value: 'camera', label: 'Camera' },
        { value: 'check-circle', label: 'Check Circle' },
        { value: 'sparkles', label: 'Sparkles' }
    ],
    source: [
        { value: 'latest', label: 'Latest' },
        { value: 'featured', label: 'Featured' },
        { value: 'special', label: 'Special' },
        { value: 'by_destination', label: 'By destination' },
        { value: 'manual', label: 'Manual' }
    ],
    sort: [
        { value: 'latest', label: 'Newest first' },
        { value: 'oldest', label: 'Oldest first' },
        { value: 'name', label: 'Name A-Z' },
        { value: 'price_low', label: 'Price low to high' },
        { value: 'price_high', label: 'Price high to low' }
    ]
};

function linkFields() {
    return {
        linkType: {
            type: 'select',
            label: 'Link Type',
            default: 'none',
            options: BuilderControlOptions.linkType
        },
        url: {
            type: 'text',
            label: 'URL',
            default: '#',
            help: 'Use a full URL or an internal path.',
            when: { key: 'linkType', is: 'external' }
        },
        email: {
            type: 'text',
            label: 'Email',
            default: '',
            when: { key: 'linkType', is: 'email' }
        },
        phone: {
            type: 'text',
            label: 'Phone',
            default: '',
            when: { key: 'linkType', is: 'phone' }
        },
        anchor: {
            type: 'text',
            label: 'Anchor ID',
            default: '',
            help: 'Example: contact, offers, destinations.',
            when: { key: 'linkType', is: 'anchor' }
        },
        target: {
            type: 'select',
            label: 'Open In',
            default: 'same-tab',
            options: [
                { value: 'same-tab', label: 'Same tab' },
                { value: 'new-tab', label: 'New tab' }
            ],
            when: { key: 'linkType', in: ['external', 'anchor'] }
        }
    };
}

function typographyFields(colorKey = 'color') {
    return {
        [colorKey]: {
            type: 'color',
            label: 'Text Color',
            default: '#111827'
        },
        fontSize: {
            type: 'range',
            label: 'Font Size',
            min: 10,
            max: 80,
            default: 16
        },
        fontWeight: {
            type: 'range',
            label: 'Font Weight',
            min: 100,
            max: 900,
            default: 400
        },
        lineHeight: {
            type: 'range',
            label: 'Line Height',
            min: 1,
            max: 3,
            step: 0.05,
            default: 1.6
        },
        align: {
            type: 'select',
            label: 'Alignment',
            default: 'left',
            options: BuilderControlOptions.alignment
        }
    };
}

function spacingFields(defaultPadding = 0) {
    return {
        padding: {
            type: 'range',
            label: 'Padding',
            min: 0,
            max: 120,
            default: defaultPadding
        },
        marginTop: {
            type: 'number',
            label: 'Margin Top',
            min: -200,
            max: 200,
            default: ''
        },
        marginBottom: {
            type: 'number',
            label: 'Margin Bottom',
            min: -200,
            max: 200,
            default: ''
        }
    };
}

function surfaceFields() {
    return {
        backgroundColor: {
            type: 'color',
            label: 'Background',
            default: '#ffffff'
        },
        borderColor: {
            type: 'color',
            label: 'Border Color',
            default: '#e5e7eb'
        },
        borderRadius: {
            type: 'range',
            label: 'Radius',
            min: 0,
            max: 80,
            default: 14
        },
        boxShadow: {
            type: 'select',
            label: 'Shadow',
            default: '',
            options: [
                { value: '', label: 'Theme default' },
                { value: 'none', label: 'None' },
                { value: '0 10px 30px rgba(15,23,42,.08)', label: 'Soft' },
                { value: '0 16px 45px rgba(15,23,42,.16)', label: 'Medium' }
            ]
        }
    };
}

function mediaFields(sourceKey = 'src') {
    return {
        [sourceKey]: {
            type: 'media',
            label: 'Media URL',
            default: '',
            help: 'Paste a media library URL or external image URL.'
        },
        altText: {
            type: 'text',
            label: 'Alt Text',
            default: ''
        },
        title: {
            type: 'text',
            label: 'Title',
            default: ''
        },
        caption: {
            type: 'text',
            label: 'Caption',
            default: ''
        }
    };
}

function dataSourceFields(sourceDefault = 'latest') {
    return {
        source: {
            type: 'select',
            label: 'Source',
            default: sourceDefault,
            options: BuilderControlOptions.source
        },
        destination_id: {
            type: 'text',
            label: 'Destination ID',
            default: '',
            help: 'Used only when source is By destination.',
            when: { key: 'source', is: 'by_destination' }
        },
        limit: {
            type: 'range',
            label: 'Limit',
            min: 1,
            max: 12,
            default: 6
        },
        sort: {
            type: 'select',
            label: 'Sort',
            default: 'latest',
            options: BuilderControlOptions.sort
        }
    };
}

function cardPartsFields() {
    return {
        showImage: {
            type: 'toggle',
            label: 'Show Image',
            default: 'yes'
        },
        showTitle: {
            type: 'toggle',
            label: 'Show Title',
            default: 'yes'
        },
        showDescription: {
            type: 'toggle',
            label: 'Show Description',
            default: 'yes'
        },
        showMeta: {
            type: 'toggle',
            label: 'Show Meta',
            default: 'yes'
        },
        showCta: {
            type: 'toggle',
            label: 'Show CTA',
            default: 'yes'
        },
        buttonText: {
            type: 'text',
            label: 'CTA Text',
            default: 'View details',
            when: { key: 'showCta', isNot: 'no' }
        }
    };
}

function advancedFields() {
    return {
        anchorId: {
            type: 'text',
            label: 'Anchor ID',
            default: ''
        },
        customClass: {
            type: 'text',
            label: 'Custom Class',
            default: ''
        },
        ariaLabel: {
            type: 'text',
            label: 'Accessibility Label',
            default: ''
        },
        hideOnMobile: {
            type: 'toggle',
            label: 'Hide On Mobile',
            default: 'no'
        }
    };
}

function tabs(definition) {
    return {
        tabs: definition
    };
}

const BuilderControlSchemaMap = {
    heading: tabs({
        content: {
            title: 'Content',
            fields: {
                text: { type: 'text', label: 'Text', default: 'Your Heading Here' },
                tag: {
                    type: 'select',
                    label: 'Heading Level',
                    default: 'h2',
                    options: ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']
                },
                ...linkFields()
            }
        },
        style: {
            title: 'Style',
            fields: typographyFields('color')
        },
        layout: {
            title: 'Layout',
            fields: spacingFields(0)
        },
        advanced: {
            title: 'Advanced',
            fields: advancedFields()
        }
    }),

    text: tabs({
        content: {
            title: 'Content',
            fields: {
                text: { type: 'textarea', label: 'Text', default: 'Your text here.' }
            }
        },
        style: {
            title: 'Style',
            fields: typographyFields('color')
        },
        layout: {
            title: 'Layout',
            fields: spacingFields(16)
        },
        advanced: {
            title: 'Advanced',
            fields: advancedFields()
        }
    }),

    richtext: tabs({
        content: {
            title: 'Content',
            fields: {
                html: { type: 'richtext', label: 'RichText', default: '<p>Your rich text here.</p>' }
            }
        },
        style: {
            title: 'Style',
            fields: typographyFields('color')
        },
        layout: {
            title: 'Layout',
            fields: spacingFields(16)
        },
        advanced: {
            title: 'Advanced',
            fields: advancedFields()
        }
    }),

    button: tabs({
        content: {
            title: 'Content',
            fields: {
                text: { type: 'text', label: 'Button Text', default: 'Click Me' },
                ...linkFields(),
                icon: {
                    type: 'select',
                    label: 'Icon',
                    default: '',
                    options: [{ value: '', label: 'None' }, ...BuilderControlOptions.icon]
                },
                iconPosition: {
                    type: 'select',
                    label: 'Icon Position',
                    default: 'before',
                    options: [
                        { value: 'before', label: 'Before text' },
                        { value: 'after', label: 'After text' }
                    ],
                    when: { key: 'icon', isNot: '' }
                }
            }
        },
        style: {
            title: 'Style',
            fields: {
                variant: {
                    type: 'select',
                    label: 'Variant',
                    default: 'solid',
                    options: [
                        { value: 'solid', label: 'Solid' },
                        { value: 'outline', label: 'Outline' },
                        { value: 'ghost', label: 'Ghost' }
                    ]
                },
                backgroundColor: { type: 'color', label: 'Background', default: '#2563eb' },
                textColor: { type: 'color', label: 'Text Color', default: '#ffffff' },
                hoverBackgroundColor: { type: 'color', label: 'Hover Background', default: '#1d4ed8' },
                hoverTextColor: { type: 'color', label: 'Hover Text', default: '#ffffff' },
                borderRadius: { type: 'range', label: 'Radius', min: 0, max: 80, default: 14 }
            }
        },
        layout: {
            title: 'Layout',
            fields: {
                align: { type: 'select', label: 'Alignment', default: 'left', options: BuilderControlOptions.alignment },
                size: {
                    type: 'select',
                    label: 'Size',
                    default: 'md',
                    options: [
                        { value: 'sm', label: 'Small' },
                        { value: 'md', label: 'Medium' },
                        { value: 'lg', label: 'Large' }
                    ]
                },
                width: {
                    type: 'select',
                    label: 'Width',
                    default: 'auto',
                    options: [
                        { value: 'auto', label: 'Auto' },
                        { value: 'full', label: 'Full width' }
                    ]
                },
                ...spacingFields(16)
            }
        },
        advanced: {
            title: 'Advanced',
            fields: advancedFields()
        }
    }),

    link: tabs({
        content: {
            title: 'Content',
            fields: {
                text: { type: 'text', label: 'Text', default: 'Link text' },
                ...linkFields()
            }
        },
        style: {
            title: 'Style',
            fields: {
                ...typographyFields('color'),
                underline: {
                    type: 'select',
                    label: 'Underline',
                    default: 'yes',
                    options: BuilderControlOptions.yesNo
                },
                hoverColor: {
                    type: 'color',
                    label: 'Hover Color',
                    default: '#1d4ed8'
                }
            }
        },
        layout: {
            title: 'Layout',
            fields: spacingFields(16)
        },
        advanced: {
            title: 'Advanced',
            fields: advancedFields()
        }
    }),

    image: tabs({
        content: {
            title: 'Content',
            fields: {
                ...mediaFields('src'),
                ...linkFields()
            }
        },
        style: {
            title: 'Style',
            fields: {
                objectFit: {
                    type: 'select',
                    label: 'Object Fit',
                    default: 'cover',
                    options: ['cover', 'contain', 'fill', 'none']
                },
                height: { type: 'range', label: 'Height', min: 80, max: 720, default: 256 },
                borderRadius: { type: 'range', label: 'Radius', min: 0, max: 80, default: 12 },
                backgroundColor: { type: 'color', label: 'Placeholder Background', default: '#f3f4f6' },
                borderColor: { type: 'color', label: 'Placeholder Border', default: '#d1d5db' }
            }
        },
        layout: {
            title: 'Layout',
            fields: spacingFields(16)
        },
        advanced: {
            title: 'Advanced',
            fields: advancedFields()
        }
    }),

    icon: tabs({
        content: {
            title: 'Content',
            fields: {
                icon: { type: 'select', label: 'Icon', default: 'star', options: BuilderControlOptions.icon },
                ...linkFields()
            }
        },
        style: {
            title: 'Style',
            fields: {
                size: { type: 'range', label: 'Size', min: 16, max: 120, default: 40 },
                color: { type: 'color', label: 'Icon Color', default: '#2563eb' },
                backgroundColor: { type: 'color', label: 'Background', default: '#ffffff' },
                padding: { type: 'range', label: 'Padding', min: 0, max: 60, default: 0 },
                borderRadius: { type: 'range', label: 'Radius', min: 0, max: 80, default: 0 }
            }
        },
        layout: {
            title: 'Layout',
            fields: {
                align: { type: 'select', label: 'Alignment', default: 'left', options: BuilderControlOptions.alignment }
            }
        },
        advanced: {
            title: 'Advanced',
            fields: advancedFields()
        }
    }),

    'icon-text': tabs({
        content: {
            title: 'Content',
            fields: {
                icon: { type: 'select', label: 'Icon', default: 'map-pin', options: BuilderControlOptions.icon },
                title: { type: 'text', label: 'Title', default: 'Icon title' },
                text: { type: 'textarea', label: 'Text', default: 'Short supporting text.' },
                ...linkFields()
            }
        },
        style: {
            title: 'Style',
            fields: {
                size: { type: 'range', label: 'Icon Size', min: 16, max: 96, default: 36 },
                iconColor: { type: 'color', label: 'Icon Color', default: '#2563eb' },
                titleColor: { type: 'color', label: 'Title Color', default: '#111827' },
                textColor: { type: 'color', label: 'Text Color', default: '#64748b' }
            }
        },
        layout: {
            title: 'Layout',
            fields: {
                layout: {
                    type: 'select',
                    label: 'Direction',
                    default: 'horizontal',
                    options: ['horizontal', 'vertical']
                },
                align: { type: 'select', label: 'Alignment', default: 'left', options: BuilderControlOptions.alignment },
                gap: { type: 'range', label: 'Gap', min: 0, max: 64, default: 12 },
                ...spacingFields(16)
            }
        },
        advanced: {
            title: 'Advanced',
            fields: advancedFields()
        }
    }),

    gallery: tabs({
        content: {
            title: 'Content',
            fields: {
                images: {
                    type: 'textarea',
                    label: 'Images',
                    default: '',
                    help: 'One image per line: URL|Alt text.'
                },
                lightbox: { type: 'toggle', label: 'Lightbox', default: 'yes' },
                showCaptions: { type: 'toggle', label: 'Show Captions', default: 'no' }
            }
        },
        style: {
            title: 'Style',
            fields: {
                layout: {
                    type: 'select',
                    label: 'Layout',
                    default: 'grid',
                    options: ['grid', 'masonry', 'carousel']
                },
                columns: { type: 'range', label: 'Columns', min: 1, max: 6, default: 3 },
                gap: { type: 'range', label: 'Gap', min: 0, max: 64, default: 16 },
                height: { type: 'range', label: 'Image Height', min: 80, max: 720, default: 220 },
                borderRadius: { type: 'range', label: 'Radius', min: 0, max: 80, default: 12 }
            }
        },
        layout: {
            title: 'Layout',
            fields: spacingFields(16)
        },
        advanced: {
            title: 'Advanced',
            fields: advancedFields()
        }
    }),

    video: tabs({
        content: {
            title: 'Content',
            fields: {
                sourceType: {
                    type: 'select',
                    label: 'Source',
                    default: 'url',
                    options: ['url', 'embed', 'upload']
                },
                url: { type: 'text', label: 'Video URL', default: '' },
                title: { type: 'text', label: 'Title', default: 'Video' },
                poster: { type: 'media', label: 'Poster URL', default: '' }
            }
        },
        style: {
            title: 'Style',
            fields: {
                height: { type: 'range', label: 'Height', min: 160, max: 720, default: 360 },
                borderRadius: { type: 'range', label: 'Radius', min: 0, max: 48, default: 12 },
                controls: { type: 'toggle', label: 'Controls', default: 'yes' },
                autoplay: { type: 'toggle', label: 'Autoplay', default: 'no' },
                muted: { type: 'toggle', label: 'Muted', default: 'no' },
                loop: { type: 'toggle', label: 'Loop', default: 'no' }
            }
        },
        layout: {
            title: 'Layout',
            fields: spacingFields(16)
        },
        advanced: {
            title: 'Advanced',
            fields: advancedFields()
        }
    }),

    iframe: tabs({
        content: {
            title: 'Content',
            fields: {
                url: { type: 'text', label: 'Embed URL', default: '' },
                title: { type: 'text', label: 'Title', default: 'Embedded content' }
            }
        },
        style: {
            title: 'Style',
            fields: {
                height: { type: 'range', label: 'Height', min: 160, max: 900, default: 420 },
                borderRadius: { type: 'range', label: 'Radius', min: 0, max: 48, default: 12 },
                allowFullscreen: { type: 'toggle', label: 'Fullscreen', default: 'yes' }
            }
        },
        layout: {
            title: 'Layout',
            fields: spacingFields(16)
        },
        advanced: {
            title: 'Advanced',
            fields: {
                sandbox: {
                    type: 'toggle',
                    label: 'Sandbox',
                    default: 'yes'
                },
                ...advancedFields()
            }
        }
    }),

    map: tabs({
        content: {
            title: 'Content',
            fields: {
                address: { type: 'text', label: 'Address', default: 'Marrakech, Morocco' },
                embedUrl: { type: 'text', label: 'Custom Embed URL', default: '' },
                markerLabel: { type: 'text', label: 'Marker Label', default: '' },
                showDirections: { type: 'toggle', label: 'Directions Link', default: 'yes' }
            }
        },
        style: {
            title: 'Style',
            fields: {
                height: { type: 'range', label: 'Height', min: 160, max: 720, default: 360 },
                zoom: { type: 'range', label: 'Zoom', min: 1, max: 20, default: 12 },
                borderRadius: { type: 'range', label: 'Radius', min: 0, max: 48, default: 12 }
            }
        },
        layout: {
            title: 'Layout',
            fields: spacingFields(16)
        },
        advanced: {
            title: 'Advanced',
            fields: advancedFields()
        }
    }),

    'contact-form': tabs({
        content: {
            title: 'Content',
            fields: {
                title: { type: 'text', label: 'Title', default: 'Contact us' },
                subtitle: { type: 'textarea', label: 'Subtitle', default: 'Send us a message and we will reply soon.' },
                nameLabel: { type: 'text', label: 'Name Label', default: 'Name' },
                emailLabel: { type: 'text', label: 'Email Label', default: 'Email' },
                messageLabel: { type: 'text', label: 'Message Label', default: 'Message' },
                buttonText: { type: 'text', label: 'Button Text', default: 'Send message' },
                recipientEmail: { type: 'text', label: 'Recipient Email', default: '' },
                actionUrl: { type: 'text', label: 'Action URL', default: '' },
                successMessage: { type: 'text', label: 'Success Message', default: 'Thank you. We will contact you soon.' },
                consentText: { type: 'text', label: 'Consent Text', default: '' }
            }
        },
        style: {
            title: 'Style',
            fields: {
                textColor: { type: 'color', label: 'Text Color', default: '#111827' },
                buttonColor: { type: 'color', label: 'Button Color', default: '#2563eb' },
                ...surfaceFields()
            }
        },
        layout: {
            title: 'Layout',
            fields: spacingFields(16)
        },
        advanced: {
            title: 'Advanced',
            fields: advancedFields()
        }
    }),

    faq: tabs({
        content: {
            title: 'Content',
            fields: {
                title: { type: 'text', label: 'Title', default: 'Frequently asked questions' },
                items: {
                    type: 'textarea',
                    label: 'Questions',
                    default: 'What is included?|Flights, hotels, transfers, and guided activities can be included depending on the offer.',
                    help: 'One FAQ per line: Question|Answer.'
                },
                allowMultiple: { type: 'toggle', label: 'Allow Multiple Open', default: 'yes' },
                schemaEnabled: { type: 'toggle', label: 'FAQ Schema', default: 'no' }
            }
        },
        style: {
            title: 'Style',
            fields: {
                iconStyle: {
                    type: 'select',
                    label: 'Icon Style',
                    default: 'plus',
                    options: ['plus', 'chevron', 'none']
                },
                ...surfaceFields()
            }
        },
        layout: {
            title: 'Layout',
            fields: spacingFields(16)
        },
        advanced: {
            title: 'Advanced',
            fields: advancedFields()
        }
    }),

    countdown: tabs({
        content: {
            title: 'Content',
            fields: {
                label: { type: 'text', label: 'Label', default: 'Offer ends in' },
                targetDate: { type: 'text', label: 'Target Date', default: '2026-12-31T23:59', help: 'Format: YYYY-MM-DDTHH:mm' },
                timezone: { type: 'text', label: 'Timezone', default: 'Africa/Casablanca' },
                expiredText: { type: 'text', label: 'Expired Text', default: 'Offer expired' }
            }
        },
        style: {
            title: 'Style',
            fields: {
                backgroundColor: { type: 'color', label: 'Background', default: '#111827' },
                textColor: { type: 'color', label: 'Text Color', default: '#ffffff' },
                accentColor: { type: 'color', label: 'Accent Color', default: '#38bdf8' }
            }
        },
        layout: {
            title: 'Layout',
            fields: {
                showDays: { type: 'toggle', label: 'Show Days', default: 'yes' },
                showHours: { type: 'toggle', label: 'Show Hours', default: 'yes' },
                showMinutes: { type: 'toggle', label: 'Show Minutes', default: 'yes' },
                showSeconds: { type: 'toggle', label: 'Show Seconds', default: 'yes' },
                ...spacingFields(16)
            }
        },
        advanced: {
            title: 'Advanced',
            fields: advancedFields()
        }
    }),

    hero: tabs({
        content: {
            title: 'Content',
            fields: {
                title: { type: 'text', label: 'Title', default: 'Hero Title' },
                description: { type: 'textarea', label: 'Subtitle', default: 'Hero description here.' },
                buttonText: { type: 'text', label: 'Primary Button', default: '' },
                ...linkFields()
            }
        },
        style: {
            title: 'Style',
            fields: {
                backgroundMode: {
                    type: 'select',
                    label: 'Background Type',
                    default: 'color',
                    options: ['color', 'image', 'video']
                },
                backgroundColor: { type: 'color', label: 'Background', default: '#111827' },
                backgroundImage: { type: 'media', label: 'Background Image', default: '', when: { key: 'backgroundMode', is: 'image' } },
                textColor: { type: 'color', label: 'Text Color', default: '#ffffff' }
            }
        },
        layout: {
            title: 'Layout',
            fields: {
                variant: {
                    type: 'select',
                    label: 'Variant',
                    default: 'left',
                    options: ['left', 'center', 'split']
                },
                ...spacingFields(64)
            }
        },
        advanced: {
            title: 'Advanced',
            fields: advancedFields()
        }
    }),

    'destination-grid': tabs({
        content: {
            title: 'Content',
            fields: {
                title: { type: 'text', label: 'Title', default: 'Destinations' },
                fallbackImage: { type: 'media', label: 'Fallback Image', default: '/images/site-templates/culture-journey.png' },
                ...dataSourceFields('latest')
            }
        },
        style: {
            title: 'Style',
            fields: {
                cardVariant: { type: 'select', label: 'Card Variant', default: 'standard', options: ['standard', 'compact', 'featured'] },
                columns: { type: 'range', label: 'Columns', min: 1, max: 4, default: 3 },
                gap: { type: 'range', label: 'Gap', min: 8, max: 48, default: 24 },
                ...cardPartsFields()
            }
        },
        layout: {
            title: 'Layout',
            fields: spacingFields(40)
        },
        advanced: {
            title: 'Advanced',
            fields: advancedFields()
        }
    }),

    'featured-destinations': tabs({
        content: {
            title: 'Content',
            fields: {
                title: { type: 'text', label: 'Title', default: 'Featured Destinations' },
                fallbackImage: { type: 'media', label: 'Fallback Image', default: '/images/site-templates/culture-journey.png' },
                limit: { type: 'range', label: 'Limit', min: 1, max: 12, default: 6 },
                sort: { type: 'select', label: 'Sort', default: 'latest', options: BuilderControlOptions.sort }
            }
        },
        style: {
            title: 'Style',
            fields: {
                columns: { type: 'range', label: 'Columns', min: 1, max: 4, default: 3 },
                gap: { type: 'range', label: 'Gap', min: 8, max: 48, default: 24 },
                ...cardPartsFields()
            }
        },
        layout: {
            title: 'Layout',
            fields: spacingFields(40)
        },
        advanced: {
            title: 'Advanced',
            fields: advancedFields()
        }
    }),

    'offer-grid': tabs({
        content: {
            title: 'Content',
            fields: {
                title: { type: 'text', label: 'Title', default: 'Offers' },
                fallbackImage: { type: 'media', label: 'Fallback Image', default: '/images/site-templates/sunset-luxe.png' },
                ...dataSourceFields('latest')
            }
        },
        style: {
            title: 'Style',
            fields: {
                cardVariant: { type: 'select', label: 'Card Variant', default: 'standard', options: ['standard', 'compact', 'deal'] },
                columns: { type: 'range', label: 'Columns', min: 1, max: 4, default: 3 },
                gap: { type: 'range', label: 'Gap', min: 8, max: 48, default: 24 },
                showPrice: { type: 'toggle', label: 'Show Price', default: 'yes' },
                showDuration: { type: 'toggle', label: 'Show Duration', default: 'yes' },
                ...cardPartsFields()
            }
        },
        layout: {
            title: 'Layout',
            fields: spacingFields(40)
        },
        advanced: {
            title: 'Advanced',
            fields: advancedFields()
        }
    }),

    'special-offers': tabs({
        content: {
            title: 'Content',
            fields: {
                title: { type: 'text', label: 'Title', default: 'Special Offers' },
                fallbackImage: { type: 'media', label: 'Fallback Image', default: '/images/site-templates/sunset-luxe.png' },
                limit: { type: 'range', label: 'Limit', min: 1, max: 12, default: 6 },
                sort: { type: 'select', label: 'Sort', default: 'latest', options: BuilderControlOptions.sort }
            }
        },
        style: {
            title: 'Style',
            fields: {
                columns: { type: 'range', label: 'Columns', min: 1, max: 4, default: 3 },
                gap: { type: 'range', label: 'Gap', min: 8, max: 48, default: 24 },
                showPrice: { type: 'toggle', label: 'Show Price', default: 'yes' },
                showDuration: { type: 'toggle', label: 'Show Duration', default: 'yes' },
                ...cardPartsFields()
            }
        },
        layout: {
            title: 'Layout',
            fields: spacingFields(40)
        },
        advanced: {
            title: 'Advanced',
            fields: advancedFields()
        }
    }),

    'offer-card': tabs({
        content: {
            title: 'Content',
            fields: {
                offer_id: { type: 'text', label: 'Offer ID', default: '' },
                fallbackImage: { type: 'media', label: 'Fallback Image', default: '/images/site-templates/sunset-luxe.png' },
                title: { type: 'text', label: 'Fallback Title', default: 'Offer title' },
                description: { type: 'textarea', label: 'Fallback Description', default: '' },
                price: { type: 'number', label: 'Fallback Price', default: '' },
                buttonText: { type: 'text', label: 'CTA Text', default: 'View offer' }
            }
        },
        style: {
            title: 'Style',
            fields: {
                showPrice: { type: 'toggle', label: 'Show Price', default: 'yes' },
                showDuration: { type: 'toggle', label: 'Show Duration', default: 'yes' },
                ...cardPartsFields()
            }
        },
        layout: {
            title: 'Layout',
            fields: spacingFields(16)
        },
        advanced: {
            title: 'Advanced',
            fields: advancedFields()
        }
    })
};

window.BuilderControlSchemas = {
    get(type, fallback = null) {
        return BuilderControlSchemaMap[type] || fallback;
    }
};
