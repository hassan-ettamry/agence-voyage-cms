@extends('builder.index')

@section('title', 'Edit Page')

@section('builder-data')

<script>

    /*
    |--------------------------------------------------------------------------
    | Builder Initial Data
    |--------------------------------------------------------------------------
    */

    window.pageId = @json($page->id);

    window.initialStructure = @json($page->structure ?? []);

</script>

@endsection