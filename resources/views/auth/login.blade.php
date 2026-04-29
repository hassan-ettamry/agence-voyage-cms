@extends('layouts.auth')

@section('title','Login')

@section('content')
<div class="w-full max-w-md">

    <div class="text-center mb-6">
        <div class="w-12 h-12 bg-indigo-600 text-white flex items-center justify-center rounded-full mx-auto">IE</div>
        <h2 class="mt-4 text-2xl font-semibold">Se connecter</h2>
    </div>

    <x-ui.card>
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <x-ui.input name="email" placeholder="Email" class="mb-4" />
            <x-ui.input name="password" type="password" placeholder="Password" class="mb-4" />

            <x-ui.button class="w-full">Continue</x-ui.button>
        </form>
    </x-ui.card>

</div>
@endsection