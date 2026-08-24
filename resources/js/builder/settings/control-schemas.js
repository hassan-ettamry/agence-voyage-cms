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

function dataSourceFields(sourceDefault = 'latest', entity = 'offers') {
    const isDestination = entity === 'destinations';
    const sourceOptions = BuilderControlOptions.source.filter(({ value }) => (
        isDestination
            ? ['latest', 'featured', 'manual'].includes(value)
            : ['latest', 'special', 'manual'].includes(value)
    ));
    const sortOptions = BuilderControlOptions.sort.filter(({ value }) => (
        !isDestination || !['price_low', 'price_high'].includes(value)
    ));

    return {
        source: {
            type: 'select',
            label: 'Source',
            default: sourceDefault,
            options: sourceOptions
        },
        ...(!isDestination ? { destination_id: {
            type: 'entity-select',
            entity: 'destinations',
            label: 'Destination Filter',
            default: ''
        } } : {}),
        manual_ids: {
            type: 'entity-multiselect',
            entity,
            label: 'Manual Selection',
            default: [],
            help: 'Choose published items from this agency.',
            when: { key: 'source', is: 'manual' }
        },
        continent: {
            type: 'select',
            label: 'Continent',
            default: '',
            options: ['', 'africa', 'asia', 'europe', 'north-america', 'south-america', 'oceania', 'antarctica']
        },
        travelType: {
            type: 'select',
            label: 'Travel Type',
            default: '',
            options: ['', 'beach', 'mountain', 'cultural', 'adventure', 'city', 'desert', 'nature', 'wellness', 'family']
        },
        idealMonth: {
            type: 'number',
            label: 'Ideal Month (1-12)',
            default: '',
            min: 1,
            max: 12
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
            options: sortOptions
        }
    };
}

