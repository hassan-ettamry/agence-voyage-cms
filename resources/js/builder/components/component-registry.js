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
                description: 'Hero description here.'
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

    heading() {

        return {

            type: 'heading',

            accepts:
                BuilderStructureRules.acceptsForType(
                    'heading'
                ),

            props: {
                text: 'Your Heading Here'
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
                text: 'Click Me'
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
                alt: 'Image'
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

            props: {},

            children: []

        };

    },

    column() {

        return {

            type: 'column',

            accepts:
                BuilderStructureRules.acceptsForType(
                    'column'
                ),

            props: {},

            children: []

        };

    },

    row() {

        return {

            type: 'row',

            accepts:
                BuilderStructureRules.acceptsForType(
                    'row'
                ),

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
