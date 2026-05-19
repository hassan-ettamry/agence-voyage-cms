window.BuilderHtmlEscape = {

    /*
    |--------------------------------------------------------------------------
    | HTML Escape
    |--------------------------------------------------------------------------
    */

    html(value) {

        const text =
            value === null || value === undefined
                ? ''
                : String(value);

        return text.replace(
            /[&<>"']/g,
            character => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            })[character]
        );

    },

    /*
    |--------------------------------------------------------------------------
    | Attribute Escape
    |--------------------------------------------------------------------------
    */

    attribute(value) {

        return this.html(value);

    },

    /*
    |--------------------------------------------------------------------------
    | Inline JavaScript String Escape
    |--------------------------------------------------------------------------
    */

    jsString(value) {

        const text =
            value === null || value === undefined
                ? ''
                : String(value);

        return this.attribute(
            JSON.stringify(text)
        );

    }

};
