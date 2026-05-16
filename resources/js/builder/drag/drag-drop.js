window.BuilderDragDropAction = {

    /*
    |----------------------------------------------------------------------|
    | Drop
    |----------------------------------------------------------------------|
    */

    drop(event) {

        BuilderLogger.group(
            'DROP EVENT',
            BuilderLogger.colors.drag
        );

        event.preventDefault();

        event.stopPropagation();

        /*
        |----------------------------------------------------------------------|
        | Reorder Existing Node
        |----------------------------------------------------------------------|
        */

        const reorderNodeId =

            event.dataTransfer.getData(
                'reorder-node-id'
            );

        /*
        |----------------------------------------------------------------------|
        | Handle Reorder
        |----------------------------------------------------------------------|
        */

        if (reorderNodeId) {

            /*
            |------------------------------------------------------------------|
            | Canvas
            |------------------------------------------------------------------|
            */

            const canvas =

                event.target.closest(
                    '#canvas'
                );

            /*
            |------------------------------------------------------------------|
            | Target Node
            |------------------------------------------------------------------|
            */

            const targetNode =

                event.target.closest(
                    '[data-node-id]'
                );

            /*
            |------------------------------------------------------------------|
            | Root Canvas Drop
            |------------------------------------------------------------------|
            */

            if (

                canvas &&

                !targetNode

            ) {

                /*
                |--------------------------------------------------------------|
                | Detect Root Position
                |--------------------------------------------------------------|
                */

                const canvasRect =

                    canvas.getBoundingClientRect();

                const offsetY =

                    event.clientY - canvasRect.top;

                const position =

                    offsetY < (canvasRect.height / 2)
                        ? 'before'
                        : 'after';

                BuilderLogger.log(
                    'DRAGGED NODE',
                    reorderNodeId
                );

                BuilderLogger.log(
                    'TARGET NODE',
                    'ROOT'
                );

                BuilderLogger.log(
                    'POSITION',
                    position
                );

                /*
                |--------------------------------------------------------------|
                | Move To Root
                |--------------------------------------------------------------|
                */

                BuilderNodeMove.moveToRoot({

                    nodeId: reorderNodeId,

                    position

                });

                /*
                |--------------------------------------------------------------|
                | Reset
                |--------------------------------------------------------------|
                */

                BuilderDragState.reset();

                BuilderDragPreview.hide();

                BuilderLogger.success(
                    'DROP COMPLETE'
                );

                BuilderLogger.end();

                return;

            }

            /*
            |------------------------------------------------------------------|
            | Target Element
            |------------------------------------------------------------------|
            */

            const targetElement =

                event.target.closest(
                    '[data-node-id]'
                );

            if (!targetElement) {

                BuilderLogger.warn(
                    'NO TARGET ELEMENT'
                );

                BuilderLogger.end();

                return;

            }

            /*
            |------------------------------------------------------------------|
            | Target Id
            |------------------------------------------------------------------|
            */

            const targetId =

                targetElement.dataset.nodeId;

            /*
            |------------------------------------------------------------------|
            | Prevent Self Drop
            |------------------------------------------------------------------|
            */

            if (reorderNodeId === targetId) {

                BuilderLogger.warn(
                    'SELF DROP PREVENTED'
                );

                BuilderLogger.end();

                return;

            }

            /*
            |------------------------------------------------------------------|
            | Detect Position
            |------------------------------------------------------------------|
            */

            const position =

                BuilderDragHitbox.detect(
                    targetElement,
                    event
                );

            BuilderLogger.log(
                'DRAGGED NODE',
                reorderNodeId
            );

            BuilderLogger.log(
                'TARGET NODE',
                targetId
            );

            BuilderLogger.log(
                'POSITION',
                position
            );

            /*
            |------------------------------------------------------------------|
            | Move Node
            |------------------------------------------------------------------|
            */

            BuilderNodeMove.move({

                nodeId: reorderNodeId,

                targetId,

                position

            });

            /*
            |------------------------------------------------------------------|
            | Reset
            |------------------------------------------------------------------|
            */

            BuilderDragState.reset();

            BuilderDragPreview.hide();

            BuilderLogger.success(
                'DROP COMPLETE'
            );

            BuilderLogger.end();

            return;

        }

        /*
        |----------------------------------------------------------------------|
        | Create New Component
        |----------------------------------------------------------------------|
        */

        const type =

            event.dataTransfer.getData(
                'component-type'
            );

        if (!type) {

            BuilderLogger.warn(
                'NO COMPONENT TYPE'
            );

            BuilderLogger.end();

            return;

        }

        /*
        |----------------------------------------------------------------------|
        | Create Component
        |----------------------------------------------------------------------|
        */

        const component =

            BuilderComponentFactory.create(
                type
            );

        if (!component) {

            BuilderLogger.warn(
                'COMPONENT CREATION FAILED'
            );

            BuilderLogger.end();

            return;

        }

        /*
        |----------------------------------------------------------------------|
        | Find Dropzone
        |----------------------------------------------------------------------|
        */

        const dropzone =

            BuilderDragUtils.getDropzone(
                event.target
            );

        /*
        |----------------------------------------------------------------------|
        | Root Drop
        |----------------------------------------------------------------------|
        */

        if (!dropzone) {

            BuilderStore.addRootComponent(
                component
            );
            
            BuilderHistory.push();

            BuilderRenderManager.requestRender();

            BuilderLogger.success(
                'DROP COMPLETE'
            );

            BuilderLogger.end();

            return;

        }

        /*
        |----------------------------------------------------------------------|
        | Parent Node
        |----------------------------------------------------------------------|
        */

        const parent =

            BuilderDragUtils.getParentNode(
                dropzone
            );

        if (!parent) {

            BuilderLogger.warn(
                'NO PARENT NODE'
            );

            BuilderLogger.end();

            return;

        }

        /*
        |----------------------------------------------------------------------|
        | Validate
        |----------------------------------------------------------------------|
        */

        const isValid =

            BuilderDragValidate.canDrop(
                parent,
                type
            );

        if (!isValid) {

            console.warn(
                `${type} not allowed inside ${parent.type}`
            );

            BuilderLogger.warn(
                'INVALID DROP'
            );

            BuilderLogger.end();

            return;

        }

        /*
        |----------------------------------------------------------------------|
        | Add Child
        |----------------------------------------------------------------------|
        */

        BuilderNodes.addChild(

            parent.id,
            component

        );

        /*
        |----------------------------------------------------------------------|
        | History
        |----------------------------------------------------------------------|
        */

        BuilderHistory.push();

        /*
        |----------------------------------------------------------------------|
        | Render
        |----------------------------------------------------------------------|
        */

        BuilderRenderManager.requestRender();

        BuilderLogger.success(
            'DROP COMPLETE'
        );

        BuilderLogger.end();

    }

};