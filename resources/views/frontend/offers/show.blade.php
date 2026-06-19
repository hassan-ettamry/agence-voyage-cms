@extends('frontend.layout')

@section('content')
<article class="mx-auto max-w-5xl px-6 py-12">
    @php($cover = $offer->media ?: $offer->destination?->media?->first())
    <div class="overflow-hidden rounded-xl bg-slate-100">
        @if($cover)
            <img src="{{ $cover->url }}" alt="{{ $cover->alt_text ?? $offer->title }}" class="h-[420px] w-full object-cover">
        @endif
    </div>

    <div class="mt-8">
        @if($offer->destination)
            <a href="{{ route('public.destinations.show', $offer->destination->slug) }}" class="text-sm font-semibold uppercase text-indigo-500">{{ $offer->destination->name }}</a>
        @endif
        <h1 class="mt-2 text-4xl font-bold text-gray-900">{{ $offer->title }}</h1>
        <div class="mt-4 flex flex-wrap gap-3">
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-600">{{ number_format((float) $offer->price, 2) }}</span>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-600">{{ $offer->duration_days }} days</span>
            @if($offer->is_special)
                <span class="rounded-full bg-rose-50 px-3 py-1 text-sm font-semibold text-rose-600">Special</span>
            @endif
        </div>
        <p class="mt-6 text-lg leading-8 text-gray-600">{{ $offer->description }}</p>
    </div>
</article>
@endsection
