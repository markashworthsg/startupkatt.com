@php
    $site = config('comics.site');
    $title = 'Archive — '.$site['name'];
    $description = 'Browse every Startup Katt comic. The complete archive of the daily webcomic about a cat trying to build a startup.';
    $canonical = route('comics.archive');
@endphp

@extends('layouts.app')

@section('content')
<h1 class="text-3xl font-extrabold mb-6" style="font-family: var(--font-display)">Archive</h1>

@if($comics->isEmpty())
    <p class="text-black/60">No comics published yet. Check back soon!</p>
@else
    <ul class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        @foreach($comics as $comic)
            <li>
                <a href="{{ $comic->url }}" class="block group">
                    <div class="aspect-square overflow-hidden rounded-lg border border-black/10 bg-white">
                        <img src="{{ $comic->image_url }}" alt="{{ $comic->alt_text }}"
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105 transition">
                    </div>
                    <p class="mt-1 text-sm font-semibold truncate">#{{ $comic->number }} {{ $comic->title }}</p>
                    <p class="text-xs text-black/50">{{ $comic->published_at->format('M j, Y') }}</p>
                </a>
            </li>
        @endforeach
    </ul>

    <div class="mt-8">
        {{ $comics->links() }}
    </div>
@endif
@endsection
