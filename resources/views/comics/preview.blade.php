@php
    use Illuminate\Support\Carbon;

    $site = config('comics.site');
    $title = 'Preview — '.$site['name'];
    $description = 'Private preview of the Startup Katt pipeline.';
    $canonical = url()->current();
    $noindex = true;
@endphp

@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-extrabold" style="font-family: var(--font-display)">Preview</h1>
    <p class="mt-1 text-sm text-black/50">
        Private sneak-peek of the whole pipeline — scheduled strips up top, live archive below.
        Don’t share this link. ({{ $scheduled->count() }} scheduled · {{ $published->count() }} live)
    </p>
</div>

{{-- Upcoming / scheduled --}}
<section class="mb-12">
    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
        <span>Upcoming</span>
        <span class="text-xs font-semibold uppercase tracking-wide text-[var(--color-katt-accent)]">not yet public</span>
    </h2>

    @if($scheduled->isEmpty())
        <p class="text-black/60">Nothing scheduled — drop art in the incoming folder and run <code>comics:import</code>.</p>
    @else
        <ul class="space-y-10">
            @foreach($scheduled as $comic)
                @php $days = (int) round(Carbon::today()->diffInDays($comic->published_at, false)); @endphp
                <li>
                    <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1 mb-2">
                        <span class="text-lg font-bold">#{{ $comic->number }} — {{ $comic->title }}</span>
                        <span class="text-sm text-black/50">
                            {{ $comic->published_at->format('l, M j, Y') }}
                            · {{ $days === 0 ? 'today' : ($days === 1 ? 'tomorrow' : "in {$days} days") }}
                        </span>
                    </div>
                    <a href="{{ $comic->url }}?preview={{ urlencode($token) }}" class="block group">
                        <img src="{{ $comic->image_url }}" alt="{{ $comic->alt_text }}" loading="lazy"
                             class="w-full h-auto rounded-lg border border-black/10 shadow-sm bg-white group-hover:shadow-md transition">
                    </a>
                    <dl class="mt-2 text-sm text-black/60 space-y-0.5">
                        <div><span class="font-semibold text-black/70">slug:</span> /comic/{{ $comic->slug }}</div>
                        <div><span class="font-semibold text-black/70">alt:</span> {{ $comic->alt_text ?: '—' }}</div>
                        @if($comic->caption)<div><span class="font-semibold text-black/70">caption:</span> {{ $comic->caption }}</div>@endif
                        @if($comic->description)<div><span class="font-semibold text-black/70">description:</span> {{ $comic->description }}</div>@endif
                    </dl>
                    <div class="mt-2 flex gap-4 text-sm font-semibold">
                        <a href="{{ $comic->url }}?preview={{ urlencode($token) }}"
                           class="underline hover:text-[var(--color-katt-accent)]">Open in reader →</a>
                        <a href="{{ route('admin.comics.edit', $comic) }}"
                           class="underline hover:text-[var(--color-katt-accent)]">Edit metadata</a>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</section>

{{-- Live archive --}}
<section>
    <h2 class="text-xl font-bold mb-4">Live</h2>
    @if($published->isEmpty())
        <p class="text-black/60">No comics are live yet.</p>
    @else
        <ul class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            @foreach($published as $comic)
                <li>
                    <a href="{{ $comic->url }}" class="block group">
                        <div class="aspect-square overflow-hidden rounded-lg border border-black/10 bg-white">
                            <img src="{{ $comic->image_url }}" alt="{{ $comic->alt_text }}" loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition">
                        </div>
                        <p class="mt-1 text-sm font-semibold truncate">#{{ $comic->number }} {{ $comic->title }}</p>
                        <p class="text-xs text-black/50">{{ $comic->published_at->format('M j, Y') }}</p>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</section>
@endsection
