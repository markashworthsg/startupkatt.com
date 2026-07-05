@props([
    'comic',
])

@php
    $reactions = config('comics.reactions');
    $counts = $comic->reactionCounts();
    $total = array_sum($counts);

    // The reader's current pick comes from the same encrypted cookie the
    // ReactionController writes ({comicId: reaction}). Counting is governed
    // server-side by IP; this is just the instant UI highlight.
    $votes = json_decode((string) request()->cookie('sk_votes'), true);
    $userReaction = is_array($votes) ? ($votes[$comic->id] ?? null) : null;

    // Compact tally (1234 -> 1.2k), mirrors the JS so counts don't reformat
    // the instant someone votes.
    $fmt = function (int $n): string {
        if ($n >= 1000000) return rtrim(rtrim(number_format($n / 1000000, 1), '0'), '.').'M';
        if ($n >= 1000) return rtrim(rtrim(number_format($n / 1000, 1), '0'), '.').'k';
        return (string) $n;
    };
@endphp

<section
    {{ $attributes->merge(['class' => 'w-full']) }}
    data-reactions
    data-react-url="{{ route('comics.react', $comic) }}"
    aria-label="React to this strip"
>
    <p class="text-xs uppercase tracking-wide text-black/40 mb-3 text-center" data-react-prompt
       data-default="How'd this one land?">
        How'd this one land?
    </p>

    <div class="flex flex-wrap items-center justify-center gap-2.5">
        @foreach($reactions as $key => $meta)
            @php $isActive = $userReaction === $key; @endphp
            <button
                type="button"
                class="reaction{{ $isActive ? ' is-active' : '' }}"
                data-reaction="{{ $key }}"
                aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                title="{{ $meta['label'] }}"
            >
                <span class="reaction__emoji" aria-hidden="true">{{ $meta['emoji'] }}</span>
                <span class="reaction__label hidden sm:inline">{{ $meta['label'] }}</span>
                <span class="reaction__count" data-count>{{ $fmt($counts[$key]) }}</span>
            </button>
        @endforeach
    </div>

    <p class="mt-3 text-center text-xs text-black/40">
        <span data-react-total>{{ $fmt($total) }}</span> reaction{{ $total === 1 ? '' : 's' }} so far
    </p>
</section>

