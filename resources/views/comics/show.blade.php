@php
    $site = config('comics.site');
    $title = $comic->title.' — '.$site['name'].' #'.$comic->number;
    $description = $comic->meta_description;
    $canonical = $comic->url;
    $ogImage = $comic->og_image_url;
    $ogType = 'article';
@endphp

@extends('layouts.app')

@push('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ComicStory',
    'name' => $comic->title,
    'position' => $comic->number,
    'url' => $comic->url,
    'datePublished' => $comic->published_at->toDateString(),
    'image' => $comic->image_url,
    'description' => $comic->meta_description,
    'author' => ['@type' => 'Person', 'name' => $site['author']],
    'publisher' => ['@type' => 'Organization', 'name' => $site['name']],
    'isPartOf' => [
        '@type' => 'ComicSeries',
        'name' => $site['name'],
        'url' => route('home'),
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<article class="flex flex-col items-center">
    <header class="w-full text-center mb-4">
        <h1 class="text-2xl sm:text-3xl font-extrabold" style="font-family: var(--font-display)">
            #{{ $comic->number }} — {{ $comic->title }}
        </h1>
        <p class="text-sm text-black/50 mt-1">
            <time datetime="{{ $comic->published_at->toDateString() }}">
                {{ $comic->published_at->format('F j, Y') }}
            </time>
        </p>
    </header>

    {{-- Top nav --}}
    <x-comic-nav :previous="$previous" :next="$next" class="mb-4" />

    <figure class="w-full">
        <a href="{{ $next?->url ?? route('home') }}" title="Next comic">
            <img
                src="{{ $comic->image_url }}"
                alt="{{ $comic->alt_text }}"
                @if($comic->width) width="{{ $comic->width }}" @endif
                @if($comic->height) height="{{ $comic->height }}" @endif
                class="w-full h-auto rounded-lg shadow-md bg-white"
                fetchpriority="high"
            >
        </a>
        @if($comic->caption)
            <figcaption class="mt-3 text-center text-black/70 leading-relaxed">
                {{ $comic->caption }}
            </figcaption>
        @endif
    </figure>

    {{-- Share --}}
    <x-comic-share :comic="$comic" class="mt-6" />

    {{-- Bottom nav --}}
    <x-comic-nav :previous="$previous" :next="$next" class="mt-6" />

    <x-newsletter-signup class="mt-10" />
</article>
@endsection
