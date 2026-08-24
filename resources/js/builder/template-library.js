function templateWidget(type, props = {}) {
    return {
        type,
        props,
        children: []
    };
}

const templateThemePropKeys = {
    section: [
        'backgroundColor',
        'textColor',
        'borderColor',
        'borderRadius',
        'boxShadow'
    ],
    container: [
        'backgroundColor',
        'textColor',
        'borderColor',
        'borderRadius',
        'boxShadow'
    ],
    text: ['color', 'textColor'],
    richtext: ['color', 'textColor'],
    heading: ['color', 'textColor'],
    button: [
        'backgroundColor',
        'textColor',
        'borderColor',
        'borderRadius',
        'boxShadow'
    ],
    link: ['color', 'textColor'],
    icon: ['color', 'backgroundColor', 'borderColor'],
    'icon-text': ['color', 'iconColor', 'titleColor', 'textColor', 'backgroundColor', 'borderColor'],
    'contact-form': ['backgroundColor', 'buttonColor', 'textColor', 'borderColor', 'borderRadius', 'boxShadow'],
    faq: ['backgroundColor', 'textColor', 'borderColor', 'borderRadius', 'boxShadow'],
    countdown: ['backgroundColor', 'textColor', 'accentColor', 'borderColor', 'borderRadius', 'boxShadow']
};

function themeReadyTemplateProps(type, props = {}) {
    const nextProps = {
        ...BuilderStructureRules.ensurePlainProps(props)
    };

    (templateThemePropKeys[type] || []).forEach(key => {
        delete nextProps[key];
    });

    return nextProps;
}

function templateText(text, props = {}) {
    return templateWidget('text', {
        text,
        padding: 0,
        ...props
    });
}

function templateButton(text, props = {}) {
    return templateWidget('button', {
        text,
        ...props
    });
}

function templateImage(alt = 'Image', props = {}) {
    return templateWidget('image', {
        alt,
        height: 180,
        backgroundColor: '#f1f5f9',
        borderStyle: 'solid',
        borderColor: 'transparent',
        ...props
    });
}

function templateContainer(role = 'group', props = {}, children = []) {
    return {
        type: 'container',
        props: BuilderContainerRoles.applyCreationDefaults(role, props),
        children
    };
}

function templateSection(props = {}, children = []) {
    return {
        type: 'section',
        props: {
            paddingTop: 72,
            paddingBottom: 72,
            paddingLeft: 20,
            paddingRight: 20,
            backgroundColor: '#ffffff',
            maxWidth: '100%',
            ...props
        },
        children
    };
}

function templateLayout(children = [], props = {}) {
    return templateContainer('layout', props, children);
}

function templateContent(children = [], props = {}) {
    return templateContainer('content', props, children);
}

function templateGrid(children = [], props = {}) {
    return templateContainer('grid', props, children);
}

function templateCard(children = [], props = {}) {
    return templateContainer('card', props, children);
}

function collectionCard(kind = 'destination', index = 0) {
    const destinationNames = [
        'Coastal Escape',
        'City Discovery',
        'Mountain Retreat'
    ];

    const offerNames = [
        'Weekend Deal',
        'Family Package',
        'Luxury Offer'
    ];

    const isOffer =
        kind === 'offer';

    const title =
        isOffer
            ? offerNames[index % offerNames.length]
            : destinationNames[index % destinationNames.length];

    return templateCard([
        templateImage(isOffer ? 'Offer image' : 'Destination image', {
            height: 170,
            backgroundColor: isOffer ? '#e0f2fe' : '#dcfce7'
        }),
        templateText(title, {
            padding: 0,
            fontSize: 20,
            fontWeight: 700,
            color: '#0f172a',
            lineHeight: 1.25
        }),
        templateText(
            isOffer
                ? 'Seasonal package with flexible duration and curated activities.'
                : 'Short destination description for this travel card.',
            {
                padding: 0,
                fontSize: 14,
                color: '#64748b',
                lineHeight: 1.5
            }
        ),
        templateButton(isOffer ? 'View offer' : 'View destination', {
            backgroundColor: '#2563eb',
            textColor: '#ffffff'
        })
    ], {
        gridSpan: 4,
        display: 'flex',
        flexDirection: 'column',
        gap: 14,
        paddingTop: 18,
        paddingBottom: 18,
        paddingLeft: 18,
        paddingRight: 18
    });
}

