window.BuilderComponentRegistry = {

    hero() {

        return {

            type: 'hero',

            accepts:
                BuilderStructureRules.acceptsForType(
                    'hero'
                ),

            props: {
                title: 'Hero Title',
                description: 'Hero description here.',
                buttonText: '',
                linkType: 'none',
                url: '#',
                target: 'same-tab',
                variant: 'left'
            },

            children: []

        };

    },

    text() {

        return {

            type: 'text',

            accepts:
                BuilderStructureRules.acceptsForType(
                    'text'
                ),

            props: {
                text: 'Your text here.'
            },

            children: []

        };

    },

    richtext() {

        return {

            type: 'richtext',

            accepts:
                BuilderStructureRules.acceptsForType(
                    'richtext'
                ),

            props: {
                html: 'Lorem, ipsum dolor sit amet consectetur adipisicing elit. Mollitia, quis! Iste debitis id nulla, nesciunt minus enim, ea sed, doloremque inventore tenetur magnam minima fugit sint consequatur repudiandae! Numquam, perspiciatis.'
            },

            children: []

        };

    },

    heading() {

        return {

            type: 'heading',

            accepts:
                BuilderStructureRules.acceptsForType(
                    'heading'
                ),

            props: {
                text: 'Your Heading Here',
                tag: 'h2',
                linkType: 'none',
                url: '#'
            },

            children: []

        };

    },

    button() {

        return {

            type: 'button',

            accepts:
                BuilderStructureRules.acceptsForType(
                    'button'
                ),

            props: {
                text: 'Click Me',
                linkType: 'none',
                url: '#',
                target: 'same-tab',
                icon: '',
                iconPosition: 'before',
                variant: 'solid',
                size: 'md',
                width: 'auto',
                align: 'left'
            },

            children: []

        };

    },

    image() {

        return {

            type: 'image',

            accepts:
                BuilderStructureRules.acceptsForType(
                    'image'
                ),

            props: {
                src: '',
                alt: 'Image',
                altText: 'Image',
                title: '',
                caption: '',
                objectFit: 'cover',
                height: 256,
                borderRadius: 12,
                linkType: 'none',
                url: '#'
            },

            children: []

        };

    },

    icon() {

        return {

            type: 'icon',

            accepts:
                BuilderStructureRules.acceptsForType(
                    'icon'
                ),

            props: {
                icon: 'star',
                size: 40,
                backgroundColor: 'transparent',
                padding: 0,
                borderRadius: 0,
                align: 'left',
                linkType: 'none',
                url: '#'
            },

            children: []

        };

    },

    'icon-text'() {

        return {

            type: 'icon-text',

            accepts:
                BuilderStructureRules.acceptsForType(
                    'icon-text'
                ),

            props: {
                icon: 'map-pin',
                title: 'Icon title',
                text: 'Short supporting text.',
                layout: 'horizontal',
                size: 36,
                gap: 12,
                align: 'left',
                linkType: 'none',
                url: '#'
            },

            children: []

        };

    },

    link() {

        return {

            type: 'link',

            accepts:
                BuilderStructureRules.acceptsForType(
                    'link'
                ),

            props: {
                text: 'Link text',
                linkType: 'external',
                url: '#',
                target: 'same-tab',
                fontSize: 16,
                fontWeight: 500,
                underline: 'yes',
                align: 'left'
            },

            children: []

        };

    },

    video() {

        return {

            type: 'video',

            accepts:
                BuilderStructureRules.acceptsForType(
                    'video'
                ),

            props: {
                url: '',
                title: 'Video',
                sourceType: 'url',
                poster: '',
                height: 360,
                controls: 'yes',
                autoplay: 'no',
                muted: 'no',
                loop: 'no',
                borderRadius: 12
            },

            children: []

        };

    },

    iframe() {

        return {

            type: 'iframe',

            accepts:
                BuilderStructureRules.acceptsForType(
                    'iframe'
                ),

            props: {
                url: '',
                title: 'Embedded content',
                height: 420,
                allowFullscreen: 'yes',
                sandbox: 'yes',
                borderRadius: 12
            },

            children: []

        };

    },

    gallery() {

        return {

            type: 'gallery',

            accepts:
                BuilderStructureRules.acceptsForType(
                    'gallery'
                ),

            props: {
                images: '',
                layout: 'grid',
                columns: 3,
                gap: 16,
                height: 220,
                borderRadius: 12,
                lightbox: 'yes',
                showCaptions: 'no'
            },

            children: []

        };

    },

    map() {

        return {

            type: 'map',

            accepts:
                BuilderStructureRules.acceptsForType(
                    'map'
                ),

            props: {
                address: 'Marrakech, Morocco',
                embedUrl: '',
                markerLabel: '',
                showDirections: 'yes',
                height: 360,
                zoom: 12,
                borderRadius: 12
            },

            children: []

        };

    },

    'contact-form'() {

        return {

            type: 'contact-form',

            accepts:
                BuilderStructureRules.acceptsForType(
                    'contact-form'
                ),

            props: {
                title: 'Contact us',
                subtitle: 'Send us a message and we will reply soon.',
                nameLabel: 'Name',
                emailLabel: 'Email',
                messageLabel: 'Message',
                buttonText: 'Send message',
                recipientEmail: '',
                actionUrl: '',
                successMessage: 'Thank you. We will contact you soon.',
                consentText: ''
            },

            children: []

        };

    },

    faq() {

        return {

            type: 'faq',

            accepts:
                BuilderStructureRules.acceptsForType(
                    'faq'
                ),

            props: {
                title: 'Frequently asked questions',
                items: 'What is included?|Flights, hotels, transfers, and guided activities can be included depending on the offer.\nCan I customize the trip?|Yes. Contact the agency to adapt dates, hotels, and activities.',
                allowMultiple: 'yes',
                iconStyle: 'plus',
                schemaEnabled: 'no'
            },

            children: []

        };

    },

    countdown() {

        return {

            type: 'countdown',

            accepts:
                BuilderStructureRules.acceptsForType(
                    'countdown'
                ),

            props: {
                label: 'Offer ends in',
                targetDate: '2026-12-31T23:59',
                timezone: 'Africa/Casablanca',
                expiredText: 'Offer expired',
                showDays: 'yes',
                showHours: 'yes',
                showMinutes: 'yes',
                showSeconds: 'yes'
            },

            children: []

        };

    },

    section() {

        return {

            type: 'section',

            accepts:
                BuilderStructureRules.acceptsForType(
                    'section'
                ),

            props: {},

            children: []

        };

    },

    container() {

        return {

            type: 'container',

            accepts:
                BuilderStructureRules.acceptsForType(
                    'container'
                ),

            props: BuilderContainerRoles.applyCreationDefaults('group'),

            children: []

        };

    },

    'destination-grid'() {

        return {
            type: 'destination-grid',
            accepts: BuilderStructureRules.acceptsForType('destination-grid'),
            props: {
                title: 'Destinations',
                source: 'latest',
                limit: 6,
                sort: 'latest',
                columns: 3,
                gap: 24,
                showImage: 'yes',
                showTitle: 'yes',
                showDescription: 'yes',
                showMeta: 'yes',
                showCta: 'yes',
                buttonText: 'View destination'
            },
            children: []
        };

    },

    'featured-destinations'() {

        return {
            type: 'featured-destinations',
            accepts: BuilderStructureRules.acceptsForType('featured-destinations'),
            props: {
                title: 'Featured Destinations',
                limit: 6,
                sort: 'latest',
                columns: 3,
                gap: 24,
                showImage: 'yes',
                showTitle: 'yes',
                showDescription: 'yes',
                showMeta: 'yes',
                showCta: 'yes',
                buttonText: 'View destination'
            },
            children: []
        };

    },

    'offer-grid'() {

        return {
            type: 'offer-grid',
            accepts: BuilderStructureRules.acceptsForType('offer-grid'),
            props: {
                title: 'Offers',
                source: 'latest',
                destination_id: '',
                limit: 6,
                sort: 'latest',
                columns: 3,
                gap: 24,
                showImage: 'yes',
                showTitle: 'yes',
                showDescription: 'yes',
                showMeta: 'yes',
                showCta: 'yes',
                showPrice: 'yes',
                showDuration: 'yes',
                buttonText: 'View offer'
            },
            children: []
        };

    },

    'special-offers'() {

        return {
            type: 'special-offers',
            accepts: BuilderStructureRules.acceptsForType('special-offers'),
            props: {
                title: 'Special Offers',
                limit: 6,
                sort: 'latest',
                columns: 3,
                gap: 24,
                showImage: 'yes',
                showTitle: 'yes',
                showDescription: 'yes',
                showMeta: 'yes',
                showCta: 'yes',
                showPrice: 'yes',
                showDuration: 'yes',
                buttonText: 'View offer'
            },
            children: []
        };

    },

    'offer-card'() {

        return {
            type: 'offer-card',
            accepts: BuilderStructureRules.acceptsForType('offer-card'),
            props: {
                offer_id: '',
                title: 'Offer title',
                description: '',
                price: '',
                buttonText: 'View offer',
                showImage: 'yes',
                showTitle: 'yes',
                showDescription: 'yes',
                showPrice: 'yes',
                showDuration: 'yes',
                showCta: 'yes'
            },
            children: []
        };

    }

};
