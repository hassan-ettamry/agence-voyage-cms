@extends('frontend.layout')

@section('content')
<section class="mx-auto max-w-7xl px-6 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Destinations</h1>
        <p class="mt-2 max-w-2xl text-gray-500">Explore published destinations from our travel agencies.</p>
    </div>

    <div class="grid gap-6 md:grid-cols-3">
        @foreach($destinations as $destination)
            @php($cover = $destination->media->first())
            <a href="{{ app(\App\Services\PublicSiteUrl::class)->destination($siteAgency, $destination) }}" class="overflow-hidden rounded-lg border border-gray-100 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="aspect-video bg-slate-100">
                    @if($cover)
                        <img src="{{ $cover->url }}" alt="{{ $cover->alt_text ?? $destination->name }}" class="h-full w-full object-cover">
                    @endif
                </div>
                <div class="p-5">
                    <div class="text-xs font-semibold uppercase text-indigo-500">{{ $destination->country }}</div>
                    <h2 class="mt-1 text-lg font-semibold text-gray-900">{{ $destination->name }}</h2>
                    <p class="mt-2 line-clamp-3 text-sm text-gray-500">{{ $destination->description }}</p>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-8">{{ $destinations->links() }}</div>
</section>
@endsection