function collectionTemplateNodes(kind, widgetProps = {}) {
    const isOffer =
        kind === 'offer';

    const title =
        widgetProps.title
        || (isOffer ? 'Special Offers' : 'Featured Destinations');

    const requestedLimit =
        Number(widgetProps.limit || 3);

    const cardCount =
        Math.max(
            1,
            Math.min(
                Number.isFinite(requestedLimit)
                    ? requestedLimit
                    : 3,
                3
            )
        );

    return [
        templateText(title, {
            padding: 0,
            fontSize: 30,
            fontWeight: 800,
            color: '#0f172a',
            lineHeight: 1.15
        }),
        templateGrid(
            Array.from({ length: cardCount }, (_, index) =>
                collectionCard(kind, index)
            ),
            {
                gap: 24,
                gridColumns: 12
            }
        )
    ];
}

function travelHeroSection(title, description, props = {}) {
    return templateSection({
        backgroundColor: props.backgroundColor || '#0f172a',
        paddingTop: props.paddingTop || 84,
        paddingBottom: props.paddingBottom || 84
    }, [
        templateLayout([
            templateGrid([
                templateContent([
                    templateText(title, {
                        color: props.textColor || '#ffffff',
                        fontSize: 48,
                        fontWeight: 800,
                        lineHeight: 1.1
                    }),
                    templateText(description, {
                        color: props.mutedTextColor || '#dbeafe',
                        fontSize: 19,
                        lineHeight: 1.55
                    }),
                    templateButton(props.buttonText || 'Explore trips', {
                        backgroundColor: props.buttonColor || '#2563eb',
                        textColor: '#ffffff'
                    })
                ], {
                    gridSpan: 7,
                    gap: 18,
                    justifyContent: 'center',
                    maxWidth: 680
                }),
                templateCard([
                    templateWidget('image', {
                        alt: props.imageAlt || 'Travel preview',
                        height: 280,
                        backgroundColor: props.imageBackground || '#e0f2fe',
                        borderStyle: 'solid',
                        borderColor: 'transparent'
                    })
                ], {
                    gridSpan: 5,
                    paddingTop: 12,
                    paddingBottom: 12,
                    paddingLeft: 12,
                    paddingRight: 12,
                    backgroundColor: props.cardColor || '#ffffff',
                    borderColor: 'rgba(255, 255, 255, 0.35)',
                    boxShadow: '0 22px 50px rgba(15, 23, 42, 0.28)'
                })
            ], {
                gap: 32,
                alignItems: 'center'
            })
        ], {
            maxWidth: 1180
        })
    ]);
}

function dynamicContentSection(widgetType, widgetProps, sectionProps = {}) {
    const kind = [
        'offer-grid',
        'special-offers',
        'offer-card'
    ].includes(widgetType)
        ? 'offer'
        : 'destination';

    return templateSection({
        paddingTop: 64,
        paddingBottom: 64,
        backgroundColor: sectionProps.backgroundColor || '#ffffff'
    }, [
        templateLayout([
            templateContent(collectionTemplateNodes(kind, widgetProps), {
                maxWidth: '100%',
                gap: 28,
                minHeight: 160
            })
        ], {
            maxWidth: sectionProps.maxWidth || 1180
        })
    ]);
}

function centeredContentSection(children, sectionProps = {}) {
    return templateSection({
        backgroundColor: sectionProps.backgroundColor || '#f8fafc',
        paddingTop: sectionProps.paddingTop || 72,
        paddingBottom: sectionProps.paddingBottom || 72
    }, [
        templateLayout([
            templateContent(children, {
                maxWidth: sectionProps.contentMaxWidth || 760,
                gap: sectionProps.gap || 18,
                alignItems: 'center'
            })
        ], {
            maxWidth: sectionProps.layoutMaxWidth || 900
        })
    ]);
}

