@extends('frontend.layout')

@section('content')
<section class="mx-auto max-w-7xl px-6 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Offers</h1>
        <p class="mt-2 max-w-2xl text-gray-500">Discover current published travel offers.</p>
    </div>

    <div class="grid gap-6 md:grid-cols-3">
        @foreach($offers as $offer)
            @include('frontend.partials.offer-card', ['offer' => $offer])
        @endforeach
    </div>

    <div class="mt-8">{{ $offers->links() }}</div>
</section>
@endsection
