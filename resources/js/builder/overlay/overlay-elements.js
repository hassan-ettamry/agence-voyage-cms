window.BuilderOverlayElements = {

    /*
    |--------------------------------------------------------------------------
    | Get Root
    |--------------------------------------------------------------------------
    */

    getRoot() {

        return document.getElementById(
            'builder-overlay-root'
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Get Canvas Wrapper
    |--------------------------------------------------------------------------
    */

    getWrapper() {

        return document.getElementById(
            'canvas-wrapper'
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Get Element By Node
    |--------------------------------------------------------------------------
    */

    getElement(nodeId) {

        return document.querySelector(

            `[data-node-id="${nodeId}"]`

        );

    }

};