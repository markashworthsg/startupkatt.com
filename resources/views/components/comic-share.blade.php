@props([
    'comic',
])

@php
    $site = config('comics.site');
    $url = $comic->url;
    $text = $comic->title.' — '.$site['name'];
    $enc = rawurlencode($url);
    $encText = rawurlencode($text);

    $links = [
        'X' => 'https://twitter.com/intent/tweet?text='.$encText.'&url='.$enc,
        'LinkedIn' => 'https://www.linkedin.com/sharing/share-offsite/?url='.$enc,
        'Facebook' => 'https://www.facebook.com/sharer/sharer.php?u='.$enc,
        'Reddit' => 'https://www.reddit.com/submit?url='.$enc.'&title='.$encText,
    ];

    $btn = 'inline-flex items-center gap-1.5 px-3 py-2 rounded-md text-sm font-semibold border border-black/10 bg-white transition hover:bg-[var(--color-katt-accent)] hover:text-white hover:border-transparent';
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }} aria-label="Share this comic">
    <p class="text-xs uppercase tracking-wide text-black/40 mb-2 text-center">Share this strip</p>
    <div class="flex flex-wrap items-center justify-center gap-2">
        @foreach($links as $label => $href)
            <a href="{{ $href }}"
               class="{{ $btn }}"
               target="_blank"
               rel="noopener noreferrer"
               aria-label="Share on {{ $label }}">{{ $label }}</a>
        @endforeach

        <button type="button"
                class="{{ $btn }}"
                data-copy-link="{{ $url }}"
                aria-label="Copy link to this comic">Copy link</button>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('[data-copy-link]').forEach(function (el) {
        el.addEventListener('click', function () {
            var url = el.getAttribute('data-copy-link');
            var done = function () {
                var original = el.textContent;
                el.textContent = 'Copied!';
                setTimeout(function () { el.textContent = original; }, 1500);
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
