window.BuilderOverlayPosition = {

    /*
    |--------------------------------------------------------------------------
    | Calculate Position
    |--------------------------------------------------------------------------
    */

    calculate(element, options = {}) {

        const {
            align = 'left',
            offsetY = 0
        } = options;

        const rect =

            element.getBoundingClientRect();

        const wrapperRect =

            BuilderOverlayElements
                .getWrapper()
                .getBoundingClientRect();

        const top =
            Math.max(
                0,
                rect.top
                -
                wrapperRect.top
                +
                offsetY
            );

        const rawLeft =
            align === 'center'
                ? rect.left - wrapperRect.left + (rect.width / 2)
                : rect.left - wrapperRect.left;

        const left =
            Math.max(
                0,
                rawLeft
            );

        return {
            top,
            left,
            align,
            width: rect.width,
            height: rect.height
        };

    }

};