@once
@push('styles')
<style>
    .reaction {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        /* 44px min tap target for thumbs (Apple HIG / WCAG 2.5.5). On mobile the
           label is hidden, so this keeps the emoji+count comfortably tappable. */
        min-height: 44px;
        border-radius: 9999px;
        border: 1px solid rgba(0, 0, 0, 0.1);
        background: #fff;
        padding: 0.5rem 1.05rem;
        font-size: 0.9375rem;
        font-weight: 600;
        line-height: 1;
        color: inherit;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        cursor: pointer;
        -webkit-tap-highlight-color: transparent;
        transition: transform 0.18s cubic-bezier(0.34, 1.56, 0.64, 1),
            background-color 0.18s ease, color 0.18s ease,
            border-color 0.18s ease, box-shadow 0.18s ease;
    }
    .reaction:hover {
        transform: translateY(-2px);
        border-color: var(--color-katt-accent);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .reaction:hover .reaction__emoji { transform: scale(1.18) rotate(-6deg); }
    .reaction:active { transform: translateY(0) scale(0.95); }
    .reaction:focus-visible {
        outline: 2px solid var(--color-katt-accent);
        outline-offset: 2px;
    }

    .reaction__emoji {
        font-size: 1.2rem;
        line-height: 1;
        display: inline-block;
        transition: transform 0.18s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .reaction__count {
        font-variant-numeric: tabular-nums;
        font-size: 0.75rem;
        border-radius: 9999px;
        padding: 0.125rem 0.375rem;
        background: rgba(0, 0, 0, 0.05);
        color: rgba(0, 0, 0, 0.5);
        transition: background-color 0.18s ease, color 0.18s ease;
    }

    /* Active state: single class, no utility conflicts */
    .reaction.is-active {
        background: var(--color-katt-accent);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 4px 14px color-mix(in oklab, var(--color-katt-accent) 45%, transparent);
    }
    .reaction.is-active .reaction__count {
        background: rgba(255, 255, 255, 0.25);
        color: #fff;
    }

    /* Vote-registered burst */
    @keyframes reaction-pop {
        0% { transform: scale(1); }
        35% { transform: scale(1.35) rotate(8deg); }
        70% { transform: scale(0.92) rotate(-4deg); }
        100% { transform: scale(1) rotate(0); }
    }
    @keyframes reaction-count-bump {
        0% { transform: translateY(0); }
        50% { transform: translateY(-4px) scale(1.12); }
        100% { transform: translateY(0) scale(1); }
    }
    .reaction.just-voted .reaction__emoji { animation: reaction-pop 0.4s ease; }
    .reaction.just-voted .reaction__count { animation: reaction-count-bump 0.3s ease; }

    @media (prefers-reduced-motion: reduce) {
        .reaction,
        .reaction__emoji,
        .reaction__count { transition: none; }
        .reaction:hover { transform: none; }
        .reaction:hover .reaction__emoji { transform: none; }
        .reaction.just-voted .reaction__emoji,
        .reaction.just-voted .reaction__count { animation: none; }
    }
</style>
@endpush
@endonce

@push('scripts')
<script>
(function () {
    var token = document.querySelector('meta[name="csrf-token"]');
    token = token ? token.getAttribute('content') : '';

    function fmt(n) {
        if (n >= 1000000) return (n / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
        if (n >= 1000) return (n / 1000).toFixed(1).replace(/\.0$/, '') + 'k';
        return String(n);
    }

    document.querySelectorAll('[data-reactions]').forEach(function (root) {
        var url = root.getAttribute('data-react-url');
        var buttons = Array.prototype.slice.call(root.querySelectorAll('[data-reaction]'));
        var totalEl = root.querySelector('[data-react-total]');
        var prompt = root.querySelector('[data-react-prompt]');
        var busy = false;
        var thanksTimer;

        function paint(data) {
            var total = 0;
            buttons.forEach(function (btn) {
                var key = btn.getAttribute('data-reaction');
                var count = (data.counts && key in data.counts) ? data.counts[key] : 0;
                total += count;
                var countEl = btn.querySelector('[data-count]');
                if (countEl) countEl.textContent = fmt(count);

                var active = data.userReaction === key;
                btn.classList.toggle('is-active', active);
                btn.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
            if (totalEl) totalEl.textContent = fmt(data.total != null ? data.total : total);
        }

        function burst(btn) {
            btn.classList.remove('just-voted');
            void btn.offsetWidth; // restart the animation
            btn.classList.add('just-voted');
        }

        function sayThanks(voted) {
            if (!prompt) return;
            clearTimeout(thanksTimer);
            if (voted) {
                prompt.textContent = 'Thanks! 🐈';
                thanksTimer = setTimeout(function () {
                    prompt.textContent = prompt.getAttribute('data-default');
                }, 1800);
            } else {
                prompt.textContent = prompt.getAttribute('data-default');
            }
        }

        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (busy) return;
                busy = true;

                var key = btn.getAttribute('data-reaction');
                var wasActive = btn.classList.contains('is-active');

                // Optimistic: reflect the choice instantly, then reconcile.
                buttons.forEach(function (b) { b.classList.remove('is-active'); b.setAttribute('aria-pressed', 'false'); });
                if (!wasActive) { btn.classList.add('is-active'); btn.setAttribute('aria-pressed', 'true'); burst(btn); }

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ reaction: key }),
                })
                    .then(function (r) { return r.ok ? r.json() : Promise.reject(r); })
                    .then(function (data) {
                        paint(data);
                        sayThanks(data.userReaction !== null);
                    })
                    .catch(function () { /* leave optimistic state; next load reconciles */ })
                    .finally(function () { busy = false; });
            });
        });
    });
})();
</script>
@endpush
