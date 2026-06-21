@php
    $embed = config('comics.beehiiv.embed_url');
@endphp

@if($embed)
<section {{ $attributes->merge(['class' => 'w-full rounded-xl border border-black/10 bg-white p-5 text-center']) }}>
    <h2 class="text-lg font-bold" style="font-family: var(--font-display)">
        Get Startup Katt in your inbox
    </h2>
    <p class="text-sm text-black/60 mt-1 mb-3">A new strip every day. No spam, just cat founder chaos.</p>

    {{-- beehiiv embed. Swap for the <iframe> beehiiv gives you, or POST to their
         subscribe API. Controlled by BEEHIIV_EMBED_URL in .env. --}}
    <iframe
        src="{{ $embed }}"
        class="w-full"
        style="height: 80px; border: none; background: transparent;"
        title="Subscribe to Startup Katt"
        scrolling="no"
    ></iframe>
</section>
@endif
