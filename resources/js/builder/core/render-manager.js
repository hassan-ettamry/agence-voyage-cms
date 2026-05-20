window.BuilderRenderManager = {

    /*
    |--------------------------------------------------------------------------
    | Render Scheduled
    |--------------------------------------------------------------------------
    */

    scheduled: false,

    lastRenderRequest: null,

    inFlight: false,

    pendingAfterCurrent: false,

    /*
    |--------------------------------------------------------------------------
    | Request Render
    |--------------------------------------------------------------------------
    */

    requestRender(source = 'unknown', reason = null) {

        this.lastRenderRequest = {
            source,
            reason,
            timestamp: Date.now()
        };

        BuilderEventBus.emitSafe(
            BuilderEvents.CANVAS_RENDER_REQUESTED,
            this.lastRenderRequest
        );

        if (
            BuilderLogger.shouldLog('render')
            ||
            BuilderLogger.shouldLog('interaction')
        ) {

            BuilderLogger.log(
                'RENDER REQUESTED',
                this.lastRenderRequest
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Prevent Render Spam
        |--------------------------------------------------------------------------
        */

        if (this.scheduled) {

            if (this.inFlight) {

                this.pendingAfterCurrent = true;

                window.BuilderCanvas
                    ?.invalidateCurrentRender
                    ?.();

            }

            return;
        }

        this.schedule();

    },

    /*
    |--------------------------------------------------------------------------
    | Schedule
    |--------------------------------------------------------------------------
    */

    schedule() {

        this.scheduled = true;

        /*
        |--------------------------------------------------------------------------
        | Next Frame Render
        |--------------------------------------------------------------------------
        */

        requestAnimationFrame(async () => {

            const request =
                this.lastRenderRequest;

            /*
            |--------------------------------------------------------------------------
            | Render
            |--------------------------------------------------------------------------
            */

            if (window.BuilderCanvas) {

                this.inFlight = true;

                this.pendingAfterCurrent = false;

                try {

                    await BuilderCanvas.render(
                        request?.source,
                        request?.reason
                    );

                } catch (error) {

                    console.error(
                        'Builder managed render error:',
                        error
                    );

                } finally {

                    this.inFlight = false;

                }

            }

            /*
            |--------------------------------------------------------------------------
            | Reset
            |--------------------------------------------------------------------------
            */

            this.scheduled = false;

            if (this.pendingAfterCurrent) {

                this.schedule();

            }

        });

    }

};
