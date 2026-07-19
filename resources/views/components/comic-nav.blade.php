@props([
    'previous' => null,
    'next' => null,
    // The strip being viewed, so Random can exclude it (?not=) and never
    // reload the same page.
    'current' => null,
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

    // Shared button shape: min-h-[48px] is a generous thumb target.
    $base = 'flex-1 flex items-center justify-center min-h-[48px] px-3 py-3 rounded-xl text-sm font-bold border-2 transition select-none';
    $on = 'bg-white border-black/10 hover:border-[var(--color-katt-accent)] hover:text-[var(--color-katt-accent)]';
    $off = 'bg-white border-black/5 text-black/25 cursor-not-allowed';
@endphp

@if($previewToken)
    {{-- Preview keeps the classic first/prev/next/latest stepper: navigation
         here spans the whole scheduled pipeline, and Random (published-only)
         would jump you out of preview. --}}
    <nav {{ $attributes->merge(['class' => 'w-full flex items-stretch gap-2']) }} aria-label="Comic navigation">
        @if($first && $previous)
            <a href="{{ $first->url }}{{ $q }}" class="{{ $base }} {{ $on }}" rel="first" aria-label="First comic">&laquo; First</a>
        @else
            <span class="{{ $base }} {{ $off }}">&laquo; First</span>
        @endif

        @if($previous)
            <a href="{{ $previous->url }}{{ $q }}" data-nav="prev" rel="prev" class="{{ $base }} {{ $on }}" aria-label="Previous comic">&lsaquo; Prev</a>
        @else
            <span class="{{ $base }} {{ $off }}">&lsaquo; Prev</span>
        @endif

        @if($next)
            <a href="{{ $next->url }}{{ $q }}" data-nav="next" rel="next" class="{{ $base }} {{ $on }}" aria-label="Next comic">Next &rsaquo;</a>
        @else
            <span class="{{ $base }} {{ $off }}">Next &rsaquo;</span>
        @endif

        @if($latest && $next)
            <a href="{{ $latest->url }}{{ $q }}" class="{{ $base }} {{ $on }}" rel="last" aria-label="Latest comic">Latest &raquo;</a>
        @else
            <span class="{{ $base }} {{ $off }}">Latest &raquo;</span>
        @endif
    </nav>
@else
    <nav {{ $attributes->merge(['class' => 'w-full']) }} aria-label="Comic navigation">
        {{-- Primary row: Prev (older) · Random (always live) · Next (newer). --}}
        <div class="flex items-stretch gap-2">
            @if($previous)
                <a href="{{ $previous->url }}" data-nav="prev" rel="prev" class="{{ $base }} {{ $on }}" aria-label="Previous (older) comic">&lsaquo; Prev</a>
            @else
                <span class="{{ $base }} {{ $off }}" aria-disabled="true">&lsaquo; Prev</span>
            @endif

            {{-- The star of the show for a gag-a-day archive: never disabled. --}}
            <a href="{{ $current ? route('comics.random', ['not' => $current->id]) : route('comics.random') }}"
               rel="nofollow"
               class="flex-1 flex items-center justify-center gap-1.5 min-h-[48px] px-3 py-3 rounded-xl text-sm font-bold border-2 border-transparent bg-[var(--color-katt-accent)] text-white shadow-sm transition hover:brightness-95 active:translate-y-px"
               aria-label="Show me a random strip">
                <span aria-hidden="true">🎲</span> Random
            </a>

            @if($next)
                <a href="{{ $next->url }}" data-nav="next" rel="next" class="{{ $base }} {{ $on }}" aria-label="Next (newer) comic">Next &rsaquo;</a>
            @else
                <span class="{{ $base }} {{ $off }}" aria-disabled="true">Next &rsaquo;</span>
            @endif
        </div>

        {{-- Secondary row: rare edge-jumps, demoted to small links. Each hides
             when you're already at that end, so nothing dead is ever shown. --}}
        @if(($first && $previous) || ($latest && $next))
            <div class="mt-2.5 flex items-center justify-center gap-3 text-xs text-black/45">
                @if($first && $previous)
                    <a href="{{ $first->url }}" rel="first" class="py-1 underline decoration-black/15 hover:text-[var(--color-katt-accent)]">&laquo; First strip</a>
                @endif
                @if(($first && $previous) && ($latest && $next))
                    <span aria-hidden="true" class="text-black/20">&middot;</span>
                @endif
                @if($latest && $next)
                    <a href="{{ $latest->url }}" rel="last" class="py-1 underline decoration-black/15 hover:text-[var(--color-katt-accent)]">Latest strip &raquo;</a>
                @endif
            </div>
        @endif
    </nav>
@endif
