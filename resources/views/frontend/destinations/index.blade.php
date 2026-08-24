@extends('frontend.layout')

@section('content')
<section class="site-section site-section--dark">
    <div class="site-container py-8 sm:py-14">
        <div class="site-eyebrow" style="color: var(--site-accent);">EXPLORE THE WORLD</div>
        <h1 class="site-heading mt-4 text-[clamp(3rem,7vw,6.5rem)]">Places that move you</h1>
        <p class="mt-5 max-w-2xl text-lg leading-8 text-white/75">Handpicked destinations, local insight, and journeys designed around how you want to travel.</p>
    </div>
</section>

@include('components.builder.destination-grid', [
    'props' => $catalogProps,
    'type' => 'destination-grid',
    'nodeId' => null,
    'isEditor' => false,
    'isPreview' => false,
    'isLive' => true,
])
@endsection
