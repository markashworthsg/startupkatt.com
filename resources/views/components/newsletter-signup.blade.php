@php
    $apiKey = config('comics.beehiiv.api_key');
    $publicationId = config('comics.beehiiv.publication_id');
    $publication = rtrim((string) config('comics.beehiiv.publication_url'), '/');

    // Custom server-side form needs both an API key and a publication id. If
    // they're missing we fall back to a plain link to the hosted subscribe page;
    // if there's no publication either, the whole block hides.
    $hasApi = $apiKey && $publicationId;
    $hostedUrl = $publication ? $publication.'/subscribe' : null;
@endphp

@if($hasApi || $hostedUrl)
<section {{ $attributes->merge(['class' => 'w-full overflow-hidden rounded-2xl border-2 border-[var(--color-katt-ink)] bg-white shadow-[6px_6px_0_0_var(--color-katt-ink)]']) }}>
    <div class="bg-[var(--color-katt-accent)]/10 px-6 py-6 sm:px-8 sm:py-7">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[var(--color-katt-accent)]">
            New strip every day
        </p>
        <h2 class="mt-1 text-2xl font-extrabold leading-tight text-[var(--color-katt-ink)] sm:text-3xl" style="font-family: var(--font-display)">
            Get Startup Katt before standup.
        </h2>
        <p class="mt-2 max-w-prose text-sm text-black/65 sm:text-base">
            One strip a day about a cat who's perpetually raising, for everyone who's ever called a nap a strategic offsite. Free, and you can unsubscribe the second your runway does.
        </p>

        @if($hasApi)
            <form
                data-newsletter
                action="{{ route('newsletter.subscribe') }}"
                method="POST"
                class="mt-4"
            >
                @csrf
                {{-- Honeypot. Hidden from humans; bots fill it and get rejected. --}}
                <div class="absolute -left-[9999px] top-auto h-px w-px overflow-hidden" aria-hidden="true">
                    <label>Company<input type="text" name="company" tabindex="-1" autocomplete="off"></label>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row">
                    <label for="newsletter-email" class="sr-only">Your email address</label>
                    <input
                        id="newsletter-email"
                        type="email"
                        name="email"
                        required
                        autocomplete="email"
                        placeholder="founder@yourstartup.com"
                        class="w-full rounded-xl border-2 border-[var(--color-katt-ink)] bg-white px-4 py-3 text-base text-[var(--color-katt-ink)] outline-none placeholder:text-black/35 focus:ring-2 focus:ring-[var(--color-katt-accent)]"
                    >
                    <button
                        type="submit"
                        data-newsletter-submit
                        class="shrink-0 rounded-xl border-2 border-[var(--color-katt-ink)] bg-[var(--color-katt-accent)] px-5 py-3 font-bold text-white transition active:translate-y-px disabled:opacity-60 hover:bg-[var(--color-katt-ink)]"
                    >
                        Get the daily strip
                    </button>
                </div>

                <p data-newsletter-message role="status" aria-live="polite" class="mt-2 hidden text-sm"></p>

                <p class="mt-2 text-xs text-black/45">
                    No spam. The cat respects your inbox more than your last board update.
                </p>
            </form>

            @if($hostedUrl)
                <noscript>
                    <a href="{{ $hostedUrl }}" target="_blank" rel="noopener"
                       class="mt-3 inline-block font-semibold text-[var(--color-katt-accent)] underline">Subscribe on beehiiv&nbsp;&rarr;</a>
                </noscript>
            @endif
        @else
            {{-- No API key configured: link straight to the hosted subscribe page. --}}
            <a
                href="{{ $hostedUrl }}"
                target="_blank"
                rel="noopener"
                onclick="if(window.plausible){window.plausible('Newsletter Signup')}"
                class="mt-4 inline-block rounded-xl border-2 border-[var(--color-katt-ink)] bg-[var(--color-katt-accent)] px-5 py-3 font-bold text-white transition active:translate-y-px hover:bg-[var(--color-katt-ink)]"
            >
                Get the daily strip
            </a>
            <p class="mt-2 text-xs text-black/45">
                No spam. The cat respects your inbox more than your last board update.
            </p>
        @endif
    </div>
</section>

@if($hasApi)
@once
@push('scripts')
<script>
(function () {
    document.querySelectorAll('form[data-newsletter]').forEach(function (form) {
        var btn = form.querySelector('[data-newsletter-submit]');
        var msg = form.querySelector('[data-newsletter-message]');
        var label = btn ? btn.textContent : '';

        function say(text, ok) {
            if (!msg) return;
            msg.textContent = text;
            msg.classList.remove('hidden');
            msg.style.color = ok ? 'var(--color-katt-ink)' : '#b42318';
            msg.style.fontWeight = '600';
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var email = form.querySelector('input[name="email"]');
            if (email && !email.checkValidity()) {
                email.reportValidity();
                return;
            }

            if (btn) { btn.disabled = true; btn.textContent = 'Subscribing...'; }

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(form)
            }).then(function (res) {
                return res.json().then(function (data) { return { res: res, data: data }; });
            }).then(function (out) {
                if (out.res.ok && out.data.ok) {
                    // Record the signup goal in Plausible (no-op if disabled).
                    if (window.plausible) { window.plausible('Newsletter Signup'); }
                    // Swap the whole form for the win state. No going back.
                    form.innerHTML = '<p class="rounded-xl border-2 border-[var(--color-katt-ink)] bg-white px-4 py-3 text-sm font-semibold text-[var(--color-katt-ink)]">' + out.data.message + '</p>';
                    return;
                }
                if (out.res.status === 422) {
                    say("That email looks off. Mind double-checking it?", false);
                } else {
                    say(out.data.message || "That didn't send (very startup of it). Mind trying again?", false);
                }
                if (btn) { btn.disabled = false; btn.textContent = label; }
            }).catch(function () {
                say("That didn't send (very startup of it). Mind trying again?", false);
                if (btn) { btn.disabled = false; btn.textContent = label; }
            });
        });
    });
})();
</script>
@endpush
@endonce
@endif
@endif
