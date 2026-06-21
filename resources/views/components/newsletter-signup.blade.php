@php
    $formId = config('comics.beehiiv.form_id');
    $embed = config('comics.beehiiv.embed_url');
    $publication = rtrim((string) config('comics.beehiiv.publication_url'), '/');
@endphp

@if($formId || $embed || $publication)
<section {{ $attributes->merge(['class' => 'w-full rounded-xl border border-black/10 bg-white p-5 text-center']) }}>
    <h2 class="text-lg font-bold" style="font-family: var(--font-display)">
        Get Startup Katt in your inbox
    </h2>
    <p class="text-sm text-black/60 mt-1 mb-3">A new strip every day. No spam, just cat founder chaos.</p>

    @if($formId)
        {{-- beehiiv v3 Subscribe Form. The loader.js script injects the form
             inline here. Controlled by BEEHIIV_FORM_ID in .env. --}}
        <script async src="https://subscribe-forms.beehiiv.com/v3/loader.js" data-beehiiv-form="{{ $formId }}"></script>
    @elseif($embed)
        {{-- Older inline iframe embed. Controlled by BEEHIIV_EMBED_URL in .env. --}}
        <iframe
            src="{{ $embed }}"
            class="w-full"
            style="height: 80px; border: none; background: transparent;"
            title="Subscribe to Startup Katt"
            scrolling="no"
        ></iframe>
    @else
        {{-- No inline embed configured; link to the beehiiv subscribe page.
             Controlled by BEEHIIV_PUBLICATION_URL in .env. --}}
        <a
            href="{{ $publication }}/subscribe"
            target="_blank"
            rel="noopener"
            class="inline-block rounded-lg bg-katt-accent px-5 py-2.5 font-bold text-white transition hover:opacity-90"
        >
            Subscribe free
        </a>
    @endif
</section>
@endif
