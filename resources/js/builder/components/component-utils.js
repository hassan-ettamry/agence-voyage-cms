window.BuilderComponentUtils = {

    /*
    |--------------------------------------------------------------------------
    | Generate Node ID
    |--------------------------------------------------------------------------
    */

    generateId() {
        if (globalThis.crypto?.randomUUID) {
            return `node_${globalThis.crypto.randomUUID()}`;
        }

        return `node_${Date.now().toString(36)}_${Math.random().toString(36).slice(2, 10)}`;

    },

    /*
    |--------------------------------------------------------------------------
    | Deep Clone
    |--------------------------------------------------------------------------
    */

    clone(data) {

        return structuredClone(data);

    }

};
