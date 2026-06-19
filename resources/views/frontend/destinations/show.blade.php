@extends('frontend.layout')

@section('content')
<article class="mx-auto max-w-7xl px-6 py-12">
    @php($cover = $destination->media->first())
    <div class="overflow-hidden rounded-xl bg-slate-100">
        @if($cover)
            <img src="{{ $cover->url }}" alt="{{ $cover->alt_text ?? $destination->name }}" class="h-[420px] w-full object-cover">
        @endif
    </div>

    <div class="mt-8 max-w-3xl">
        <div class="text-sm font-semibold uppercase text-indigo-500">{{ $destination->country }}</div>
        <h1 class="mt-2 text-4xl font-bold text-gray-900">{{ $destination->name }}</h1>
        <p class="mt-4 text-lg leading-8 text-gray-600">{{ $destination->description }}</p>
    </div>

    @if($destination->offers->isNotEmpty())
        <h2 class="mt-12 text-2xl font-bold text-gray-900">Offers</h2>
        <div class="mt-5 grid gap-6 md:grid-cols-3">
            @foreach($destination->offers as $offer)
                @include('frontend.partials.offer-card', ['offer' => $offer])
            @endforeach
        </div>
    @endif
</article>
@endsection
