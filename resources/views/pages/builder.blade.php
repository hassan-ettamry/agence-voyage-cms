@extends('builder.index')

@section('title', 'Page Builder')

@section('builder-data')

<script>

    /*
    |--------------------------------------------------------------------------
    | Builder Initial Data
    |--------------------------------------------------------------------------
    */

    window.pageId =
        @json($page->id);

    window.builderPreviewUrl =
        @json(route('pages.preview', $page));

    window.builderPublishUrl =
        @json(route('pages.publish', $page));

    window.builderPageStatus =
        @json($page->status);

    window.builderMediaPickerUrl =
        @json(route('media.picker'));

    window.builderSavedBlocksUrl =
        @json(route('builder-saved-blocks.index'));

    window.builderDataOptions =
        @json($builderDataOptions ?? ['destinations' => [], 'offers' => []]);

    window.initialStructure =
        @json($builderStructure ?? []);

    window.builderMenuItems =
        @json($menuItems ?? []);

    /*
    |--------------------------------------------------------------------------
    | Builder Components Registry
    |--------------------------------------------------------------------------
    */

    window.builderComponents =
        @json($widgets);

</script>

@endsection
