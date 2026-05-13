window.BuilderCanvasEvents = {

    /*
    |--------------------------------------------------------------------------
    | Bind Events
    |--------------------------------------------------------------------------
    */

    bind() {

        const canvas =
            BuilderCanvasUtils.getCanvas();

        if (!canvas) return;

        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Bindings
        |--------------------------------------------------------------------------
        */

        if (canvas.dataset.eventsBound) {
            return;
        }

        canvas.dataset.eventsBound =
            'true';

        /*
        |--------------------------------------------------------------------------
        | Bind Systems
        |--------------------------------------------------------------------------
        */

        BuilderCanvasHover.bind(
            canvas
        );

        BuilderCanvasSelection.bind(
            canvas
        );

        BuilderCanvasFocus.bind();

    }

};