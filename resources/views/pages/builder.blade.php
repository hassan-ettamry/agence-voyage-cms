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
