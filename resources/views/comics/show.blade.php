@php
    $site = config('comics.site');
    $preview = $preview ?? false;
    $previewToken = $previewToken ?? null;
    $title = $comic->title.': '.$site['name'].' #'.$comic->number;
    $description = $comic->meta_description;
    $canonical = $comic->url;
    $ogImage = $comic->og_image_url;
    $ogType = 'article';
    // Never let an unpublished sneak-peek into search.
    $noindex = $preview;
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
    @if($preview)
        <div class="w-full mb-4 rounded-md border border-[var(--color-katt-accent)] bg-[var(--color-katt-accent)]/10 px-4 py-2 text-sm text-center font-semibold">
            👁 Preview: scheduled for {{ $comic->published_at->format('F j, Y') }}, not yet public.
            <a href="{{ route('preview', $previewToken) }}" class="underline">Back to all upcoming</a>
        </div>
    @endif

    <header class="w-full text-center mb-4">
        <h1 class="text-2xl sm:text-3xl font-extrabold" style="font-family: var(--font-display)">
            #{{ $comic->number }}: {{ $comic->title }}
        </h1>
        <p class="text-sm text-black/50 mt-1">
            <time datetime="{{ $comic->published_at->toDateString() }}">
                {{ $comic->published_at->format('F j, Y') }}
            </time>
        </p>
    </header>

    <figure class="w-full" data-swipe>
        <img
            src="{{ $comic->image_url }}"
            alt="{{ $comic->alt_text }}"
            @if($comic->width) width="{{ $comic->width }}" @endif
            @if($comic->height) height="{{ $comic->height }}" @endif
            class="w-full h-auto rounded-lg shadow-md bg-white"
            fetchpriority="high"
        >
        @if($comic->caption)
            {{-- Transcript kept for screen readers + crawlers (SEO/AEO), but hidden
                 visually so the plain-text restatement doesn't spoil the joke. --}}
            <figcaption class="sr-only">
                {{ $comic->caption }}
            </figcaption>
        @endif
    </figure>

    {{-- Primary navigation, right under the punchline so the next strip is in
         reach (swipe on mobile, ← → on desktop, or these buttons). --}}
    <x-comic-nav :previous="$previous" :next="$next"
        :current="$comic"
        :preview-token="$previewToken"
        :first="$preview ? \App\Models\Comic::firstOverall() : null"
        :latest="$preview ? \App\Models\Comic::latestOverall() : null"
        class="mt-5" />

    {{-- Reactions (login-free voting): only on live strips, never previews --}}
    @unless($preview)
        <x-comic-reactions :comic="$comic" class="mt-8" />
    @endunless

    {{-- Newsletter CTA: below reactions, so we ask after a reader has actually
         enjoyed a strip or two (a higher-intent moment than strip #1). --}}
    @unless($preview)
        <x-newsletter-signup class="mt-8" />
    @endunless

    {{-- Share --}}
    <x-comic-share :comic="$comic" class="mt-8" />

    {{-- Bottom nav (after the reader has scrolled the whole strip) --}}
    <x-comic-nav :previous="$previous" :next="$next"
        :current="$comic"
        :preview-token="$previewToken"
        :first="$preview ? \App\Models\Comic::firstOverall() : null"
        :latest="$preview ? \App\Models\Comic::latestOverall() : null"
        class="mt-10" />
</article>

{{-- Engagement nudge: teases the most-reacted strip, links to /top. Hidden in
     preview, and never tease the strip you're currently reading. --}}
@unless($preview)
    <x-top-strip-toast :current="$comic" />
@endunless
@endsection
