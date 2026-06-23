@props([
    'current' => null, // the strip being viewed, so we never tease the page you're on
])

@php
    $featured = \App\Models\Comic::topOverall(1)->first();

    // Nothing to show until a strip has reactions, and never tease the current page.
    $show = $featured && (! $current || $featured->id !== $current->id);

    if ($show) {
        $reactions = config('comics.reactions');
        $emoji = $reactions[$featured->topReaction()]['emoji'] ?? '🔥';
        $total = (int) $featured->reactions_total;
        $totalLabel = $total >= 1000 ? rtrim(rtrim(number_format($total / 1000, 1), '0'), '.').'k' : (string) $total;
    }
@endphp

@if($show)
<div
    data-top-toast
    class="fixed bottom-4 right-4 left-4 sm:left-auto sm:w-80 z-50 hidden"
    role="complementary"
    aria-label="Reader favourite strip"
>
    <div class="relative rounded-xl border border-black/10 bg-white shadow-lg">
        <button
            type="button"
            data-top-toast-dismiss
            class="absolute -top-2 -right-2 grid h-6 w-6 place-items-center rounded-full border border-black/10 bg-white text-black/50 shadow-sm transition hover:text-black hover:border-black/30"
            aria-label="Dismiss"
        >
            <svg viewBox="0 0 16 16" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                <path d="M3 3l10 10M13 3L3 13"/>
            </svg>
        </button>

        <a href="{{ route('top') }}" class="group flex items-center gap-3 p-3">
            <div class="h-14 w-14 shrink-0 overflow-hidden rounded-lg border border-black/10 bg-white">
                <img src="{{ $featured->image_url }}" alt="{{ $featured->alt_text }}" loading="lazy"
                     class="h-full w-full object-cover group-hover:scale-105 transition">
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[10px] uppercase tracking-wide text-[var(--color-katt-accent)] font-bold">Reader favourite</p>
                <p class="font-semibold truncate leading-tight">{{ $featured->title }}</p>
                <p class="text-xs text-black/50 mt-0.5">
                    <span aria-hidden="true">{{ $emoji }}</span> {{ $totalLabel }} reactions · See the top strips →
                </p>
            </div>
        </a>
    </div>
</div>

@once
@push('scripts')
<script>
(function () {
    var toast = document.querySelector('[data-top-toast]');
    if (!toast) return;

    var KEY = 'sk_top_toast_dismissed';
    try { if (sessionStorage.getItem(KEY)) return; } catch (e) {}

    function dismiss() {
        toast.classList.add('hidden');
        try { sessionStorage.setItem(KEY, '1'); } catch (e) {}
    }

    var btn = toast.querySelector('[data-top-toast-dismiss]');
    if (btn) btn.addEventListener('click', function (e) { e.preventDefault(); dismiss(); });

    // Slide in after the reader has settled on the page.
    setTimeout(function () { toast.classList.remove('hidden'); }, 8000);
})();
</script>
@endpush
@endonce
@endif
