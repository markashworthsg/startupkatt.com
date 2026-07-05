@php
    $site = config('comics.site');
    $creator = $site['creator'];
    $linkedin = $site['creator_linkedin'];
    $title = 'About Startup Katt: the daily webcomic about a cat founder';
    $description = 'Startup Katt is a daily webcomic about a cat trying to build a startup. What it is, who makes it, how often it updates, and how to read it from the beginning.';
    $canonical = route('about');

    // Visible "last updated" line. Re-stamp this when you meaningfully edit the page.
    $lastUpdated = '2026-06-22';

    // Single source of truth for the FAQ: rendered both as a visible section and
    // as FAQPage JSON-LD below, so the two can never drift apart.
    $faqs = [
        [
            'q' => 'What is Startup Katt?',
            'a' => 'Startup Katt is a daily webcomic about a cat trying to build a startup. Each strip is a single, self-contained gag about startup life (fundraising, pivots, demo days, burnout, and the gap between the pitch and the reality), told through one stubbornly optimistic cat founder. A new strip goes live every day, and the full run is free to read in the archive.',
        ],
        [
            'q' => 'Who is the cat, and why do people call him Startup Cat?',
            'a' => 'The cat is named Startup Katt (with two t\'s), but almost everyone calls him "Startup Cat" anyway. He has decided to let it slide. He\'s ambitious, perpetually over-caffeinated, and completely convinced his next idea is the one that finally works. The running joke is that he keeps building things nobody asked for with the unshakeable confidence of someone who has never once read a churn report.',
        ],
        [
            'q' => 'How often is a new comic published?',
            'a' => 'Every day. One new strip is published per calendar day, and each one gets its own permanent URL so it\'s easy to share or link to a specific gag.',
        ],
        [
            'q' => 'Where do I start reading Startup Katt?',
            'a' => 'You can jump in anywhere. Every strip stands on its own. If you\'d rather read in order, open the archive and start from the earliest comic, or use the first / previous / next / latest navigation on any strip to move through the run one day at a time.',
        ],
        [
            'q' => 'Is Startup Katt AI-generated?',
            'a' => 'The art is AI-generated; the jokes, characters, and editorial voice are written and curated by a human. Think of it as a human-run comic strip where the drawing tool happens to be a model instead of a pen.',
        ],
        [
            'q' => 'Who makes Startup Katt?',
            'a' => 'Startup Katt is made by one human, who spends his days around startups, product, and the strange comedy of trying to make something out of nothing. The comic is where the funnier, more honest version of that lands.',
        ],
        [
            'q' => 'How can I follow Startup Katt or get new strips by email?',
            'a' => 'Subscribe to the RSS feed to get every new strip in your reader, follow Startup Katt on Instagram so the daily comic shows up in your feed, or join the Telegram channel. A new strip lands every single day.',
        ],
    ];

    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'AboutPage',
                'url' => $canonical,
                'name' => $title,
                'description' => $description,
                'dateModified' => $lastUpdated,
                'mainEntity' => [
                    '@type' => 'ComicSeries',
                    'name' => $site['name'],
                    'url' => route('home'),
                    'description' => $site['description'],
                    'genre' => ['Webcomic', 'Comedy', 'Startups'],
                    'sameAs' => array_values(array_filter([$site['instagram'] ?? null, $site['telegram'] ?? null])),
                    'author' => [
                        '@type' => 'Person',
                        'url' => $linkedin,
                        'sameAs' => [$linkedin],
                    ],
                ],
            ],
            [
                '@type' => 'FAQPage',
                'mainEntity' => array_map(fn ($faq) => [
                    '@type' => 'Question',
                    'name' => $faq['q'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $faq['a'],
                    ],
                ], $faqs),
            ],
        ],
    ];
@endphp

@extends('layouts.app')

@push('schema')
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<article class="prose-katt max-w-none">
    <h1 class="text-3xl font-extrabold mb-2" style="font-family: var(--font-display)">
        About Startup Katt
    </h1>
    <p class="text-sm text-black/50 mb-6">
        Last updated:
        <time datetime="{{ $lastUpdated }}">{{ \Illuminate\Support\Carbon::parse($lastUpdated)->format('F j, Y') }}</time>
    </p>

    <p class="text-lg leading-relaxed">
        <strong>Startup Katt is a daily webcomic about a cat trying to build a startup.</strong>
        New strip every day, free to read, one stubbornly optimistic cat founder at a time.
    </p>

    <h2 class="text-2xl font-bold mt-10 mb-3" style="font-family: var(--font-display)">
        What is Startup Katt about?
    </h2>
    <p class="leading-relaxed text-black/80">
        Every strip is a single, self-contained gag about startup life: the fundraising,
        the pivots, the demo days, the burnout, and the enormous gap between the pitch and
        the reality. I draw it because every founder I've ever met (myself included) has
        been that cat at 2&nbsp;a.m., pitch deck open, litter box of self-doubt nearby. It's
        the funnier, more honest version of building something out of nothing.
    </p>

    <h2 class="text-2xl font-bold mt-10 mb-3" style="font-family: var(--font-display)">
        Who is Startup Katt (and why "Startup Cat")?
    </h2>
    <p class="leading-relaxed text-black/80">
        The cat is <strong>Startup Katt</strong> (with two t's), but everyone calls him
        "Startup Cat" anyway, and he's letting it slide. He's ambitious, a little
        over-caffeinated, and absolutely convinced his next idea is the one. He keeps
        shipping things nobody asked for with the confidence of someone who has never once
        read a churn report. You'll recognise him. You might be him.
    </p>

    <h2 class="text-2xl font-bold mt-10 mb-3" style="font-family: var(--font-display)">
        Who makes it?
    </h2>
    <p class="leading-relaxed text-black/80">
        Hi, I'm the human behind Startup Katt. I spend my
        days around startups, product, and the strange comedy of trying to make something
        out of nothing. The art is AI-generated; the jokes, the characters, and the voice
        are mine. New strip every day. Pull up a chair.
    </p>

    <h2 class="text-2xl font-bold mt-10 mb-4" style="font-family: var(--font-display)">
        Frequently asked questions
    </h2>
    <div class="divide-y divide-black/10 border-t border-black/10">
        @foreach ($faqs as $faq)
            <div class="py-4">
                <h3 class="font-semibold text-lg">{{ $faq['q'] }}</h3>
                <p class="mt-2 leading-relaxed text-black/80">{{ $faq['a'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-10 flex flex-wrap items-center gap-4">
        <a href="{{ route('comics.archive') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-[var(--color-katt-accent)] px-4 py-2 font-semibold text-white hover:opacity-90 transition">
            Read the archive →
        </a>
        <a href="{{ route('feed') }}"
           class="inline-flex items-center gap-2 font-semibold hover:text-[var(--color-katt-accent)]">
            Subscribe via RSS
        </a>
        @if (!empty($site['instagram']))
            <a href="{{ $site['instagram'] }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 font-semibold hover:text-[var(--color-katt-accent)]">
                Follow on Instagram
            </a>
        @endif
        @if (!empty($site['telegram']))
            <a href="{{ $site['telegram'] }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 font-semibold hover:text-[var(--color-katt-accent)]">
                Join on Telegram
            </a>
        @endif
        <a href="{{ $linkedin }}" target="_blank" rel="noopener"
           class="inline-flex items-center gap-2 font-semibold hover:text-[var(--color-katt-accent)]">
            Connect on LinkedIn
        </a>
    </div>
</article>
@endsection
