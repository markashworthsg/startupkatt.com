@php
    $site = config('comics.site');

    // Compact tally (1234 -> 1.2k), shared with the reaction widget's style.
    $fmt = function (int $n): string {
        if ($n >= 1000000) return rtrim(rtrim(number_format($n / 1000000, 1), '0'), '.').'M';
        if ($n >= 1000) return rtrim(rtrim(number_format($n / 1000, 1), '0'), '.').'k';
        return (string) $n;
    };

    $meta = $reaction ? $reactions[$reaction] : null;

    $heading = $reaction
        ? 'The '.$meta['superlative'].' Startup Katt strips'
        : 'The most-reacted Startup Katt strips';

    $title = $heading.': '.$site['name'];

    $description = $reaction
        ? 'The Startup Katt strips readers voted "'.$meta['label'].'" '.$meta['emoji'].' the most. Ranked by real reader reactions, updated as people vote.'
        : 'The Startup Katt strips readers react to most: funniest, most iconic, most painfully real, most unhinged. Ranked by real reader votes, updated continuously.';

    $canonical = route('top', $reaction);

    // Don't let a near-empty leaderboard into the index (no thin pages).
    $noindex = $comics->count() < 3;

    $updated = $comics->max('updated_at') ?? now();
@endphp

@extends('layouts.app')

@push('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => $heading,
    'description' => $description,
    'url' => $canonical,
    'numberOfItems' => $comics->count(),
    'itemListElement' => $comics->values()->map(fn ($c, $i) => [
        '@type' => 'ListItem',
        'position' => $i + 1,
        'url' => $c->url,
        'name' => $c->title,
        'image' => $c->image_url,
    ])->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        [
            '@type' => 'Question',
            'name' => 'How are the top Startup Katt strips ranked?',
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'By real reader reactions. Every strip has a one-tap, login-free vote (iconic, dead, galaxy brain, based, too real, or unhinged). This page ranks strips by total reactions; each tab ranks by a single reaction. Rankings update continuously as readers vote.'],
        ],
        [
            '@type' => 'Question',
            'name' => 'What does the "too real" reaction mean?',
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'It is the "this hit too close to home" vote for founder life: burnout, runway panic, another all-nighter. The most painfully real strips leaderboard ranks the strips that earned it the most. Its chaotic sibling, "unhinged," ranks the most deranged-startup-energy strips.'],
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<header class="mb-6">
    <h1 class="text-3xl font-extrabold" style="font-family: var(--font-display)">{{ $heading }}</h1>
    <p class="mt-2 text-black/60">
        Ranked by real reader votes. No editors, just what landed.
    </p>
    <p class="mt-1 text-xs text-black/40">
        Last updated <time datetime="{{ $updated->toDateString() }}">{{ $updated->format('F j, Y') }}</time>
    </p>
</header>

{{-- Reaction tabs --}}
<nav class="flex flex-wrap gap-2 mb-8" aria-label="Leaderboards">
    @php $tab = 'inline-flex items-center gap-1.5 min-h-[44px] rounded-full border px-4 py-2.5 text-sm font-semibold transition'; @endphp
    <a href="{{ route('top') }}"
       class="{{ $tab }} {{ $reaction === null ? 'bg-[var(--color-katt-accent)] text-white border-transparent' : 'border-black/10 bg-white hover:border-[var(--color-katt-accent)]' }}">
        🔥 Most reacted
    </a>
    @foreach($reactions as $key => $r)
        <a href="{{ route('top', $key) }}"
           class="{{ $tab }} {{ $reaction === $key ? 'bg-[var(--color-katt-accent)] text-white border-transparent' : 'border-black/10 bg-white hover:border-[var(--color-katt-accent)]' }}">
            <span aria-hidden="true">{{ $r['emoji'] }}</span> {{ $r['label'] }}
        </a>
    @endforeach
</nav>

@if($comics->isEmpty())
    <p class="text-black/60">
        No votes counted yet. Be the first to react.
        <a href="{{ route('home') }}" class="font-semibold underline decoration-black/20 hover:text-[var(--color-katt-accent)]">Read the latest strip →</a>
    </p>
@else
    <ol class="space-y-3">
        @foreach($comics as $i => $comic)
            @php
                $emoji = $reaction ? $meta['emoji'] : ($reactions[$comic->topReaction()]['emoji'] ?? '🔥');
            @endphp
            <li>
                <a href="{{ $comic->url }}" class="group flex items-center gap-4 rounded-lg border border-black/10 bg-white p-3 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md hover:border-[var(--color-katt-accent)]">
                    <span class="w-8 shrink-0 text-center text-xl font-extrabold tabular-nums text-black/30" style="font-family: var(--font-display)">{{ $i + 1 }}</span>
                    <div class="h-16 w-16 shrink-0 overflow-hidden rounded-md border border-black/10 bg-white">
                        <img src="{{ $comic->image_url }}" alt="{{ $comic->alt_text }}" loading="lazy"
                             class="h-full w-full object-cover group-hover:scale-105 transition">
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold truncate">#{{ $comic->number }} {{ $comic->title }}</p>
                        <p class="text-xs text-black/50">{{ $comic->published_at->format('M j, Y') }}</p>
                    </div>
                    <span class="shrink-0 inline-flex items-center gap-1.5 rounded-full bg-black/5 px-3 py-1.5 text-sm font-semibold tabular-nums">
                        <span aria-hidden="true">{{ $emoji }}</span> {{ $fmt((int) $comic->reactions_total) }}
                    </span>
                </a>
            </li>
        @endforeach
    </ol>
@endif

{{-- Visible FAQ (mirrors the FAQ schema above, an AEO signal) --}}
<section class="mt-12 border-t border-black/10 pt-8">
    <h2 class="text-xl font-extrabold mb-4" style="font-family: var(--font-display)">Questions</h2>
    <div class="space-y-5">
        <div>
            <h3 class="font-semibold">How are the top Startup Katt strips ranked?</h3>
            <p class="mt-1 text-black/70 leading-relaxed">By real reader reactions. Every strip has a one-tap, login-free vote. This page ranks strips by total reactions; each tab ranks by a single reaction. Rankings update continuously as readers vote.</p>
        </div>
        <div>
            <h3 class="font-semibold">What does the "too real" reaction mean?</h3>
            <p class="mt-1 text-black/70 leading-relaxed">It's the "this hit too close to home" vote for founder life: burnout, runway panic, another all-nighter. The <a href="{{ route('top', 'real') }}" class="font-semibold underline decoration-black/20 hover:text-[var(--color-katt-accent)]">most painfully real strips</a> leaderboard ranks the strips that earned it most. Its chaotic sibling, <a href="{{ route('top', 'unhinged') }}" class="font-semibold underline decoration-black/20 hover:text-[var(--color-katt-accent)]">unhinged</a>, ranks the most deranged-startup-energy strips.</p>
        </div>
    </div>

    <p class="mt-8 text-sm">
        <a href="{{ route('comics.archive') }}" class="font-semibold underline decoration-black/20 hover:text-[var(--color-katt-accent)]">Browse the full archive →</a>
    </p>
</section>
@endsection
