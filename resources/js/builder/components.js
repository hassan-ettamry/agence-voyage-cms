window.BuilderComponents = {

    /*
    |--------------------------------------------------------------------------
    | Generate Node ID
    |--------------------------------------------------------------------------
    */

    generateId() {

        return 'node_' +
            Math.random()
                .toString(36)
                .substring(2, 9);

    },

    /*
    |--------------------------------------------------------------------------
    | Component Factory
    |--------------------------------------------------------------------------
    */

    factory(type) {

        const map = {

            /*
            |--------------------------------------------------------------------------
            | Hero
            |--------------------------------------------------------------------------
            */

            hero: {

                id: this.generateId(),

                type: 'hero',

                accepts: [],

                props: {
                    title: 'Hero Title',
                    description: 'Hero description here.'
                },

                children: []
            },

            /*
            |--------------------------------------------------------------------------
            | Text
            |--------------------------------------------------------------------------
            */

            text: {

                id: this.generateId(),

                type: 'text',

                accepts: [],

                props: {
                    text: 'Your text here.'
                },

                children: []
            },

            /*
            |--------------------------------------------------------------------------
            | Heading
            |--------------------------------------------------------------------------
            */

            heading: {

                id: this.generateId(),

                type: 'heading',

                accepts: [],

                props: {
                    text: 'Your Heading Here'
                },

                children: []
            },

            /*
            |--------------------------------------------------------------------------
            | Button
            |--------------------------------------------------------------------------
            */

            button: {

                id: this.generateId(),

                type: 'button',

                accepts: [],

                props: {
                    text: 'Click Me'
                },

                children: []
            },

            /*
            |--------------------------------------------------------------------------
            | Image
            |--------------------------------------------------------------------------
            */

            image: {

                id: this.generateId(),

                type: 'image',

                accepts: [],

                props: {
                    src: '',
                    alt: 'Image'
                },

                children: []
            },

            /*
            |--------------------------------------------------------------------------
            | Section
            |--------------------------------------------------------------------------
            */

            section: {

                id: this.generateId(),

                type: 'section',

                accepts: [
                    'text',
                    'heading',
                    'button',
                    'image',
                    'container',
                    'section',
                    'row',
                    'hero'
                ],

                props: {},

                children: []
            },

            /*
            |--------------------------------------------------------------------------
            | Container
            |--------------------------------------------------------------------------
            */

            container: {

                id: this.generateId(),

                type: 'container',

                accepts: [
                    'text',
                    'heading',
                    'button',
                    'image',
                    'container',
                    'section',
                    'row',
                    'hero'
                ],

                props: {},

                children: []
            },

            /*
            |--------------------------------------------------------------------------
            | Column
            |--------------------------------------------------------------------------
            */

            column: {

                id: this.generateId(),

                type: 'column',

                accepts: [
                    'text',
                    'heading',
                    'button',
                    'image',
                    'container',
                    'section',
                    'row',
                    'hero'
                ],

                props: {},

                children: []
            },

            /*
            |--------------------------------------------------------------------------
            | Row
            |--------------------------------------------------------------------------
            */

            row: {

                id: this.generateId(),

                type: 'row',

                accepts: [
                    'column'
                ],

                props: {
                    columns: 2
                },

                children: [

                    {
                        id: this.generateId(),

                        type: 'column',

                        accepts: [
                            'text',
                            'heading',
                            'button',
                            'image',
                            'container',
                            'section',
                            'row',
                            'hero'
                        ],

                        props: {},

                        children: []
                    },

                    {
                        id: this.generateId(),

                        type: 'column',

                        accepts: [
                            'text',
                            'heading',
                            'button',
                            'image',
                            'container',
                            'section',
                            'row',
                            'hero'
                        ],

                        props: {},

                        children: []
                    }

                ]
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

        const component =
            this.factory(type);

        if (!component) return;

        /*
        |--------------------------------------------------------------------------
        | Add To State
        |--------------------------------------------------------------------------
        */

        Builder.addComponent(
            component
        );

        /*
        |--------------------------------------------------------------------------
        | Re-render Canvas
        |--------------------------------------------------------------------------
        */

        BuilderCanvas.render();

    }

};