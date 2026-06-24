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
    | Order here is the order shown in the UI. The last one is deliberately
    | unhinged: the "this hit too close to home" vote for startup life.
    |
    | Each entry also carries a `superlative` used to headline its leaderboard
    | page (/top/{reaction}), e.g. "The funniest Startup Katt strips".
    |
    */

    'reactions' => [
        'love'      => ['emoji' => '😻', 'label' => 'Love it', 'superlative' => 'most loved'],
        'funny'     => ['emoji' => '😹', 'label' => 'Funny', 'superlative' => 'funniest'],
        'insightful' => ['emoji' => '🧠', 'label' => 'Insightful', 'superlative' => 'most insightful'],
        'shocking'  => ['emoji' => '🤯', 'label' => 'Shocking', 'superlative' => 'most shocking'],
        'gross'     => ['emoji' => '🤢', 'label' => 'Gross', 'superlative' => 'grossest'],
        'unhinged'  => ['emoji' => '🫠', 'label' => 'Disturbingly relatable', 'superlative' => 'most unhinged'],
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
