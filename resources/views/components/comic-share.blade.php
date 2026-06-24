@props([
    'comic',
])

@php
    $site = config('comics.site');
    $url = $comic->url;
    $text = $comic->title.': '.$site['name'];
    $enc = rawurlencode($url);
    $encText = rawurlencode($text);

    // Each entry: share URL + a brand-recognisable inline SVG path (16×16, currentColor).
    $links = [
        'X' => [
            'href' => 'https://twitter.com/intent/tweet?text='.$encText.'&url='.$enc,
            'icon' => '<path d="M14.234 1.5h2.69l-5.875 6.71L18 16.5h-5.41l-4.24-5.54-4.85 5.54H.81l6.28-7.18L0 1.5h5.546l3.832 5.065L14.234 1.5Zm-.944 13.39h1.49L5.18 3.03H3.58l9.71 11.86Z"/>',
        ],
        'LinkedIn' => [
            'href' => 'https://www.linkedin.com/sharing/share-offsite/?url='.$enc,
            'icon' => '<path d="M13.6 13.6h-2.37V9.9c0-.88-.02-2.02-1.23-2.02-1.23 0-1.42.96-1.42 1.96v3.76H6.21V6h2.28v1.04h.03c.32-.6 1.1-1.23 2.26-1.23 2.42 0 2.86 1.59 2.86 3.66v4.13ZM3.54 4.96a1.38 1.38 0 1 1 0-2.76 1.38 1.38 0 0 1 0 2.76ZM4.73 13.6H2.35V6h2.38v7.6ZM14.78 0H1.21C.54 0 0 .53 0 1.18v13.64C0 15.47.54 16 1.21 16h13.57c.67 0 1.22-.53 1.22-1.18V1.18C16 .53 15.45 0 14.78 0Z"/>',
        ],
        'Facebook' => [
            'href' => 'https://www.facebook.com/sharer/sharer.php?u='.$enc,
            'icon' => '<path d="M16 8a8 8 0 1 0-9.25 7.9v-5.59H4.72V8h2.03V6.24c0-2 1.2-3.11 3.02-3.11.87 0 1.79.16 1.79.16v1.97h-1.01c-.99 0-1.3.62-1.3 1.25V8h2.22l-.36 2.31H9.25v5.59A8 8 0 0 0 16 8Z"/>',
        ],
        'Reddit' => [
            'href' => 'https://www.reddit.com/submit?url='.$enc.'&title='.$encText,
            'icon' => '<path d="M16 8a1.5 1.5 0 0 0-2.54-1.08 7.4 7.4 0 0 0-3.95-1.25l.67-3.16 2.2.47a1.07 1.07 0 1 0 .12-.72l-2.46-.52a.36.36 0 0 0-.42.28l-.75 3.53a7.46 7.46 0 0 0-4.01 1.25 1.5 1.5 0 1 0-1.66 2.47 2.9 2.9 0 0 0-.04.48c0 2.45 2.85 4.43 6.37 4.43 3.52 0 6.37-1.98 6.37-4.43 0-.16-.01-.32-.04-.47A1.5 1.5 0 0 0 16 8ZM4.67 9.07a1.07 1.07 0 1 1 2.14 0 1.07 1.07 0 0 1-2.14 0Zm6.02 2.84c-.74.74-2.15.79-2.56.79-.41 0-1.83-.05-2.56-.79a.28.28 0 0 1 .39-.39c.47.46 1.46.63 2.17.63.71 0 1.71-.17 2.17-.63a.28.28 0 0 1 .39.39Zm-.13-1.76a1.07 1.07 0 1 1 0-2.14 1.07 1.07 0 0 1 0 2.14Z"/>',
        ],
        'WhatsApp' => [
            'href' => 'https://wa.me/?text='.rawurlencode($text.' '.$url),
            'icon' => '<path d="M13.6 2.33A7.86 7.86 0 0 0 8 0a7.94 7.94 0 0 0-6.9 11.9L0 16l4.2-1.1A7.93 7.93 0 0 0 8 15.86 7.94 7.94 0 0 0 13.6 2.33ZM8 14.5a6.6 6.6 0 0 1-3.36-.92l-.24-.14-2.49.65.66-2.43-.16-.25A6.59 6.59 0 1 1 8 14.5Zm3.62-4.93c-.2-.1-1.17-.58-1.35-.64-.18-.07-.31-.1-.44.1-.13.2-.5.64-.62.77-.11.13-.23.15-.43.05a5.4 5.4 0 0 1-1.59-.98 6 6 0 0 1-1.1-1.37c-.12-.2 0-.3.09-.4.09-.09.2-.23.3-.35.1-.12.13-.2.2-.34.06-.13.03-.25-.02-.35-.05-.1-.44-1.07-.6-1.46-.16-.38-.32-.33-.44-.34h-.38a.73.73 0 0 0-.53.25 2.23 2.23 0 0 0-.69 1.65c0 .98.71 1.92.81 2.05.1.13 1.4 2.14 3.4 3 .47.2.84.32 1.13.42.48.15.91.13 1.25.08.38-.06 1.17-.48 1.34-.94.16-.46.16-.86.11-.94-.05-.08-.18-.13-.38-.23Z"/>',
        ],
        'Email' => [
            'href' => 'mailto:?subject='.$encText.'&body='.rawurlencode($text."\n\n".$url),
            'icon' => '<path d="M2 2.5A1.5 1.5 0 0 0 .5 4v8A1.5 1.5 0 0 0 2 13.5h12A1.5 1.5 0 0 0 15.5 12V4A1.5 1.5 0 0 0 14 2.5H2Zm0 1h12c.06 0 .12 0 .17.02L8 8.07 1.83 3.52A.6.6 0 0 1 2 3.5Zm-.5 1.2 5.6 4.13a1.5 1.5 0 0 0 1.8 0l5.6-4.13V12a.5.5 0 0 1-.5.5H2a.5.5 0 0 1-.5-.5V4.7Z"/>',
        ],
    ];

    $btn = 'inline-flex items-center gap-2 px-3.5 py-2 rounded-full text-sm font-semibold border border-black/10 bg-white shadow-sm transition hover:bg-[var(--color-katt-accent)] hover:text-white hover:border-transparent hover:-translate-y-0.5 hover:shadow-md focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-katt-accent)]';
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }} aria-label="Share this comic">
    <p class="text-xs uppercase tracking-wide text-black/40 mb-3 text-center">Share this strip</p>
    <div class="flex flex-wrap items-center justify-center gap-2">
        @foreach($links as $label => $link)
            <a href="{{ $link['href'] }}"
               class="{{ $btn }}"
               target="_blank"
               rel="noopener noreferrer"
               aria-label="Share on {{ $label }}">
                <svg viewBox="0 0 16 16" width="16" height="16" fill="currentColor" aria-hidden="true" class="shrink-0">{!! $link['icon'] !!}</svg>
                <span>{{ $label }}</span>
            </a>
        @endforeach

        <button type="button"
                class="{{ $btn }}"
                data-copy-link="{{ $url }}"
                aria-label="Copy link to this comic">
            <svg viewBox="0 0 16 16" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="shrink-0">
                <path d="M6.5 9.5a2.5 2.5 0 0 0 3.6.05l2-2a2.5 2.5 0 0 0-3.54-3.54l-1.1 1.1"/>
                <path d="M9.5 6.5a2.5 2.5 0 0 0-3.6-.05l-2 2a2.5 2.5 0 0 0 3.54 3.54l1.1-1.1"/>
            </svg>
            <span data-copy-label>Copy link</span>
        </button>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('[data-copy-link]').forEach(function (el) {
        el.addEventListener('click', function () {
            var url = el.getAttribute('data-copy-link');
            var label = el.querySelector('[data-copy-label]') || el;
            var done = function () {
                var original = label.textContent;
                label.textContent = 'Copied!';
                setTimeout(function () { label.textContent = original; }, 1500);
            };
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(done, done);
            } else {
                var ta = document.createElement('textarea');
                ta.value = url;
                document.body.appendChild(ta);
                ta.select();
                try { document.execCommand('copy'); } catch (e) {}
                document.body.removeChild(ta);
                done();
            }
        });
    });
</script>
@endpush
