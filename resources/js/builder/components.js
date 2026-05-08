window.BuilderComponents = {

    /*
    |--------------------------------------------------------------------------
    | Component Factory
    |--------------------------------------------------------------------------
    */

    factory(type) {

        const map = {

            hero: {
                type: 'hero',

                props: {
                    title: 'Hero Title',
                    description: 'Hero description here.'
                },

                children: []
            },

            text: {
                type: 'text',

                props: {
                    text: 'Your text here.'
                },

                children: []
            },

            heading: {
                type: 'heading',

                props: {
                    text: 'Your Heading Here'
                },

                children: []
            },

            button: {
                type: 'button',

                props: {
                    text: 'Click Me'
                },

                children: []
            },

            image: {
                type: 'image',

                props: {
                    src: '',
                    alt: 'Image'
                },

                children: []
            },

            section: {
                type: 'section',

                props: {},

                children: []
            },

            row: {
                type: 'row',

                props: {},

                children: []
            },

            container: {
                type: 'container',

                props: {},

                children: []
            }

        };

        return map[type] || null;
    },

    /*
    |--------------------------------------------------------------------------
    | Add Component
    |--------------------------------------------------------------------------
    */

    add(type) {

        const component = this.factory(type);

        if (!component) return;

        /*
        |--------------------------------------------------------------------------
        | Add To State
        |--------------------------------------------------------------------------
        */

        Builder.addComponent(component);

        /*
        |--------------------------------------------------------------------------
        | Re-render Canvas
        |--------------------------------------------------------------------------
        */

        BuilderCanvas.render();
    }

};