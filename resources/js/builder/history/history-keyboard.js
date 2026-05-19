window.BuilderHistoryKeyboard = {

    /*
    |--------------------------------------------------------------------------
    | Bind Keyboard Shortcuts
    |--------------------------------------------------------------------------
    */

    bind() {

        document.addEventListener(

            'keydown',

            (event) => {

                /*
                |--------------------------------------------------------------------------
                | Ignore Editable Elements
                |--------------------------------------------------------------------------
                */

                const active =
                    document.activeElement;

                const isTyping =

                    active?.tagName === 'INPUT'

                    ||

                    active?.tagName === 'TEXTAREA'

                    ||

                    active?.isContentEditable;

                if (isTyping) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | CTRL + Z
                |--------------------------------------------------------------------------
                */

                if (

                    event.ctrlKey
                    &&

                    event.key === 'z'

                ) {

                    event.preventDefault();

                    BuilderHistory.undo();

                }

                /*
                |--------------------------------------------------------------------------
                | CTRL + Y
                |--------------------------------------------------------------------------
                */

                if (

                    event.ctrlKey
                    &&

                    event.key === 'y'

                ) {

                    event.preventDefault();

                    BuilderHistory.redo();

                }

                /*
                |--------------------------------------------------------------------------
                | DELETE / BACKSPACE
                |--------------------------------------------------------------------------
                */

                if (

                    event.key === 'Delete'

                    ||

                    event.key === 'Backspace'

                ) {

                    event.preventDefault();

                    BuilderSelection.delete();

                }

            }

        );

    }

};