window.BuilderTemplateLibrary = {

    activeTab: 'blocks',

    activeCategory: 'all',

    search: '',

    categories: [
        { value: 'all', label: 'All' },
        { value: 'agency', label: 'Agency' },
        { value: 'destinations', label: 'Destinations' },
        { value: 'offers', label: 'Offers' },
        { value: 'landing-page', label: 'Landing Page' },
        { value: 'beach-trips', label: 'Beach Trips' },
        { value: 'city-tours', label: 'City Tours' },
        { value: 'luxury-travel', label: 'Luxury Travel' },
        { value: 'honeymoon', label: 'Honeymoon' },
        { value: 'family-travel', label: 'Family Travel' },
        { value: 'adventure', label: 'Adventure' },
        { value: 'pilgrimage', label: 'Pilgrimage' },
        { value: 'weekend', label: 'Weekend' },
        { value: 'thank-you', label: 'Thank you' }
    ],

    templates: [
        {
            id: 'travel-hero',
            tab: 'blocks',
            category: 'landing-page',
            title: 'Travel hero with search',
            thumbnail: '/images/builder-templates/hero-beach.svg',
            nodes: [
                travelHeroSection(
                    'Discover your next journey',
                    'Create unforgettable trips with curated destinations, flexible offers, and local expertise.',
                    {
                        backgroundColor: '#082f49',
                        buttonText: 'Start planning',
                        buttonColor: '#0f766e',
                        imageAlt: 'Travel hero image'
                    }
                )
            ]
        },
        {
            id: 'featured-destinations',
            tab: 'blocks',
            category: 'destinations',
            title: 'Featured destinations grid',
            thumbnail: '/images/builder-templates/featured-destinations.svg',
            nodes: [
                dynamicContentSection(
                    'featured-destinations',
                    {
                        title: 'Featured Destinations',
                        limit: 6
                    },
                    {
                        backgroundColor: '#ffffff'
                    }
                )
            ]
        },
        {
            id: 'special-offers',
            tab: 'blocks',
            category: 'offers',
            title: 'Special offers section',
            thumbnail: '/images/builder-templates/special-offers.svg',
            nodes: [
                dynamicContentSection(
                    'special-offers',
                    {
                        title: 'Special Offers',
                        limit: 6
                    },
                    {
                        backgroundColor: '#f8fafc'
                    }
                )
            ]
        },
        {
            id: 'destination-cta',
            tab: 'blocks',
            category: 'landing-page',
            title: 'Destination call to action',
            thumbnail: '/images/builder-templates/destination-cta.svg',
            nodes: [
                centeredContentSection([
                    templateText('Plan your perfect escape', {
                        align: 'center',
                        fontSize: 34,
                        fontWeight: 800,
                        color: '#0f172a',
                        lineHeight: 1.15
                    }),
                    templateText('Use destinations and offers from your agency catalog to build a page visitors can explore quickly.', {
                        align: 'center',
                        fontSize: 18,
                        color: '#475569'
                    }),
                    templateButton('Explore offers', {
                        backgroundColor: '#0f766e',
                        textColor: '#ffffff'
                    })
                ], {
                    backgroundColor: '#f8fafc',
                    contentMaxWidth: 780,
                    layoutMaxWidth: 1100
                })
            ]
        },
        {
            id: 'agency-home-template',
            tab: 'templates',
            category: 'agency',
            title: 'Travel agency homepage',
            thumbnail: '/images/builder-templates/travel-home.svg',
            nodes: [
                travelHeroSection(
                    'Travel made simple',
                    'Showcase destinations, seasonal offers, and booking paths from one modern page.',
                    {
                        backgroundColor: '#0f172a',
                        buttonText: 'View destinations',
                        imageAlt: 'Agency travel preview'
                    }
                ),
                dynamicContentSection(
                    'featured-destinations',
                    {
                        title: 'Popular Destinations',
                        limit: 6
                    },
                    {
                        backgroundColor: '#ffffff'
                    }
                ),
                dynamicContentSection(
                    'special-offers',
                    {
                        title: 'This Month Offers',
                        limit: 3
                    },
                    {
                        backgroundColor: '#f8fafc'
                    }
                )
            ]
        },
        {
            id: 'offers-page-template',
            tab: 'templates',
            category: 'offers',
            title: 'Offers landing page',
            thumbnail: '/images/builder-templates/offers-landing.svg',
            nodes: [
                travelHeroSection(
                    'Find the right offer',
                    'Promote special deals and organize packages by destination and duration.',
                    {
                        backgroundColor: '#164e63',
                        buttonText: 'Browse offers',
                        imageAlt: 'Offer preview'
                    }
                ),
                dynamicContentSection(
                    'offer-grid',
                    {
                        title: 'Latest Offers',
                        source: 'latest',
                        destination_id: '',
                        limit: 9
                    },
                    {
                        backgroundColor: '#ffffff'
                    }
                )
            ]
        },
        {
            id: 'destinations-page-template',
            tab: 'templates',
            category: 'destinations',
            title: 'Destinations showcase',
            thumbnail: '/images/builder-templates/destinations-showcase.svg',
            nodes: [
                travelHeroSection(
                    'Explore destinations',
                    'A visual destination page connected to your CMS content.',
                    {
                        backgroundColor: '#14532d',
                        buttonText: 'See destinations',
                        buttonColor: '#16a34a',
                        imageAlt: 'Destination preview'
                    }
                ),
                dynamicContentSection(
                    'destination-grid',
                    {
                        title: 'All Destinations',
                        source: 'latest',
                        limit: 9
                    },
                    {
                        backgroundColor: '#ffffff'
                    }
                )
            ]
        },
        {
            id: 'luxury-package-template',
            tab: 'templates',
            category: 'luxury-travel',
            title: 'Luxury package page block',
            thumbnail: '/images/builder-templates/luxury-package.svg',
            nodes: [
                travelHeroSection(
                    'Luxury escapes',
                    'Present premium stays, private tours, and handpicked offers.',
                    {
                        backgroundColor: '#111827',
                        buttonText: 'Explore luxury offers',
                        imageAlt: 'Luxury escape preview'
                    }
                ),
                dynamicContentSection(
                    'offer-grid',
                    {
                        title: 'Premium Offers',
                        source: 'latest',
                        destination_id: '',
                        limit: 6
                    },
                    {
                        backgroundColor: '#ffffff'
                    }
                )
            ]
        },
        {
            id: 'family-trip-template',
            tab: 'templates',
            category: 'family-travel',
            title: 'Family travel page block',
            thumbnail: '/images/builder-templates/family-trip.svg',
            nodes: [
                travelHeroSection(
                    'Family trips made easy',
                    'Highlight safe destinations, flexible durations, and family packages.',
                    {
                        backgroundColor: '#0f766e',
                        buttonText: 'Plan family trip',
                        buttonColor: '#f97316',
                        imageAlt: 'Family trip preview'
                    }
                ),
                dynamicContentSection(
                    'featured-destinations',
                    {
                        title: 'Family Friendly Destinations',
                        limit: 6
                    },
                    {
                        backgroundColor: '#ffffff'
                    }
                )
            ]
        },
        {
            id: 'city-tours-template',
            tab: 'templates',
            category: 'city-tours',
            title: 'City tours page block',
            thumbnail: '/images/builder-templates/city-tours.svg',
            nodes: [
                travelHeroSection(
                    'City breaks and guided tours',
                    'Promote urban experiences, short stays, and cultural tours.',
                    {
                        backgroundColor: '#1d4ed8',
                        buttonText: 'Browse city tours',
                        imageAlt: 'City tour preview'
                    }
                ),
                dynamicContentSection(
                    'destination-grid',
                    {
                        title: 'Top Cities',
                        source: 'latest',
                        limit: 6
                    },
                    {
                        backgroundColor: '#ffffff'
                    }
                )
            ]
        },
        {
            id: 'beach-trips-template',
            tab: 'templates',
            category: 'beach-trips',
            title: 'Beach trips page block',
            thumbnail: '/images/builder-templates/beach-trips.svg',
            nodes: [
                travelHeroSection(
                    'Beach holidays',
                    'Feature sunny destinations, seasonal deals, and relaxing stays.',
                    {
                        backgroundColor: '#0891b2',
                        buttonText: 'See beach deals',
                        buttonColor: '#f59e0b',
                        imageAlt: 'Beach holiday preview'
                    }
                ),
                dynamicContentSection(
                    'special-offers',
                    {
                        title: 'Beach Offers',
                        limit: 6
                    },
                    {
                        backgroundColor: '#f8fafc'
                    }
                )
            ]
        },
        {
            id: 'travel-thank-you-template',
            tab: 'templates',
            category: 'thank-you',
            title: 'Travel thank you page',
            thumbnail: '/images/builder-templates/thank-you.svg',
            nodes: [
                centeredContentSection([
                    templateCard([
                        templateText('Thank you for your request', {
                            align: 'center',
                            fontSize: 34,
                            fontWeight: 800,
                            color: '#0f172a',
                            lineHeight: 1.15
                        }),
                        templateText('Our travel team will contact you with the best options shortly.', {
                            align: 'center',
                            fontSize: 18,
                            color: '#475569'
                        })
                    ], {
                        maxWidth: 720,
                        paddingTop: 36,
                        paddingBottom: 36,
                        paddingLeft: 36,
                        paddingRight: 36
                    })
                ], {
                    backgroundColor: '#f8fafc',
                    paddingTop: 96,
                    paddingBottom: 96,
                    contentMaxWidth: 760,
                    layoutMaxWidth: 900
                })
            ]
        }
    ],

    expandLegacyDynamicTemplateNodes(nodes, parent = null) {
        if (!Array.isArray(nodes)) {
            return [];
        }

        return nodes.flatMap((node, index) => {
            if (!node || typeof node !== 'object' || Array.isArray(node)) {
                return [];
            }

            const props =
                BuilderStructureRules.ensurePlainProps(node.props);

            const expandedChildren =
                this.expandLegacyDynamicTemplateNodes(
                    node.children || [],
                    node
                );

            const currentNode = {
                ...node,
                props,
                children: expandedChildren
            };

            const replacement =
                this.legacyDynamicReplacement(currentNode);

            if (
                replacement
                &&
                (
                    parent?.type === 'section'
                    ||
                    parent?.type === 'container'
                )
            ) {

                return this.instantiateTemplateNodes(
                    replacement,
                    currentNode.id || `${currentNode.type}_${index}`
                );

            }

            return [
                currentNode
            ];
        });
    },

    legacyDynamicReplacement(node) {
        switch (node.type) {
            case 'destination-grid':
            case 'featured-destinations':
                return collectionTemplateNodes(
                    'destination',
                    node.props
                );

            case 'offer-grid':
            case 'special-offers':
                return collectionTemplateNodes(
                    'offer',
                    node.props
                );

            case 'offer-card':
                return [
                    collectionCard('offer')
                ];

            default:
                return null;
        }
    },

    instantiateTemplateNodes(nodes, seed = 'template') {
        return Array.isArray(nodes)
            ? nodes.map((node, index) =>
                this.instantiateTemplateNode(
                    node,
                    seed,
                    String(index)
                )
            )
            : [];
    },

    instantiateTemplateNode(node, seed, path) {
        const id =
            this.templateNodeId(
                seed,
                node.type,
                path
            );

        return {
            id,
            type: node.type,
            accepts: BuilderStructureRules.acceptsForType(node.type),
            props: themeReadyTemplateProps(node.type, this.cloneProps(node.props)),
            children: Array.isArray(node.children)
                ? node.children.map((child, index) =>
                    this.instantiateTemplateNode(
                        child,
                        seed,
                        `${path}_${index}`
                    )
                )
                : []
        };
    },

    templateNodeId(seed, type, path) {
        return `${seed}_${type}_${path}`
            .replace(/[^A-Za-z0-9_-]/g, '_');
    },

    init() {
        this.render();
    },

    open(tab = 'blocks') {
        this.activeTab = tab;
        this.render();

        const modal = document.getElementById('builder-template-library');

        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    },

    close() {
        const modal = document.getElementById('builder-template-library');

        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    },

    setTab(tab) {
        this.activeTab = tab;
        this.activeCategory = 'all';
        this.render();
    },

    setCategory(category) {
        this.activeCategory = category || 'all';
        this.render();
    },

    setSearch(value) {
        this.search = String(value || '').trim().toLowerCase();
        this.render();
    },

    insert(id) {
        if (String(id).startsWith('saved:')) {
            BuilderSavedBlocks.insert(String(id).slice(6));
            return;
        }

        const template = this.templates.find(item => item.id === id);

        if (!template) {
            return;
        }

        const nodes = this.cloneNodes(template.nodes);

        nodes.forEach(node => {
            BuilderStore.addRootComponent(node);
        });

        BuilderHistory.push();
        BuilderRenderManager.requestRender('template.insert');
        this.close();
    },

    addBlankSection() {
        this.openSectionLayoutPicker();
    },

    openSectionLayoutPicker() {
        const modal = document.getElementById('builder-section-layout-picker');

        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    },

    closeSectionLayoutPicker() {
        const modal = document.getElementById('builder-section-layout-picker');

        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    },

    insertSectionLayout(layout = 'normal') {
        const node = this.createSectionLayout(layout);

        BuilderStore.addRootComponent(node);
        BuilderStore.setSelection(node.id, null);
        BuilderHistory.push();
        BuilderRenderManager.requestRender('section-layout.insert', layout);

        this.closeSectionLayoutPicker();
        this.close();

        window.setTimeout(() => {
            const element = document.querySelector(
                `[data-node-id="${CSS.escape(node.id)}"]`
            );

            if (element && window.BuilderSelectionManager) {
                BuilderSelectionManager.select(node.id, element, {
                    source: 'section-layout',
                    scroll: true
                });
            }
        }, 350);
    },

    createSectionLayout(layout = 'normal') {
        const section = BuilderComponentFactory.create('section');

        if (!section) {
            return null;
        }

        const layoutSpans = {
            'two-blocks': [6, 6],
            'three-blocks': [4, 4, 4],
            'four-blocks': [3, 3, 3, 3],
            'five-blocks': [2, 2, 2, 2, 2],
            'six-blocks': [2, 2, 2, 2, 2, 2],
            'left-sidebar': [3, 9],
            'right-sidebar': [9, 3],
            'narrow-wide': [3, 9],
            'wide-narrow': [9, 3],
            'main-sidebar': [8, 4],
            'sidebar-main': [4, 8]
        };

        section.props = {
            ...BuilderStructureRules.ensurePlainProps(section.props),
            paddingTop: 60,
            paddingBottom: 60
        };

        if (layout === 'normal') {
            const container = BuilderComponentFactory.create('container');

            container.props = {
                ...BuilderContainerRoles.applyCreationDefaults(
                    'layout',
                    BuilderStructureRules.ensurePlainProps(container.props)
                ),
                minHeight: 120
            };

            section.children = [container];

            return section;
        }

        const spans = layoutSpans[layout] || [];

        if (spans.length > 0) {
            const layoutContainer = BuilderComponentFactory.create('container');
            const gridContainer = BuilderComponentFactory.create('container');

            layoutContainer.props = {
                ...BuilderContainerRoles.applyCreationDefaults(
                    'layout',
                    BuilderStructureRules.ensurePlainProps(layoutContainer.props)
                ),
                minHeight: 120
            };

            gridContainer.props = {
                ...BuilderContainerRoles.applyCreationDefaults(
                    'grid',
                    BuilderStructureRules.ensurePlainProps(gridContainer.props)
                ),
                gap: 20,
                gridSpan: 12,
                minHeight: 120
            };

            gridContainer.children = spans.map(span => {
                const child = BuilderComponentFactory.create('container');

                child.props = {
                    ...BuilderContainerRoles.applyCreationDefaults(
                        'grid-item',
                        BuilderStructureRules.ensurePlainProps(child.props)
                    ),
                    gridSpan: span,
                    minHeight: 120
                };

                return child;
            });

            layoutContainer.children = [gridContainer];

            section.children = [layoutContainer];
        }

        return section;
    },

    render() {
        const grid = document.getElementById('builder-template-grid');
        const empty = document.getElementById('builder-template-empty');

        if (!grid || !empty) {
            return;
        }

        this.renderCategories();
        this.renderTabs();

        const items = this.filteredTemplates();
        const cards = [];

        if (this.shouldShowScratchCard()) {
            cards.push(this.scratchCardHtml());
        }

        cards.push(...items.map(item => this.cardHtml(item)));

        grid.innerHTML = cards.join('');
        empty.classList.toggle('hidden', cards.length > 0);
    },

    renderTabs() {
        document.querySelectorAll('[data-template-tab]').forEach(tab => {
            const active = tab.dataset.templateTab === this.activeTab;
            tab.classList.toggle('bg-indigo-50', active);
            tab.classList.toggle('text-slate-900', active);
            tab.classList.toggle('border-indigo-600', active);
            tab.classList.toggle('bg-white', !active);
            tab.classList.toggle('text-slate-500', !active);
            tab.classList.toggle('border-transparent', !active);
        });
    },

    renderCategories() {
        const container = document.getElementById('builder-template-categories');

        if (!container) {
            return;
        }

        container.innerHTML = this.categories
            .map(category => this.categoryButtonHtml(category))
            .join('');
    },

    categoryButtonHtml(category) {
        const active = category.value === this.activeCategory;

        return `
            <button
                type="button"
                data-action="template-library-category-button"
                data-template-category-value="${BuilderHtmlEscape.attribute(category.value)}"
                class="
                    block
                    w-full
                    rounded-sm
                    px-3
                    py-1.5
                    text-left
                    text-[13px]
                    font-medium
                    ${active ? 'bg-slate-100 text-slate-950' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950'}
                "
            >
                ${BuilderHtmlEscape.html(category.label)}
            </button>
        `;
    },

    filteredTemplates() {
        if (this.activeTab === 'my-templates') {
            return BuilderSavedBlocks.templates(this.search, this.activeCategory);
        }

        return this.templates.filter(item => {
            const inTab = item.tab === this.activeTab;
            const inCategory = this.activeCategory === 'all' || item.category === this.activeCategory;
            const inSearch = !this.search || item.title.toLowerCase().includes(this.search);

            return inTab && inCategory && inSearch;
        });
    },

    shouldShowScratchCard() {
        const scratchMatchesSearch =
            !this.search
            || 'create section from scratch'.includes(this.search);

        return this.activeTab !== 'my-templates'
            && (this.activeCategory === 'all' || this.activeCategory === 'landing-page')
            && scratchMatchesSearch;
    },

    scratchCardHtml() {
        return `
            <button
                type="button"
                data-action="add-blank-section"
                class="
                    flex
                    min-h-[148px]
                    flex-col
                    items-center
                    justify-center
                    gap-4
                    border
                    border-slate-200
                    bg-white
                    text-center
                    shadow-sm
                    hover:border-indigo-200
                    hover:bg-indigo-50/30
                "
            >
                <span class="
                    flex
                    h-12
                    w-12
                    items-center
                    justify-center
                    rounded-full
                    bg-indigo-600
                    text-3xl
                    font-light
                    leading-none
                    text-white
                    shadow-sm
                ">+</span>
                <span class="text-sm font-semibold text-slate-700">
                    Create section from scratch
                </span>
            </button>
        `;
    },

    cardHtml(item) {
        if (item.savedBlock) {
            return this.savedBlockCardHtml(item);
        }

        return `
            <article
                data-action="insert-builder-template"
                data-template-id="${BuilderHtmlEscape.attribute(item.id)}"
                class="
                    group
                    cursor-pointer
                    overflow-hidden
                    border
                    border-slate-200
                    bg-white
                    shadow-sm
                    transition
                    hover:-translate-y-0.5
                    hover:shadow-md
                "
            >
                <div class="
                    relative
                    h-28
                    overflow-hidden
                    bg-slate-100
                ">
                    <img
                        src="${BuilderHtmlEscape.attribute(item.thumbnail)}"
                        alt="${BuilderHtmlEscape.attribute(item.title)}"
                        class="h-full w-full object-cover"
                        loading="lazy"
                    >

                    <div class="absolute inset-0 hidden items-center justify-center bg-slate-950/35 group-hover:flex">
                        <span class="
                            inline-flex
                            items-center
                            gap-2
                            rounded-sm
                            bg-indigo-600
                            px-6
                            py-3
                            text-sm
                            font-semibold
                            text-white
                            shadow-lg
                        ">
                            <span class="text-base leading-none">+</span>
                            Insert
                        </span>
                    </div>
                </div>

                <div class="
                    border-t
                    border-slate-100
                    bg-white
                    px-3
                    py-2
                ">
                    <h3 class="truncate text-sm font-semibold text-slate-800">
                        ${BuilderHtmlEscape.html(item.title)}
                    </h3>
                </div>
            </article>
        `;
    },

    savedBlockCardHtml(item) {
        const id = BuilderHtmlEscape.attribute(item.savedBlock.id);
        return `
            <article class="overflow-hidden border border-slate-200 bg-white shadow-sm">
                <button type="button" data-action="insert-builder-template" data-template-id="${BuilderHtmlEscape.attribute(item.id)}" class="flex h-28 w-full items-center justify-center bg-gradient-to-br from-indigo-50 to-slate-100 text-center hover:from-indigo-100">
                    <span class="px-4 text-sm font-semibold text-slate-700">Insert ${BuilderHtmlEscape.html(item.title)}</span>
                </button>
                <div class="border-t border-slate-100 px-3 py-2">
                    <h3 class="truncate text-sm font-semibold text-slate-800">${BuilderHtmlEscape.html(item.title)}</h3>
                    <p class="mt-0.5 truncate text-[11px] text-slate-400">${BuilderHtmlEscape.html(item.category)}</p>
                    <div class="mt-2 flex gap-2">
                        <button type="button" data-action="rename-saved-block" data-saved-block-id="${id}" class="text-xs font-medium text-blue-600 hover:text-blue-700">Rename</button>
                        <button type="button" data-action="delete-saved-block" data-saved-block-id="${id}" class="text-xs font-medium text-red-600 hover:text-red-700">Delete</button>
                    </div>
                </div>
            </article>
        `;
    },

    cloneNodes(nodes) {
        return BuilderNodeClone.cloneNodes(
            nodes,
            () => BuilderComponentUtils.generateId(),
            (props, type) => themeReadyTemplateProps(type, this.cloneProps(props))
        );
    },

    cloneNode(node) {
        return BuilderNodeClone.cloneNodes(
            [node],
            () => BuilderComponentUtils.generateId(),
            (props, type) => themeReadyTemplateProps(type, this.cloneProps(props))
        )[0] || null;
    },

    cloneProps(props) {
        const safeProps = BuilderStructureRules.ensurePlainProps(props);

        if (typeof structuredClone === 'function') {
            return structuredClone(safeProps);
        }

        return JSON.parse(JSON.stringify(safeProps));
    },

};
