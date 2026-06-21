@props([
    'previous' => null,
    'next' => null,
])

@php
    $first = \App\Models\Comic::firstComic();
    $latest = \App\Models\Comic::latestComic();
    $btn = 'flex-1 text-center px-3 py-2 rounded-md text-sm font-semibold border border-black/10 transition';
    $on = 'bg-white hover:bg-[var(--color-katt-accent)] hover:text-white hover:border-transparent';
    $off = 'opacity-40 cursor-not-allowed bg-white';
@endphp

<nav {{ $attributes->merge(['class' => 'w-full flex items-stretch gap-2']) }} aria-label="Comic navigation">
    @if($first && $previous)
        <a href="{{ $first->url }}" class="{{ $btn }} {{ $on }}" rel="first" aria-label="First comic">&laquo; First</a>
    @else
        <span class="{{ $btn }} {{ $off }}">&laquo; First</span>
    @endif

    @if($previous)
        <a href="{{ $previous->url }}" data-nav="prev" rel="prev" class="{{ $btn }} {{ $on }}" aria-label="Previous comic">&lsaquo; Prev</a>
    @else
        <span class="{{ $btn }} {{ $off }}">&lsaquo; Prev</span>
    @endif

    @if($next)
        <a href="{{ $next->url }}" data-nav="next" rel="next" class="{{ $btn }} {{ $on }}" aria-label="Next comic">Next &rsaquo;</a>
    @else
        <span class="{{ $btn }} {{ $off }}">Next &rsaquo;</span>
    @endif

    @if($latest && $next)
        <a href="{{ $latest->url }}" class="{{ $btn }} {{ $on }}" rel="last" aria-label="Latest comic">Latest &raquo;</a>
    @else
        <span class="{{ $btn }} {{ $off }}">Latest &raquo;</span>
    @endif
</nav>
