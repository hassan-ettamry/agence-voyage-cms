@extends('frontend.layout')

@section('content')
    @if($previewMode ?? false)
        <div class="sticky top-0 z-[60] bg-amber-300 px-4 py-2 text-center text-xs font-bold text-amber-950">Draft preview — only signed-in agency users can see this page.</div>
    @endif
    {!! $html !!}
@endsection
