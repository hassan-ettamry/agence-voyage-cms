window.BuilderDragDrop = {

    start(event, type) {

        event.dataTransfer.setData('type', type);

    },

    allowDrop(event) {

        event.preventDefault();

    },

    drop(event) {

        event.preventDefault();

        const type = event.dataTransfer.getData('type');

        if (!type) return;

        BuilderComponents.add(type);

    }

};