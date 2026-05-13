window.BuilderComponentRegistry = {

    hero() {

        return {

            type: 'hero',

            accepts: [],

            props: {
                title: 'Hero Title',
                description: 'Hero description here.'
            },

            children: []

        };

    },

    text() {

        return {

            type: 'text',

            accepts: [],

            props: {
                text: 'Your text here.'
            },

            children: []

        };

    },

    heading() {

        return {

            type: 'heading',

            accepts: [],

            props: {
                text: 'Your Heading Here'
            },

            children: []

        };

    },

    button() {

        return {

            type: 'button',

            accepts: [],

            props: {
                text: 'Click Me'
            },

            children: []

        };

    },

    image() {

        return {

            type: 'image',

            accepts: [],

            props: {
                src: '',
                alt: 'Image'
            },

            children: []

        };

    },

    section() {

        return {

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

        };

    },

    container() {

        return {

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

        };

    },

    column() {

        return {

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

        };

    },

    row() {

        return {

            type: 'row',

            accepts: [
                'column'
            ],

            props: {
                columns: 2
            },

            children: [

                BuilderComponentFactory.create(
                    'column'
                ),

                BuilderComponentFactory.create(
                    'column'
                )

            ]

        };

    }

};