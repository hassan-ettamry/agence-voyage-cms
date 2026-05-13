window.BuilderOverlayPosition = {

    /*
    |--------------------------------------------------------------------------
    | Calculate Position
    |--------------------------------------------------------------------------
    */

    calculate(element) {

        const rect =

            element.getBoundingClientRect();

        const wrapperRect =

            BuilderOverlayElements
                .getWrapper()
                .getBoundingClientRect();

        return {

            top:

                rect.top
                -
                wrapperRect.top
                -
                30,

            left:

                rect.left
                -
                wrapperRect.left

        };

    }

};