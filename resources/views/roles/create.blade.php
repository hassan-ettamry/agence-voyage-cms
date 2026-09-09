@extends('layouts.admin')

@section('title', 'Create Role')

@section('topbar')
    <x-layout.topbar>
        <x-slot name="left">
            <a href="{{ route('roles.index') }}">Back to roles</a>
        </x-slot>
    </x-layout.topbar>
@endsection

@section('content')
    <div class="mx-auto max-w-5xl">
        @include('roles.partials.form')
    </div>
@endsection
