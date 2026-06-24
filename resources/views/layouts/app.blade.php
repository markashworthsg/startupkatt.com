<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Favicons / app icons --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#ff6b35">

    @php
        $site = config('comics.site');
        $metaTitle = trim($title ?? $site['name']);
        $metaDescription = $description ?? $site['description'];
        $canonical = $canonical ?? url()->current();
        $ogImage = $ogImage ?? asset('og-default.png');
        $ogType = $ogType ?? 'website';
    @endphp

    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    @if($noindex ?? false)
        <meta name="robots" content="noindex, nofollow">
    @endif
    <link rel="canonical" href="{{ $canonical }}">

    {{-- Open Graph --}}
    <meta property="og:site_name" content="{{ $site['name'] }}">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $ogImage }}">

    {{-- Twitter / X --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="{{ $site['twitter'] }}">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    {{-- Feeds --}}
    <link rel="alternate" type="application/rss+xml" title="{{ $site['name'] }} RSS" href="{{ route('feed') }}">

    @stack('schema')
    @stack('styles')

    {{-- beehiiv attribution tracking. Records where subscribers came from.
         Loads only when a beehiiv form is configured. --}}
    @if(config('comics.beehiiv.form_id'))
        <script type="text/javascript" async src="https://subscribe-forms.beehiiv.com/attribution.js"></script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col font-sans antialiased">
    <header class="border-b border-black/10">
        <div class="max-w-3xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-2xl font-extrabold tracking-tight"
               style="font-family: var(--font-display)">
                Startup Katt
            </a>
            <nav class="flex items-center gap-4 text-sm font-medium">
                <a href="{{ route('home') }}" class="hover:text-[var(--color-katt-accent)]">Latest</a>
                <a href="{{ route('top') }}" class="hover:text-[var(--color-katt-accent)]">Top</a>
                <a href="{{ route('comics.archive') }}" class="hover:text-[var(--color-katt-accent)]">Archive</a>
                <a href="{{ route('about') }}" class="hover:text-[var(--color-katt-accent)]">About</a>
                <a href="{{ route('feed') }}" class="hover:text-[var(--color-katt-accent)]">RSS</a>
            </nav>
        </div>
    </header>

    <main class="flex-1 w-full max-w-3xl mx-auto px-4 py-8">
        @yield('content')
    </main>

    <footer class="border-t border-black/10 mt-12">
        <div class="max-w-3xl mx-auto px-4 py-6 text-sm text-black/60 flex flex-col sm:flex-row gap-2 sm:justify-between">
            <p>&copy; {{ date('Y') }} {{ $site['name'] }}. His friends call him Startup Cat.</p>
            <p class="flex items-center gap-3">
                @if(!empty($site['instagram']))
                    <a href="{{ $site['instagram'] }}" target="_blank" rel="noopener"
                       class="font-medium underline decoration-black/20 hover:text-[var(--color-katt-accent)]">Instagram</a>
                    <span aria-hidden="true">·</span>
                @endif
                <span>Made by
                    <a href="{{ $site['creator_linkedin'] }}" target="_blank" rel="noopener"
                       class="font-medium underline decoration-black/20 hover:text-[var(--color-katt-accent)]">{{ $site['creator'] }}</a>
                </span>
            </p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
