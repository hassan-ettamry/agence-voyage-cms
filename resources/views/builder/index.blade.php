@extends('layouts.builder')

@section('content')

<div class="flex flex-col h-full">

    {{-- Topbar --}}
    @include('builder.partials.topbar')

    {{-- Builder Workspace --}}
    <div class="flex flex-1 overflow-hidden">

        {{-- Left Sidebar --}}
        @include('builder.partials.left-sidebar')

        {{-- Canvas --}}
        @include('builder.partials.canvas')

        {{-- Right Sidebar --}}
        @include('builder.partials.right-sidebar')

    </div>

</div>

{{-- Global Builder Modals --}}
@include('builder.partials.modals')

@endsection