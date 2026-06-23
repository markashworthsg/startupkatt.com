@props([
    'comic',
])

@php
    $reactions = config('comics.reactions');
    $counts = $comic->reactionCounts();
    $total = array_sum($counts);

    // The reader's current pick comes from the same encrypted cookie the
    // ReactionController writes ({comicId: reaction}).
    $votes = json_decode((string) request()->cookie('sk_votes'), true);
    $userReaction = is_array($votes) ? ($votes[$comic->id] ?? null) : null;

    // Compact tally (1234 -> 1.2k) — mirrors the JS so counts don't reformat
    // the instant someone votes.
    $fmt = function (int $n): string {
        if ($n >= 1000000) return rtrim(rtrim(number_format($n / 1000000, 1), '0'), '.').'M';
        if ($n >= 1000) return rtrim(rtrim(number_format($n / 1000, 1), '0'), '.').'k';
        return (string) $n;
    };

    $base = 'reaction-btn group inline-flex items-center gap-1.5 rounded-full border border-black/10 bg-white px-3 py-1.5 text-sm font-semibold shadow-sm transition hover:-translate-y-0.5 hover:shadow-md hover:border-[var(--color-katt-accent)] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-katt-accent)]';
    // Classes toggled (here and in JS) to mark the active vote.
    $activeClasses = 'bg-[var(--color-katt-accent)] text-white border-transparent';
@endphp

<section
    {{ $attributes->merge(['class' => 'w-full']) }}
    data-reactions
    data-react-url="{{ route('comics.react', $comic) }}"
    aria-label="React to this strip"
>
    <p class="text-xs uppercase tracking-wide text-black/40 mb-3 text-center">
        How'd this one land?
    </p>

    <div class="flex flex-wrap items-center justify-center gap-2">
        @foreach($reactions as $key => $meta)
            @php $isActive = $userReaction === $key; @endphp
            <button
                type="button"
                data-reaction="{{ $key }}"
                aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                title="{{ $meta['label'] }}"
                class="{{ $base }} {{ $isActive ? $activeClasses : '' }}"
            >
                <span aria-hidden="true" class="text-base leading-none">{{ $meta['emoji'] }}</span>
                <span class="hidden sm:inline">{{ $meta['label'] }}</span>
                <span
                    data-count
                    class="tabular-nums text-xs rounded-full px-1.5 py-0.5 {{ $isActive ? 'bg-white/25 text-white' : 'bg-black/5 text-black/50' }}"
                >{{ $fmt($counts[$key]) }}</span>
            </button>
        @endforeach
    </div>

    <p class="mt-3 text-center text-xs text-black/40">
        <span data-react-total>{{ $fmt($total) }}</span> reaction{{ $total === 1 ? '' : 's' }} so far
    </p>
</section>

@push('scripts')
<script>
(function () {
    var token = document.querySelector('meta[name="csrf-token"]');
    token = token ? token.getAttribute('content') : '';
    var ACTIVE = {!! json_encode(explode(' ', $activeClasses)) !!};

    function fmt(n) {
        if (n >= 1000000) return (n / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
        if (n >= 1000) return (n / 1000).toFixed(1).replace(/\.0$/, '') + 'k';
        return String(n);
    }

    document.querySelectorAll('[data-reactions]').forEach(function (root) {
        var url = root.getAttribute('data-react-url');
        var buttons = root.querySelectorAll('[data-reaction]');

        function paint(data) {
            var total = 0;
            buttons.forEach(function (btn) {
                var key = btn.getAttribute('data-reaction');
                var count = (data.counts && key in data.counts) ? data.counts[key] : 0;
                total += count;
                var countEl = btn.querySelector('[data-count]');
                if (countEl) countEl.textContent = fmt(count);

                var active = data.userReaction === key;
                btn.setAttribute('aria-pressed', active ? 'true' : 'false');
                ACTIVE.forEach(function (c) { if (c) btn.classList.toggle(c, active); });
                if (countEl) {
                    countEl.classList.toggle('bg-white/25', active);
                    countEl.classList.toggle('text-white', active);
                    countEl.classList.toggle('bg-black/5', !active);
                    countEl.classList.toggle('text-black/50', !active);
                }
            });
            var totalEl = root.querySelector('[data-react-total]');
            if (totalEl) totalEl.textContent = fmt(data.total != null ? data.total : total);
        }

        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                buttons.forEach(function (b) { b.disabled = true; });
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ reaction: btn.getAttribute('data-reaction') }),
                })
                    .then(function (r) { return r.ok ? r.json() : Promise.reject(r); })
                    .then(paint)
                    .catch(function () {})
                    .finally(function () { buttons.forEach(function (b) { b.disabled = false; }); });
            });
        });
    });
})();
</script>
@endpush
