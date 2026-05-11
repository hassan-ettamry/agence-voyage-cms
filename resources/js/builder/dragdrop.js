window.BuilderDragDrop = {

    /*
    |--------------------------------------------------------------
    | Start Drag
    |--------------------------------------------------------------
    */

    start(event, type) {

        event.dataTransfer.setData(
            'component-type',
            type
        );

    },

    /*
    |--------------------------------------------------------------
    | Allow Drop
    |--------------------------------------------------------------
    */

    allowDrop(event) {

        event.preventDefault();

    },

    /*
    |--------------------------------------------------------------
    | Drop
    |--------------------------------------------------------------
    */

    drop(event) {

        event.preventDefault();

        event.stopPropagation();

        /*
        |----------------------------------------------------------
        | Component Type
        |----------------------------------------------------------
        */

        const type =
            event.dataTransfer.getData(
                'component-type'
            );

        if (!type) return;

        /*
        |----------------------------------------------------------
        | Create Component
        |----------------------------------------------------------
        */

        const component =
            BuilderComponents.factory(type);

        if (!component) return;

        /*
        |----------------------------------------------------------
        | Find Dropzone
        |----------------------------------------------------------
        */

        const dropzone =
            event.target.closest(
                '[data-dropzone]'
            );

        /*
        |----------------------------------------------------------
        | ROOT DROP
        |----------------------------------------------------------
        */

        if (!dropzone) {

            Builder.addComponent(
                component
            );
            BuilderHistory.push();
            BuilderCanvas.render();

            console.log(

                JSON.stringify(
                    Builder.structure,
                    null,
                    2
                )

            );

            return;

        }

        /*
        |----------------------------------------------------------
        | Parent Node
        |----------------------------------------------------------
        */

        const parentId =
            dropzone.dataset.nodeId;

        if (!parentId) return;

        /*
        |----------------------------------------------------------
        | Find Parent
        |----------------------------------------------------------
        */

        const parent =
            Builder.findNodeById(
                parentId
            );

        if (!parent) return;

        /*
        |----------------------------------------------------------
        | Validate Child
        |----------------------------------------------------------
        */

        if (

            parent.accepts &&

            !parent.accepts.includes(type)

        ) {

            console.warn(
                `${type} not allowed inside ${parent.type}`
            );

            return;

        }

        /*
        |----------------------------------------------------------
        | Add Child
        |----------------------------------------------------------
        */

        Builder.addChild(
            parentId,
            component
        );

        /*
        |----------------------------------------------------------
        | Re-render
        |----------------------------------------------------------
        */
        BuilderHistory.push();
        BuilderCanvas.render();

        console.log(

            JSON.stringify(
                Builder.structure,
                null,
                2
            )

        );

    }

};