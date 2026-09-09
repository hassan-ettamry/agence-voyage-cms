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

    completionWaiters: [],

    /*
    |--------------------------------------------------------------------------
    | Request Render
    |--------------------------------------------------------------------------
    */

    requestRender(source = 'unknown', reason = null) {

        const completion =
            new Promise(resolve => {

                this.completionWaiters.push(resolve);

            });

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

            return completion;
        }

        this.schedule();

        return completion;

    },

    /*
    |--------------------------------------------------------------------------
    | Complete Grouped Requests
    |--------------------------------------------------------------------------
    */

    settleRequests(result) {

        const waiters =
            this.completionWaiters.splice(0);

        waiters.forEach(resolve => {

            resolve(result === true);

        });

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

            let rendered = false;

            /*
            |--------------------------------------------------------------------------
            | Render
            |--------------------------------------------------------------------------
            */

            if (window.BuilderCanvas) {

                this.inFlight = true;

                this.pendingAfterCurrent = false;

                try {

                    rendered = await BuilderCanvas.render(
                        request?.source,
                        request?.reason
                    ) === true;

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

                return;

            }

            this.settleRequests(rendered);

        });

    }

};
