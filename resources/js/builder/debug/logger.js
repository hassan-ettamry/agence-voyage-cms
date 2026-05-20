window.BuilderLogs = [];

window.BuilderLogger = {

    enabled: true,

    /*
    |--------------------------------------------------------------------------
    | Smart Log Filters
    |--------------------------------------------------------------------------
    */

    shouldLog(type) {

        switch (type) {

            case 'render':
                return BuilderConfig.renderLogs;

            case 'history':
                return BuilderConfig.historyLogs;

            case 'selection':
                return BuilderConfig.selectionLogs;

            case 'interaction':
                return BuilderConfig.interactionLogs;

            case 'drag':
                return BuilderConfig.dragLogs;

            case 'move':
                return BuilderConfig.moveLogs;

            case 'drop':
                return BuilderConfig.dropLogs;

            case 'structure':
                return BuilderConfig.structureLogs;

            default:
                return true;

        }

    },

    maxLogs: 5000,

    colors: {

        info: '#3b82f6',
        success: '#10b981',
        warning: '#f59e0b',
        error: '#ef4444',
        drag: '#8b5cf6',
        render: '#06b6d4',
        state: '#14b8a6',
        history: '#ec4899'

    },

    /*
    |--------------------------------------------------------------------------
    | Time
    |--------------------------------------------------------------------------
    */

    time() {

        return new Date()
            .toLocaleTimeString();

    },

    /*
    |--------------------------------------------------------------------------
    | Save Internal Log
    |--------------------------------------------------------------------------
    */

    save(type, label, data = null) {

        BuilderLogs.push({

            type,

            label,

            data,

            time: this.time(),

            timestamp: Date.now()

        });

        /*
        |--------------------------------------------------------------------------
        | Limit Logs
        |--------------------------------------------------------------------------
        */

        if (
            BuilderLogs.length >
            this.maxLogs
        ) {

            BuilderLogs.shift();

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Group
    |--------------------------------------------------------------------------
    */

    group(title, color = '#3b82f6') {

        if (!this.enabled) return;

        this.save(
            'group',
            title
        );

        console.group(

            `%c${this.time()} | ${title}`,

            `
                color:white;
                background:${color};
                padding:3px 8px;
                border-radius:4px;
                font-weight:bold;
            `
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Log
    |--------------------------------------------------------------------------
    */

    log(label, data = null) {

        if (!this.enabled) return;

        this.save(
            'log',
            label,
            data
        );

        console.log(

            `%c${label}`,

            'color:#94a3b8;font-weight:bold;',

            data

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Success
    |--------------------------------------------------------------------------
    */

    success(label, data = null) {

        if (!this.enabled) return;

        this.save(
            'success',
            label,
            data
        );

        console.log(

            `%c${label}`,

            'color:#10b981;font-weight:bold;',

            data

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Warning
    |--------------------------------------------------------------------------
    */

    warn(label, data = null) {

        if (!this.enabled) return;

        this.save(
            'warning',
            label,
            data
        );

        console.warn(

            `%c${label}`,

            'color:#f59e0b;font-weight:bold;',

            data

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Error
    |--------------------------------------------------------------------------
    */

    error(label, data = null) {

        if (!this.enabled) return;

        this.save(
            'error',
            label,
            data
        );

        console.error(

            `%c${label}`,

            'color:#ef4444;font-weight:bold;',

            data

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    table(data) {

        if (!this.enabled) return;

        this.save(
            'table',
            'TABLE',
            data
        );

        console.table(data);

    },

    /*
    |--------------------------------------------------------------------------
    | End Group
    |--------------------------------------------------------------------------
    */

    end() {

        if (!this.enabled) return;

        this.save(
            'groupEnd',
            'GROUP END'
        );

        console.groupEnd();

    },

    /*
    |--------------------------------------------------------------------------
    | Clear Logs
    |--------------------------------------------------------------------------
    */

    clear() {

        BuilderLogs = [];

        console.clear();

        console.log(

            '%cBUILDER LOGS CLEARED',

            `
                color:white;
                background:#ef4444;
                padding:4px 8px;
                border-radius:4px;
                font-weight:bold;
            `

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Download Logs
    |--------------------------------------------------------------------------
    */

    download() {

        const blob = new Blob(

            [

                JSON.stringify(
                    BuilderLogs,
                    null,
                    2
                )

            ],

            {

                type: 'application/json'

            }

        );

        const url =
            URL.createObjectURL(blob);

        const a =
            document.createElement('a');

        a.href = url;

        a.download =
            `builder-logs-${Date.now()}.json`;

        a.click();

        URL.revokeObjectURL(url);

        this.success(
            'LOG FILE DOWNLOADED'
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Print Internal Logs
    |--------------------------------------------------------------------------
    */

    printLogs() {

        console.table(
            BuilderLogs
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Stats
    |--------------------------------------------------------------------------
    */

    stats() {

        const stats = {

            total:
                BuilderLogs.length,

            logs:
                BuilderLogs.filter(
                    x => x.type === 'log'
                ).length,

            warnings:
                BuilderLogs.filter(
                    x => x.type === 'warning'
                ).length,

            errors:
                BuilderLogs.filter(
                    x => x.type === 'error'
                ).length,

            success:
                BuilderLogs.filter(
                    x => x.type === 'success'
                ).length

        };

        console.table(stats);

        return stats;

    }

};
