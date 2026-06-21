<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Admin is private — never index it. --}}
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'Admin' }} — Startup Katt</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col font-sans antialiased">
    <header class="border-b border-black/10">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ route('admin.index') }}" class="text-xl font-extrabold tracking-tight">
                Startup Katt <span class="text-[var(--color-katt-accent)]">admin</span>
            </a>
            <nav class="flex items-center gap-4 text-sm font-medium">
                <a href="{{ route('home') }}" class="hover:text-[var(--color-katt-accent)]">View site</a>
            </nav>
        </div>
    </header>

    <main class="flex-1 w-full max-w-4xl mx-auto px-4 py-8">
        @if (session('status'))
            <div class="mb-6 rounded-md border border-[var(--color-katt-accent)]/40 bg-[var(--color-katt-accent)]/10 px-4 py-3 text-sm font-medium">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
