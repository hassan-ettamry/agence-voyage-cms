window.BuilderDebugRender = {

    activeRenders: 0,

    totalRenders: 0,

    renderHistory: [],

    maxHistory: 50,

    /*
    |--------------------------------------------------------------------------
    | Start Render
    |--------------------------------------------------------------------------
    */

    start(source = 'unknown') {

        this.activeRenders++;

        this.totalRenders++;

        const renderId =
            this.totalRenders;

        const startTime =
            performance.now();

        /*
        |--------------------------------------------------------------------------
        | Save History
        |--------------------------------------------------------------------------
        */

        this.renderHistory.push({

            id: renderId,

            source,

            startTime,

            active:
                this.activeRenders,

            timestamp:
                Date.now()

        });

        /*
        |--------------------------------------------------------------------------
        | Limit History
        |--------------------------------------------------------------------------
        */

        if (
            this.renderHistory.length >
            this.maxHistory
        ) {

            this.renderHistory.shift();

        }

        /*
        |--------------------------------------------------------------------------
        | Logs
        |--------------------------------------------------------------------------
        */

        BuilderLogger.group(

            `RENDER START #${renderId}`,

            BuilderLogger.colors.render

        );

        BuilderLogger.log(
            'SOURCE',
            source
        );

        BuilderLogger.log(
            'ACTIVE RENDERS',
            this.activeRenders
        );

        /*
        |--------------------------------------------------------------------------
        | Detect Overlap
        |--------------------------------------------------------------------------
        */

        if (this.activeRenders > 1) {

            BuilderLogger.warn(
                'RENDER OVERLAP DETECTED'
            );

        }

        return {

            renderId,

            startTime

        };

    },

    /*
    |--------------------------------------------------------------------------
    | End Render
    |--------------------------------------------------------------------------
    */

    end(renderData) {

        this.activeRenders--;

        const duration =
            performance.now()
            -
            renderData.startTime;

        BuilderLogger.success(
            `RENDER END #${renderData.renderId}`
        );

        BuilderLogger.log(
            'DURATION',
            `${duration.toFixed(2)}ms`
        );

        BuilderLogger.log(
            'ACTIVE RENDERS',
            this.activeRenders
        );

        /*
        |--------------------------------------------------------------------------
        | Slow Render Detection
        |--------------------------------------------------------------------------
        */

        if (duration > 100) {

            BuilderLogger.warn(
                'SLOW RENDER DETECTED'
            );

        }

        BuilderLogger.end();

    },

    /*
    |--------------------------------------------------------------------------
    | Print Stats
    |--------------------------------------------------------------------------
    */

    stats() {

        BuilderLogger.group(
            'RENDER STATS',
            BuilderLogger.colors.render
        );

        BuilderLogger.log(
            'TOTAL RENDERS',
            this.totalRenders
        );

        BuilderLogger.log(
            'ACTIVE RENDERS',
            this.activeRenders
        );

        BuilderLogger.table(
            this.renderHistory
        );

        BuilderLogger.end();

    },

    /*
    |--------------------------------------------------------------------------
    | Detect Render Spam
    |--------------------------------------------------------------------------
    */

    detectSpam() {

        const now = Date.now();

        const recent =
            this.renderHistory.filter(

                item =>

                    now - item.timestamp < 1000

            );

        if (recent.length > 10) {

            BuilderLogger.warn(
                'RENDER SPAM DETECTED'
            );

            BuilderLogger.table(recent);

        }

    }

};