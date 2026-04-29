@extends('layouts.auth')

@section('title','Register')

@section('content')
<div class="w-full max-w-md">

    <div class="text-center mb-6">
        <h2 class="text-2xl font-semibold">Créer un compte</h2>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <input name="agency_name" class="input mb-4" placeholder="Agency name">
            <input name="name" class="input mb-4" placeholder="Name">
            <input name="email" class="input mb-4" placeholder="Email">
            <input name="password" type="password" class="input mb-4" placeholder="Password">
            <input name="password_confirmation" type="password" class="input mb-4" placeholder="Confirm">

            <button class="btn-primary w-full">Create</button>
        </form>
    </div>

</div>
@endsection