window.BuilderComponentRegistry = {
    section() {
        return {
            type: 'section',
            accepts: BuilderStructureRules.acceptsForType('section'),
            props: {},
            children: []
        };
    },

    container() {
        return {
            type: 'container',
            accepts: BuilderStructureRules.acceptsForType('container'),
            props: BuilderContainerRoles.applyCreationDefaults('group'),
            children: []
        };
    }
};
