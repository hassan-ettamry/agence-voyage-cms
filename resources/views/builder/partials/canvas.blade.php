<div
    id="builder-workspace"
    class="
        flex-1
        overflow-auto
        bg-gray-100
        flex
        flex-col
        items-center
        py-6
        px-4
        relative
    "
>

    <div
        id="canvas-wrapper"
        class="
            w-full
            max-w-full
            transition-all
            duration-300
            mx-auto
            relative
        "
    >

        {{-- Overlay Root --}}
        <div
            id="builder-overlay-root"
            class="
                absolute
                inset-0
                pointer-events-none
                z-50
            "
        ></div>

        {{-- Canvas --}}
        <div
            id="canvas"
            data-root-dropzone="true"
            class="
                bg-white
                min-h-[600px]
                border
                border-dashed
                border-gray-300
                shadow-sm
                relative
            "
            style="{{ $builderThemeCss ?? '' }} background-color: var(--site-background, #ffffff); color: var(--site-text, #0f172a); font-family: var(--site-body-font, ui-sans-serif, system-ui, sans-serif);"
        >

            @include('builder.components.empty-state')

        </div>

    </div>

    <div
        id="builder-template-library"
        class="
            fixed
            inset-0
            z-[60]
            hidden
            items-center
            justify-center
            bg-slate-950/45
            px-8
            py-8
        "
    >
        <div
            class="
                flex
                h-[90vh]
                max-h-[930px]
                w-[70vw]
                min-w-[760px]
                max-w-7xl
                flex-col
                overflow-hidden
                bg-white
                shadow-2xl
                ring-1
                ring-slate-200
            "
        >
            <div
                class="
                    grid
                    h-12
                    shrink-0
                    grid-cols-[1fr_1fr_1fr_48px]
                    border-b
                    border-slate-200
                    bg-white
                "
            >
                <button
                    type="button"
                    data-action="template-library-tab"
                    data-template-tab="blocks"
                    class="flex items-center justify-center gap-2 border-b-2 border-indigo-600 bg-indigo-50 px-4 text-sm font-semibold text-slate-900"
                >
                    <span aria-hidden="true" class="grid h-4 w-4 grid-cols-2 gap-0.5">
                        <span class="border border-current"></span>
                        <span class="border border-current"></span>
                        <span class="border border-current"></span>
                        <span class="border border-current"></span>
                    </span>
                    Sections
                </button>

                <button
                    type="button"
                    data-action="template-library-tab"
                    data-template-tab="templates"
                    class="flex items-center justify-center gap-2 border-b-2 border-transparent bg-white px-4 text-sm font-semibold text-slate-500"
                >
                    <span aria-hidden="true" class="grid h-4 w-4 grid-cols-2 gap-px">
                        <span class="bg-current"></span>
                        <span class="bg-current"></span>
                        <span class="bg-current"></span>
                        <span class="bg-current"></span>
                    </span>
                    Page Blocks
                </button>

                <button
                    type="button"
                    data-action="template-library-tab"
                    data-template-tab="my-templates"
                    class="flex items-center justify-center gap-2 border-b-2 border-transparent bg-white px-4 text-sm font-semibold text-slate-500"
                >
                    <span aria-hidden="true" class="h-4 w-3 rounded-sm border-2 border-current"></span>
                    My Blocks
                </button>

                <button
                    type="button"
                    data-action="close-template-library"
                    class="
                        flex
                        h-12
                        w-12
                        items-center
                        justify-center
                        border-l
                        border-slate-200
                        bg-white
                        text-2xl
                        font-light
                        leading-none
                        text-slate-500
                        hover:bg-slate-50
                        hover:text-slate-900
                    "
                >
                    &times;
                </button>
            </div>

            <div class="flex min-h-0 flex-1">
                <aside class="w-44 shrink-0 overflow-y-auto border-r border-slate-200 bg-white px-5 py-6">
                    <h3 class="mb-3 text-sm font-bold text-slate-900">
                        Categories
                    </h3>

                    <div
                        id="builder-template-categories"
                        class="space-y-1"
                    ></div>
                </aside>

                <section class="flex min-w-0 flex-1 flex-col bg-white">
                    <div class="shrink-0 px-5 pb-4 pt-6">
                        <div class="flex h-9 w-64 overflow-hidden border border-slate-200 bg-white">
                            <input
                                type="search"
                                data-template-search
                                placeholder="Search here..."
                                class="min-w-0 flex-1 border-0 px-3 text-sm font-semibold text-slate-700 outline-none placeholder:text-slate-400"
                            >
                            <div class="flex w-10 items-center justify-center border-l border-slate-200 text-slate-400">
                                <span class="h-3.5 w-3.5 rounded-full border-2 border-current"></span>
                            </div>
                        </div>
                    </div>

                    <div class="min-h-0 flex-1 overflow-y-auto px-5 pb-6">
                        <div
                            id="builder-template-grid"
                            class="grid grid-cols-1 gap-x-6 gap-y-5 md:grid-cols-2 xl:grid-cols-4"
                        ></div>

                        <div
                            id="builder-template-empty"
                            class="hidden py-20 text-center text-sm font-medium text-slate-400"
                        >
                            No blocks found.
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <div
        id="builder-section-layout-picker"
        class="
            fixed
            inset-0
            z-[70]
            hidden
            items-center
            justify-center
            bg-slate-950/65
            px-8
            py-8
        "
    >
        <div class="w-full max-w-5xl bg-white shadow-2xl">
            <div class="relative m-6 border-2 border-dashed border-slate-200 px-5 py-20">
                <button
                    type="button"
                    data-action="close-section-layout-picker"
                    class="absolute right-4 top-4 text-2xl font-light leading-none text-slate-500 hover:text-slate-900"
                >
                    &times;
                </button>

                <div class="grid grid-cols-4 gap-x-6 gap-y-7">
                    <button
                        type="button"
                        data-action="choose-section-layout"
                        data-section-layout="normal"
                        class="group grid h-20 gap-1 rounded-sm focus:outline-none"
                        title="Normal section"
                    >
                        <span class="pointer-events-none block h-full w-full rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                    </button>

                    <button
                        type="button"
                        data-action="choose-section-layout"
                        data-section-layout="two-blocks"
                        class="group grid h-20 grid-cols-2 gap-1 focus:outline-none"
                        title="Two blocks"
                    >
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                    </button>

                    <button
                        type="button"
                        data-action="choose-section-layout"
                        data-section-layout="three-blocks"
                        class="group grid h-20 grid-cols-3 gap-1 focus:outline-none"
                        title="Three blocks"
                    >
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                    </button>

                    <button
                        type="button"
                        data-action="choose-section-layout"
                        data-section-layout="four-blocks"
                        class="group grid h-20 grid-cols-4 gap-1 focus:outline-none"
                        title="Four blocks"
                    >
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                    </button>

                    <button
                        type="button"
                        data-action="choose-section-layout"
                        data-section-layout="five-blocks"
                        class="group grid h-20 grid-cols-5 gap-1 focus:outline-none"
                        title="Five blocks"
                    >
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                    </button>

                    <button
                        type="button"
                        data-action="choose-section-layout"
                        data-section-layout="six-blocks"
                        class="group grid h-20 grid-cols-6 gap-1 focus:outline-none"
                        title="Six blocks"
                    >
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                    </button>

                    <button
                        type="button"
                        data-action="choose-section-layout"
                        data-section-layout="left-sidebar"
                        class="group grid h-20 grid-cols-[1fr_3fr] gap-1 focus:outline-none"
                        title="Left sidebar"
                    >
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                    </button>

                    <button
                        type="button"
                        data-action="choose-section-layout"
                        data-section-layout="right-sidebar"
                        class="group grid h-20 grid-cols-[3fr_1fr] gap-1 focus:outline-none"
                        title="Right sidebar"
                    >
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                    </button>

                    <button
                        type="button"
                        data-action="choose-section-layout"
                        data-section-layout="narrow-wide"
                        class="group grid h-20 grid-cols-[1fr_4fr] gap-1 focus:outline-none"
                        title="Narrow wide"
                    >
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                    </button>

                    <button
                        type="button"
                        data-action="choose-section-layout"
                        data-section-layout="wide-narrow"
                        class="group grid h-20 grid-cols-[4fr_1fr] gap-1 focus:outline-none"
                        title="Wide narrow"
                    >
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                    </button>

                    <button
                        type="button"
                        data-action="choose-section-layout"
                        data-section-layout="main-sidebar"
                        class="group grid h-20 grid-cols-[2fr_1fr] gap-1 focus:outline-none"
                        title="Main sidebar"
                    >
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                    </button>

                    <button
                        type="button"
                        data-action="choose-section-layout"
                        data-section-layout="sidebar-main"
                        class="group grid h-20 grid-cols-[1fr_2fr] gap-1 focus:outline-none"
                        title="Sidebar main"
                    >
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                        <span class="pointer-events-none rounded-sm bg-slate-300 transition group-hover:bg-sky-500 group-focus-visible:bg-sky-500"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