function fixedSourceFields(source, entity) {
    const { source: ignoredSource, manual_ids: ignoredManual, ...fields } = dataSourceFields(source, entity);
    return fields;
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

function destinationPartsFields() {
    const { showMeta: ignoredMeta, ...parts } = cardPartsFields();
    return {
        ...parts,
        showLocation: {
            type: 'toggle',
            label: 'Show Location',
            default: 'yes'
        },
        showTravelTypes: {
            type: 'toggle',
            label: 'Show Travel Types',
            default: 'yes'
        }
    };
}

function offerPartsFields() {
    const { showMeta: ignoredMeta, ...parts } = cardPartsFields();
    return {
        ...parts,
        showDestination: {
            type: 'toggle',
            label: 'Show Destination',
            default: 'yes'
        },
        showPrice: {
            type: 'toggle',
            label: 'Show Price',
            default: 'yes'
        },
        showDuration: {
            type: 'toggle',
            label: 'Show Duration',
            default: 'yes'
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
                    type: 'repeater',
                    label: 'Images',
                    default: [],
                    maxItems: 24,
                    itemFields: [
                        { key: 'url', label: 'Image URL', type: 'text' },
                        { key: 'alt', label: 'Alt Text', type: 'text' }
                    ],
                    help: 'Add a safe media URL and meaningful alternative text.'
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
                    type: 'repeater',
                    label: 'Questions',
                    default: [{ question: 'What is included?', answer: 'Flights, hotels, transfers, and guided activities can be included depending on the offer.' }],
                    maxItems: 24,
                    itemFields: [
                        { key: 'question', label: 'Question', type: 'text' },
                        { key: 'answer', label: 'Answer', type: 'text' }
                    ]
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
                backgroundPosition: { type: 'select', label: 'Image Position', default: 'center', options: ['center', 'top', 'bottom', 'left', 'right'], when: { key: 'backgroundMode', is: 'image' } },
                overlayOpacity: { type: 'range', label: 'Overlay', min: 0, max: 90, default: 55 },
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
                minHeight: { type: 'range', label: 'Minimum Height', min: 360, max: 900, default: 520 },
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
                fallbackImage: { type: 'media', label: 'Fallback Image', default: '/images/site-templates/culture-journey.png' }
            }
        },
        data: {
            title: 'Data',
            fields: dataSourceFields('latest', 'destinations')
        },
        style: {
            title: 'Style',
            fields: {
                cardVariant: { type: 'select', label: 'Card Variant', default: 'standard', options: ['standard', 'compact', 'featured'] },
                imageRatio: { type: 'select', label: 'Image Ratio', default: '16/9', options: ['square', '4/3', '16/9'] },
                columns: { type: 'range', label: 'Columns', min: 1, max: 4, default: 3 },
                gap: { type: 'range', label: 'Gap', min: 8, max: 48, default: 24 },
                ...destinationPartsFields()
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
                fallbackImage: { type: 'media', label: 'Fallback Image', default: '/images/site-templates/culture-journey.png' }
            }
        },
        data: {
            title: 'Data',
            fields: fixedSourceFields('featured', 'destinations')
        },
        style: {
            title: 'Style',
            fields: {
                cardVariant: { type: 'select', label: 'Card Variant', default: 'featured', options: ['standard', 'compact', 'featured'] },
                imageRatio: { type: 'select', label: 'Image Ratio', default: '4/3', options: ['square', '4/3', '16/9'] },
                columns: { type: 'range', label: 'Columns', min: 1, max: 4, default: 3 },
                gap: { type: 'range', label: 'Gap', min: 8, max: 48, default: 24 },
                ...destinationPartsFields()
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

    'destination-carousel': tabs({
        content: {
            title: 'Content',
            fields: {
                title: { type: 'text', label: 'Title', default: 'Explore remarkable places' },
                fallbackImage: { type: 'media', label: 'Fallback Image', default: '/images/site-templates/culture-journey.png' },
                buttonText: { type: 'text', label: 'CTA Text', default: 'View destination' }
            }
        },
        data: {
            title: 'Data',
            fields: dataSourceFields('featured', 'destinations')
        },
        style: {
            title: 'Style',
            fields: {
                cardVariant: { type: 'select', label: 'Card Variant', default: 'overlay', options: ['overlay', 'compact'] },
                gap: { type: 'range', label: 'Gap', min: 8, max: 48, default: 20 },
                ...destinationPartsFields()
            }
        },
        layout: {
            title: 'Layout',
            fields: spacingFields(64)
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
                fallbackImage: { type: 'media', label: 'Fallback Image', default: '/images/site-templates/sunset-luxe.png' }
            }
        },
        data: {
            title: 'Data',
            fields: dataSourceFields('latest', 'offers')
        },
        style: {
            title: 'Style',
            fields: {
                cardVariant: { type: 'select', label: 'Card Variant', default: 'standard', options: ['standard', 'compact', 'deal'] },
                imageRatio: { type: 'select', label: 'Image Ratio', default: '16/9', options: ['square', '4/3', '16/9'] },
                columns: { type: 'range', label: 'Columns', min: 1, max: 4, default: 3 },
                gap: { type: 'range', label: 'Gap', min: 8, max: 48, default: 24 },
                ...offerPartsFields()
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

    'offer-comparison': tabs({
        content: {
            title: 'Content',
            fields: {
                title: { type: 'text', label: 'Title', default: 'Compare our journeys' },
                fallbackImage: { type: 'media', label: 'Fallback Image', default: '/images/site-templates/sunset-luxe.png' },
                buttonText: { type: 'text', label: 'CTA Text', default: 'View journey' }
            }
        },
        data: {
            title: 'Data',
            fields: {
                ...dataSourceFields('latest', 'offers'),
                limit: { type: 'range', label: 'Offers', min: 2, max: 4, default: 3 }
            }
        },
        style: {
            title: 'Style',
            fields: offerPartsFields()
        },
        layout: {
            title: 'Layout',
            fields: spacingFields(64)
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
                fallbackImage: { type: 'media', label: 'Fallback Image', default: '/images/site-templates/sunset-luxe.png' }
            }
        },
        data: {
            title: 'Data',
            fields: fixedSourceFields('special', 'offers')
        },
        style: {
            title: 'Style',
            fields: {
                cardVariant: { type: 'select', label: 'Card Variant', default: 'deal', options: ['standard', 'compact', 'deal'] },
                imageRatio: { type: 'select', label: 'Image Ratio', default: '16/9', options: ['square', '4/3', '16/9'] },
                columns: { type: 'range', label: 'Columns', min: 1, max: 4, default: 3 },
                gap: { type: 'range', label: 'Gap', min: 8, max: 48, default: 24 },
                ...offerPartsFields()
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
                offer_id: { type: 'entity-select', entity: 'offers', label: 'Offer', default: '' },
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
                cardVariant: { type: 'select', label: 'Card Variant', default: 'standard', options: ['standard', 'compact', 'deal'] },
                imageRatio: { type: 'select', label: 'Image Ratio', default: '16/9', options: ['square', '4/3', '16/9'] },
                ...offerPartsFields()
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
