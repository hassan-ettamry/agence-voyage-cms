<section 
    data-index="{{ $index ?? 0 }}"
    class="{{ $props['class'] ?? '' }}"
    style="cursor:pointer;"
>
    <h2>{{ $props['title'] ?? 'Section Title' }}</h2>

    {!! $children !!}
</section>