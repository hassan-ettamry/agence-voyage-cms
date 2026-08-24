@extends('frontend.layout')

@section('content')
<section class="site-section site-section--dark">
    <div class="site-container py-8 sm:py-14">
        <div class="site-eyebrow" style="color: var(--site-accent);">CURATED JOURNEYS</div>
        <h1 class="site-heading mt-4 text-[clamp(3rem,7vw,6.5rem)]">Travel offers, made personal</h1>
        <p class="mt-5 max-w-2xl text-lg leading-8 text-white/75">Compare inspiring itineraries and find the journey that matches your pace, interests, and budget.</p>
    </div>
</section>

@include('components.builder.offer-grid', [
    'props' => $catalogProps,
    'type' => 'offer-grid',
    'nodeId' => null,
    'isEditor' => false,
    'isPreview' => false,
    'isLive' => true,
])
@endsection
