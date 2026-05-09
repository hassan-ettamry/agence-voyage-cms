window.BuilderRightSidebar = {

    /*
    |--------------------------------------------------------------
    | Render Layers
    |--------------------------------------------------------------
    */

    render() {

        const tree =
            document.getElementById(
                'layers-tree'
            );

        const empty =
            document.getElementById(
                'layers-empty'
            );

        if (!tree) return;

        /*
        |----------------------------------------------------------
        | Get Canvas Elements
        |----------------------------------------------------------
        */

        const elements =
            document.querySelectorAll(
                '#canvas [data-index]'
            );

        /*
        |----------------------------------------------------------
        | Empty State
        |----------------------------------------------------------
        */

        if (!elements.length) {

            tree.innerHTML = '';

            empty?.classList.remove(
                'hidden'
            );

            return;
        }

        empty?.classList.add(
            'hidden'
        );

        /*
        |----------------------------------------------------------
        | Build Layers
        |----------------------------------------------------------
        */

        let html = '';

        elements.forEach(element => {

            const index =
                element.dataset.index;

            const type =
                element.dataset.type
                || 'element';

            html += `

                <div

                    data-layer-index="${index}"

                    class="
                        layer-item
                        flex
                        items-center
                        justify-between
                        gap-2
                        px-3
                        py-2
                        border-b
                        border-gray-100
                        cursor-pointer
                        hover:bg-gray-50
                        text-xs
                        transition-all
                    "

                    onclick="
                        BuilderRightSidebar.select(
                            ${index}
                        )
                    "
                >

                    <div class="
                        flex
                        items-center
                        gap-2
                    ">

                        <span class="
                            uppercase
                            text-gray-400
                            font-bold
                            text-[10px]
                        ">
                            ${type}
                        </span>

                    </div>

                </div>

            `;
        });

        tree.innerHTML = html;
    },

    /*
    |--------------------------------------------------------------
    | Select Layer
    |--------------------------------------------------------------
    */

    select(index) {

        const element =
            document.querySelector(
                `[data-index="${index}"]`
            );

        if (!element) return;

        /*
        |----------------------------------------------------------
        | Trigger Element Click
        |----------------------------------------------------------
        */

        element.click();

        /*
        |----------------------------------------------------------
        | Scroll Into View
        |----------------------------------------------------------
        */

        element.scrollIntoView({

            behavior: 'smooth',

            block: 'center'

        });

    },

    /*
    |--------------------------------------------------------------
    | Highlight Active Layer
    |--------------------------------------------------------------
    */

    highlightElement(element) {

        document
            .querySelectorAll('.layer-item')
            .forEach(item => {

                item.classList.remove(
                    'bg-blue-50',
                    'text-blue-600'
                );

            });

        if (!element) return;

        const index =
            element.dataset.index;

        const layer =
            document.querySelector(
                `[data-layer-index="${index}"]`
            );

        if (!layer) return;

        layer.classList.add(
            'bg-blue-50',
            'text-blue-600'
        );

    },

    /*
    |--------------------------------------------------------------
    | Toggle Sidebar
    |--------------------------------------------------------------
    */

    toggle() {

        document
            .getElementById('right-panel')
            ?.classList.toggle('hidden');

    }

};

/*
|--------------------------------------------------------------
| Init
|--------------------------------------------------------------
*/

document.addEventListener(

    'DOMContentLoaded',

    () => {

        setTimeout(() => {

            BuilderRightSidebar.render();

        }, 300);

    }

);