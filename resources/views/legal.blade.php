@php
    $site = config('comics.site');
    $creator = $site['creator'];
    $linkedin = $site['creator_linkedin'];
    $title = 'Legal & Privacy: a word from our lawyers (ChatGPT)';
    $description = 'The fiction disclaimer, copyright, and privacy policy for Startup Katt. Any resemblance to real founders is, whilst unfortunate, purely coincidental.';
    $canonical = route('legal');

    // Visible "last updated" line. Re-stamp this when you meaningfully edit the page.
    $lastUpdated = '2026-06-26';

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'url' => $canonical,
        'name' => $title,
        'description' => $description,
        'dateModified' => $lastUpdated,
        'isPartOf' => [
            '@type' => 'WebSite',
            'name' => $site['name'],
            'url' => route('home'),
        ],
        'publisher' => [
            '@type' => 'Person',
            'url' => $linkedin,
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
        Legal, privacy, and other things our lawyers made us say
    </h1>
    <p class="text-sm text-black/50 mb-6">
        Last updated:
        <time datetime="{{ $lastUpdated }}">{{ \Illuminate\Support\Carbon::parse($lastUpdated)->format('F j, Y') }}</time>
    </p>

    <p class="text-lg leading-relaxed">
        And now, a word from our lawyers. Our lawyer is ChatGPT. We typed
        "write me a legal page" and pasted whatever it gave back. If any of the
        below is wrong, that is between you and a model with no bar membership,
        no malpractice insurance, and a worrying confidence in its own advice.
    </p>

    <figure class="my-8">
        <img
            src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url('comics/2026/06/a-word-from-our-lawyers.png') }}"
            alt="Startup Cat, dressed as a lawyer for the ChatGPT Legal Department, reads a disclaimer that all characters and events are fictional and any resemblance to real people or startups is purely coincidental. If you think the comic is about you it probably isn't, but if you genuinely resemble Startup Cat, take the afternoon off."
            width="1536" height="1024"
            class="w-full rounded-xl border border-black/10 shadow-sm"
            loading="lazy"
        >
        <figcaption class="mt-2 text-sm text-black/50">
            Our legal team, hard at work.
        </figcaption>
    </figure>

    <h2 class="text-2xl font-bold mt-10 mb-3" style="font-family: var(--font-display)">
        The fiction bit (the important one)
    </h2>
    <p class="leading-relaxed text-black/80">
        Startup Katt is a work of fiction. The characters, companies, pitch
        decks, valuations, and bad decisions are invented for comic effect.
        <strong>Any resemblance to real persons, living or fundraising, is,
        whilst unfortunate, purely coincidental.</strong>
    </p>
    <p class="leading-relaxed text-black/80">
        If you read a strip and thought "that is weirdly specific, that is
        basically me", we want to be clear: it is not about you. It was never
        about you. And honestly, if you genuinely recognise yourself in Startup
        Cat, the cat who keeps shipping things nobody asked for, we are not
        angry. We feel sorry for you. Take the afternoon off.
    </p>

    <h2 class="text-2xl font-bold mt-10 mb-3" style="font-family: var(--font-display)">
        Copyright
    </h2>
    <p class="leading-relaxed text-black/80">
        The comics, characters, and writing are &copy; {{ date('Y') }}
        Startup Katt. You are very welcome to share a strip, link to it, or
        send it to the one person on your team who needs to see it. Please keep
        the watermark and link back here. Do not sell our cat, slap him on
        merchandise, or feed the strips into a model to spin up a competing cat.
        He is one of a kind, and his lawyer is already typing.
    </p>

    <h2 class="text-2xl font-bold mt-10 mb-3" style="font-family: var(--font-display)">
        About the art
    </h2>
    <p class="leading-relaxed text-black/80">
        The art is AI-generated. The jokes, the characters, and the editorial
        voice are written and curated by a human. We mention
        this here because we mention it everywhere, not because a model made us.
    </p>

    <h2 class="text-2xl font-bold mt-10 mb-3" style="font-family: var(--font-display)">
        Privacy (this part is real)
    </h2>
    <p class="leading-relaxed text-black/80">
        Comedy aside, here is exactly what this site does with your data, in
        plain English. There is not much of it.
    </p>
    <ul class="mt-4 space-y-3 list-disc pl-5 text-black/80">
        <li>
            <strong>Reactions.</strong> When you tap a reaction on a strip, we
            store a one-way hash of your IP address so the same device or
            network can only vote once per comic. We cannot turn that hash back
            into your IP, and it is never linked to your name or email. That is
            the entire anti-spam system.
        </li>
        <li>
            <strong>A cookie that lights up buttons.</strong> We set one cookie,
            <code>sk_votes</code>, that remembers which reactions you tapped so
            the buttons highlight when you come back. It lives in your browser,
            it is not used for tracking or advertising, and clearing it just
            un-highlights some buttons. Nothing breaks.
        </li>
        <li>
            <strong>The newsletter.</strong> If you subscribe, your email is
            sent to beehiiv, who run the newsletter and store the list. Their
            privacy policy covers what happens to it there. You can unsubscribe
            from any email, any time, and we forget you cheerfully.
        </li>
        <li>
            <strong>Privacy-friendly analytics.</strong> We use
            <a href="https://plausible.io/data-policy" target="_blank" rel="noopener"
               class="font-medium underline decoration-black/20 hover:text-[var(--color-katt-accent)]">Plausible</a>
            to count visits and see which strips land. It sets
            <strong>no cookies</strong>, collects no personal data, and cannot
            follow you to other websites. We see aggregate numbers like page
            views and where readers came from, never individual people. That is
            why there is no annoying cookie banner on this site.
        </li>
        <li>
            <strong>Boring server logs.</strong> Like every website on Earth,
            the web server keeps standard request logs (IP, time, page) for
            security and debugging. Nothing exciting, nothing sold.
        </li>
    </ul>
    <p class="leading-relaxed text-black/80 mt-4">
        What we do <strong>not</strong> do: no ad networks, no advertising
        pixels, no cross-site tracking, no cookies that follow you around, no
        selling your data to anyone for any reason. We make a cat comic. We are
        not in the surveillance business.
    </p>

    <h2 class="text-2xl font-bold mt-10 mb-3" style="font-family: var(--font-display)">
        Changes and contact
    </h2>
    <p class="leading-relaxed text-black/80">
        If we change any of this, the "last updated" date at the top changes
        with it. If something here worries you, or you want your email removed,
        the fastest way to reach a human is
        <a href="{{ $linkedin }}" target="_blank" rel="noopener"
           class="font-medium underline decoration-black/20 hover:text-[var(--color-katt-accent)]">{{ $creator }} on LinkedIn</a>.
        A real person reads it. The lawyer (ChatGPT) does not.
    </p>

    <div class="mt-10 flex flex-wrap items-center gap-4">
        <a href="{{ route('home') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-[var(--color-katt-accent)] px-4 py-2 font-semibold text-white hover:opacity-90 transition">
            Back to the comic →
        </a>
        <a href="{{ route('about') }}"
           class="inline-flex items-center gap-2 font-semibold hover:text-[var(--color-katt-accent)]">
            About Startup Katt
        </a>
    </div>
</article>
@endsection
