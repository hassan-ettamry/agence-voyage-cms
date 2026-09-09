window.BuilderDragUtils = {

    /*
    |--------------------------------------------------------------------------
    | Get Dropzone
    |--------------------------------------------------------------------------
    */

    getDropzone(target) {

        return target.closest(
            '[data-dropzone]'
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Get Parent Node
    |--------------------------------------------------------------------------
    */

    getParentNode(dropzone) {

        if (!dropzone) {
            return null;
        }

        const parentId =
            dropzone.dataset.nodeId;

        if (!parentId) {
            return null;
        }

        return Builder.findNodeById(
            parentId
        );

    }

};