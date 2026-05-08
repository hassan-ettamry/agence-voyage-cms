window.BuilderCanvas = {

    /*
    |--------------------------------------------------------------------------
    | Render Full Canvas
    |--------------------------------------------------------------------------
    */

    render() {

        const canvas = document.getElementById('canvas');

        if (!canvas) return;

        /*
        |--------------------------------------------------------------------------
        | Clear Canvas
        |--------------------------------------------------------------------------
        */

        canvas.innerHTML = '';

        /*
        |--------------------------------------------------------------------------
        | Get Structure State
        |--------------------------------------------------------------------------
        */

        const structure = Builder.getStructure();

        /*
        |--------------------------------------------------------------------------
        | Empty State
        |--------------------------------------------------------------------------
        */

        if (!structure.length) {

            canvas.innerHTML = `
                <div
                    id="canvas-empty-state"
                    class="h-[500px] flex items-center justify-center text-gray-400"
                >
                    Drag components here
                </div>
            `;

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Render Components
        |--------------------------------------------------------------------------
        */

        structure.forEach((node, index) => {

            const element = this.renderNode(node, index);

            if (element) {
                canvas.appendChild(element);
            }

        });

    },

    /*
    |--------------------------------------------------------------------------
    | Render Single Node
    |--------------------------------------------------------------------------
    */

    renderNode(node, index = 0) {

        const wrapper = document.createElement('div');

        wrapper.dataset.index = index;

        wrapper.className = `
            canvas-node
            relative
            border-2
            border-transparent
            hover:border-blue-400
            transition-all
        `;

        /*
        |--------------------------------------------------------------------------
        | Components
        |--------------------------------------------------------------------------
        */

        switch (node.type) {

            /*
            |--------------------------------------------------------------------------
            | HERO
            |--------------------------------------------------------------------------
            */

            case 'hero':

                wrapper.innerHTML = `
                    <section class="bg-gray-900 text-white px-16 py-20">

                        <h1
                            class="text-5xl font-bold mb-4 outline-none"
                            contenteditable="true"
                            data-field="title"
                        >
                            ${node.props.title || ''}
                        </h1>

                        <p
                            class="text-gray-300 max-w-xl outline-none"
                            contenteditable="true"
                            data-field="description"
                        >
                            ${node.props.description || ''}
                        </p>

                    </section>
                `;

                break;

            /*
            |--------------------------------------------------------------------------
            | TEXT
            |--------------------------------------------------------------------------
            */

            case 'text':

                wrapper.innerHTML = `
                    <div class="p-4">

                        <p
                            class="text-gray-700 outline-none"
                            contenteditable="true"
                            data-field="text"
                        >
                            ${node.props.text || ''}
                        </p>

                    </div>
                `;

                break;

            /*
            |--------------------------------------------------------------------------
            | HEADING
            |--------------------------------------------------------------------------
            */

            case 'heading':

                wrapper.innerHTML = `
                    <div class="p-4">

                        <h2
                            class="text-3xl font-bold outline-none"
                            contenteditable="true"
                            data-field="text"
                        >
                            ${node.props.text || ''}
                        </h2>

                    </div>
                `;

                break;

            /*
            |--------------------------------------------------------------------------
            | BUTTON
            |--------------------------------------------------------------------------
            */

            case 'button':

                wrapper.innerHTML = `
                    <div class="p-4">

                        <button
                            class="bg-blue-600 text-white px-5 py-2 rounded-lg outline-none"
                            contenteditable="true"
                            data-field="text"
                        >
                            ${node.props.text || 'Button'}
                        </button>

                    </div>
                `;

                break;

            /*
            |--------------------------------------------------------------------------
            | IMAGE
            |--------------------------------------------------------------------------
            */

            case 'image':

                wrapper.innerHTML = `
                    <div class="p-4">

                        <div class="
                            h-64
                            bg-gray-100
                            border-2
                            border-dashed
                            rounded-xl
                            flex
                            items-center
                            justify-center
                            text-gray-400
                        ">
                            ${node.props.alt || 'Image'}
                        </div>

                    </div>
                `;

                break;

            /*
            |--------------------------------------------------------------------------
            | SECTION
            |--------------------------------------------------------------------------
            */

            case 'section':

                wrapper.innerHTML = `
                    <section class="p-10 bg-gray-50">

                        <div class="text-center text-gray-400 italic">
                            Empty Section
                        </div>

                    </section>
                `;

                break;

            /*
            |--------------------------------------------------------------------------
            | ROW
            |--------------------------------------------------------------------------
            */

            case 'row':

                wrapper.innerHTML = `
                    <div class="grid grid-cols-2 gap-4 p-4">

                        <div class="bg-gray-100 h-32 rounded"></div>

                        <div class="bg-gray-100 h-32 rounded"></div>

                    </div>
                `;

                break;

            /*
            |--------------------------------------------------------------------------
            | CONTAINER
            |--------------------------------------------------------------------------
            */

            case 'container':

                wrapper.innerHTML = `
                    <div class="max-w-5xl mx-auto p-4">

                        <div class="border border-dashed rounded-xl p-10 text-center text-gray-400">
                            Container
                        </div>

                    </div>
                `;

                break;

            /*
            |--------------------------------------------------------------------------
            | UNKNOWN
            |--------------------------------------------------------------------------
            */

            default:

                wrapper.innerHTML = `
                    <div class="p-4 text-red-500">

                        Unknown Component:
                        ${node.type}

                    </div>
                `;
        }

        /*
        |--------------------------------------------------------------------------
        | Bind Editable Fields
        |--------------------------------------------------------------------------
        */

        wrapper
            .querySelectorAll('[contenteditable="true"]')
            .forEach(element => {

                element.addEventListener('input', () => {

                    const field = element.dataset.field;

                    if (!field) return;

                    /*
                    |--------------------------------------------------------------------------
                    | Update Builder State
                    |--------------------------------------------------------------------------
                    */

                    Builder.structure[index].props[field] =
                        element.innerText;

                });

            });

        return wrapper;

    }

};

/*
|--------------------------------------------------------------------------
| Initial Render
|--------------------------------------------------------------------------
*/

window.addEventListener('DOMContentLoaded', () => {

    BuilderCanvas.render();

});