@extends('layouts.admin')

@section('topbar')
<x-layout.topbar>
    <x-slot name="left">
        <a href="{{ route('destinations.index') }}">Back to destinations</a>
    </x-slot>
</x-layout.topbar>
@endsection

@section('content')
<div class="mx-auto max-w-5xl rounded-xl bg-white p-6 shadow-sm">
    <h1 class="mb-5 text-lg font-semibold text-gray-800">Edit Destination</h1>
    <form method="POST" action="{{ route('destinations.update', $destination) }}" class="space-y-4">
        @csrf
        @method('PUT')
        @include('destinations.partials.form', ['destination' => $destination, 'mediaAssets' => $mediaAssets])
        <div class="flex justify-end gap-2 pt-2">
            <a href="{{ route('destinations.index') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600">Cancel</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Save</button>
        </div>
    </form>
</div>
@endsection
