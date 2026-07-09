<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Incoming drop folder
    |--------------------------------------------------------------------------
    |
    | Drop new comic image files in here. The `comics:import` command scans
    | this folder, de-duplicates by file hash, orders new files by their file
    | modification time (earliest first), and assigns each one the next empty
    | future release date. One comic per day.
    |
    */

    'incoming_path' => env('COMICS_INCOMING_PATH') ?: storage_path('app/comics/incoming'),

    /*
    |--------------------------------------------------------------------------
    | Public storage subdirectory
    |--------------------------------------------------------------------------
    |
    | Imported art is copied onto the "public" disk under this subdirectory,
    | organised as comics/{year}/{month}/{slug}.{ext}. Run `php artisan
    | storage:link` once so these are web-accessible.
    |
    */

    'public_dir' => 'comics',

    /*
    |--------------------------------------------------------------------------
    | Allowed image extensions
    |--------------------------------------------------------------------------
    */

    'extensions' => ['png', 'jpg', 'jpeg', 'gif', 'webp'],

    /*
    |--------------------------------------------------------------------------
    | First release slot
    |--------------------------------------------------------------------------
    |
    | When no comics are scheduled yet (or every scheduled date is already in
    | the past), where should the next batch start releasing?
    |
    |   "today"    => the most recent file goes live today
    |   "tomorrow" => the schedule always starts the day after today
    |
    | Forward-fill always continues one day at a time after the latest
    | scheduled comic, so once you're ahead this only matters when catching up.
    |
    */

    'first_slot' => env('COMICS_FIRST_SLOT', 'today'),

    /*
    |--------------------------------------------------------------------------
    | Move files after import
    |--------------------------------------------------------------------------
    |
    | true  => delete the original from the incoming folder once imported
    | false => leave originals in place (hash de-dupe prevents re-importing)
    |
    */

    'move_after_import' => (bool) env('COMICS_MOVE_AFTER_IMPORT', true),

    /*
    |--------------------------------------------------------------------------
    | Reactions (login-free voting: a retention growth loop)
    |--------------------------------------------------------------------------
    |
    | Readers tap one reaction per strip (changeable, toggleable). It's a tiny
    | engagement investment, and the aggregate tallies act as social proof. The
    | loop is: react -> counts -> social proof + investment -> return + share ->
    | more readers -> more reactions. Keep this list short so it stays a
    | one-tap ritual, not gamification.
    |
    | The voice is Gen Z and native to the Startup Katt universe: the whole set
    | is affectionate (even "unhinged" is praise), so every leaderboard it powers
    | is one you'd proudly rank. Order here is the order shown in the UI; the last
    | two close on founder life: "too real" (the "this hit too close to home"
    | burnout vote) and "unhinged" (the deranged-startup-energy vote).
    |
    | Each entry also carries a `superlative` used to headline its leaderboard
    | page (/top/{reaction}), e.g. "The funniest Startup Katt strips".
    |
    */

    'reactions' => [
        'iconic'      => ['emoji' => '😻', 'label' => 'Iconic', 'superlative' => 'most iconic'],
        'funny'       => ['emoji' => '💀', 'label' => 'Dead', 'superlative' => 'funniest'],
        'galaxybrain' => ['emoji' => '🧠', 'label' => 'Galaxy brain', 'superlative' => 'most galaxy-brained'],
        'facts'       => ['emoji' => '💯', 'label' => 'Facts', 'superlative' => 'most spot-on'],
        'back'        => ['emoji' => '📈', 'label' => 'We\'re so back', 'superlative' => '"we\'re so back"'],
        'real'        => ['emoji' => '🫠', 'label' => 'Too real', 'superlative' => 'most painfully real'],
        'unhinged'    => ['emoji' => '🤪', 'label' => 'Unhinged', 'superlative' => 'most unhinged'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Site metadata (SEO / AEO defaults)
    |--------------------------------------------------------------------------
    */

    'site' => [
        'name'        => 'Startup Katt',
        'tagline'     => 'The daily webcomic about a cat trying to build a startup.',
        'author'      => 'Startup Katt',
        'twitter'     => '@startupkatt',
        'instagram'   => 'https://www.instagram.com/startupkatt',
        'telegram'    => 'https://t.me/startupkatt',
        'description' => 'Startup Katt: a daily AI-generated webcomic following the misadventures of a cat founder. New strip every day. Read the full archive.',

        // The human behind the comic. Linked from the footer of every page
        // and from the /about page.
        'creator'          => 'Mark Ashworth',
        'creator_linkedin' => 'https://www.linkedin.com/in/markashworthsg/',
    ],

    /*
    |--------------------------------------------------------------------------
    | beehiiv (hybrid newsletter layer)
    |--------------------------------------------------------------------------
    |
    | Leave blank to hide the signup form. When you create a beehiiv
    | publication, drop the embed URL (and optionally the publication id) here.
    | See CLAUDE.md → "beehiiv integration" for the three hook points.
    |
    */

    'beehiiv' => [
        'publication_id'  => env('BEEHIIV_PUBLICATION_ID'),

        // beehiiv API v2 key (Settings → API in beehiiv). When this and
        // publication_id are set, the on-brand signup form POSTs to our own
        // /subscribe endpoint, which creates the subscription server-side. This
        // is the reliable, fully-styleable path (no ad-blocker-prone embed).
        'api_key'         => env('BEEHIIV_API_KEY'),

        // beehiiv v3 Subscribe Form. The embed code is a <script> tag with a
        // data-beehiiv-form="<uuid>" attribute; put that uuid here. When set,
        // the inline form renders and takes precedence over everything below.
        'form_id'         => env('BEEHIIV_FORM_ID'),

        // Older inline iframe form (https://embeds.beehiiv.com/<uuid>). Used
        // only if form_id is not set.
        'embed_url'       => env('BEEHIIV_EMBED_URL'),

        // Your beehiiv publication homepage, e.g. https://startupkatt.beehiiv.com
        // Fallback when neither form_id nor embed_url is set: shows a
        // "Subscribe" button linking to {publication_url}/subscribe.
        'publication_url' => env('BEEHIIV_PUBLICATION_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics (Plausible: cookieless, privacy-first)
    |--------------------------------------------------------------------------
    |
    | Leave PLAUSIBLE_DOMAIN blank to disable analytics entirely (no script is
    | emitted, no third-party request). When set, a single ~1KB script loads
    | in the <head> (see layouts/app.blade.php) and the on-brand newsletter
    | form fires a "Newsletter Signup" goal on success.
    |
    | We use Plausible over GA4/Amplitude on purpose: no cookies (so no consent
    | banner), no cross-site tracking, no personal data — which keeps the
    | promises on the /legal privacy page true. See CLAUDE.md → "Analytics".
    |
    |   domain => the property name in your Plausible dashboard, usually the
    |             bare host, e.g. "startupkatt.com".
    |   src    => the script URL. Default is Plausible Cloud. Override for a
    |             self-hosted instance, a custom domain / proxy, or to switch
    |             to an extended script (e.g. .../js/script.outbound-links.js).
    |             Custom events ("Newsletter Signup") work with the default.
    |
    */

    'analytics' => [
        'domain' => env('PLAUSIBLE_DOMAIN'),
        'src'    => env('PLAUSIBLE_SRC', 'https://plausible.io/js/script.js'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Search Console (ownership verification)
    |--------------------------------------------------------------------------
    |
    | The verification token from a URL-prefix property's "HTML tag" method.
    | When set, layouts/app.blade.php emits the <meta google-site-verification>
    | tag. Leave blank to emit nothing. Search Console is where SEO/AEO work
    | actually surfaces (queries, impressions, CTR, indexing); pair it with
    | Plausible (audience) and on-site reactions (engagement).
    |
    */

    'search_console' => [
        'verification' => env('GOOGLE_SITE_VERIFICATION'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin (metadata editor)
    |--------------------------------------------------------------------------
    |
    | The /admin area lets you edit a comic's title, alt text, caption,
    | description and release date. It is a single HTTP Basic gate, no users
    | table, no registration. Set both ADMIN_USERNAME and ADMIN_PASSWORD in
    | .env to unlock it; if the password is blank the admin area is locked
    | (returns 403) so it is never accidentally left open in production.
    |
    */

    'admin' => [
        'username' => env('ADMIN_USERNAME', 'admin'),
        'password' => env('ADMIN_PASSWORD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Preview (secret sneak-peek link)
    |--------------------------------------------------------------------------
    |
    | A read-only "lite admin" view of the whole pipeline: every scheduled
    | (not-yet-public) strip plus the live ones, reachable at a secret URL:
    |
    |     /preview/{COMICS_PREVIEW_TOKEN}
    |
    | The same token also unlocks an individual future strip in the normal
    | reader via ?preview={token}. Leave COMICS_PREVIEW_TOKEN blank to disable
    | the feature entirely (any /preview/... URL then 404s). All preview pages
    | are noindex so they never leak into search.
    |
    */

    'preview' => [
        'token' => env('COMICS_PREVIEW_TOKEN'),
    ],

];
