@props([
    'previous' => null,
    'next' => null,
    // Preview mode: append the secret token to every link and let the caller
    // override first/latest to span the whole (incl. scheduled) pipeline.
    'previewToken' => null,
    'first' => null,
    'latest' => null,
])

@php
    $first = $first ?? \App\Models\Comic::firstComic();
    $latest = $latest ?? \App\Models\Comic::latestComic();
    // Carry the preview token through so future strips stay reachable.
    $q = $previewToken ? '?preview='.urlencode($previewToken) : '';
    // min-h-[44px] + flex centering keeps every control a comfortable thumb
    // target on mobile (Apple HIG / WCAG 2.5.5). px-2 so four fit across a phone.
    $btn = 'flex-1 flex items-center justify-center min-h-[44px] px-2 py-2.5 rounded-lg text-sm font-semibold border border-black/10 transition';
    $on = 'bg-white hover:bg-[var(--color-katt-accent)] hover:text-white hover:border-transparent';
    $off = 'opacity-40 cursor-not-allowed bg-white';
@endphp

<nav {{ $attributes->merge(['class' => 'w-full flex items-stretch gap-2']) }} aria-label="Comic navigation">
    @if($first && $previous)
        <a href="{{ $first->url }}{{ $q }}" class="{{ $btn }} {{ $on }}" rel="first" aria-label="First comic">&laquo; First</a>
    @else
        <span class="{{ $btn }} {{ $off }}">&laquo; First</span>
    @endif

    @if($previous)
        <a href="{{ $previous->url }}{{ $q }}" data-nav="prev" rel="prev" class="{{ $btn }} {{ $on }}" aria-label="Previous comic">&lsaquo; Prev</a>
    @else
        <span class="{{ $btn }} {{ $off }}">&lsaquo; Prev</span>
    @endif

    @if($next)
        <a href="{{ $next->url }}{{ $q }}" data-nav="next" rel="next" class="{{ $btn }} {{ $on }}" aria-label="Next comic">Next &rsaquo;</a>
    @else
        <span class="{{ $btn }} {{ $off }}">Next &rsaquo;</span>
    @endif

    @if($latest && $next)
        <a href="{{ $latest->url }}{{ $q }}" class="{{ $btn }} {{ $on }}" rel="last" aria-label="Latest comic">Latest &raquo;</a>
    @else
        <span class="{{ $btn }} {{ $off }}">Latest &raquo;</span>
    @endif
</nav>
