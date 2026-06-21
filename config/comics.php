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
    | future release date — one comic per day.
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
    | Site metadata (SEO / AEO defaults)
    |--------------------------------------------------------------------------
    */

    'site' => [
        'name'        => 'Startup Katt',
        'tagline'     => 'The daily webcomic about a cat trying to build a startup.',
        'author'      => 'Startup Katt',
        'twitter'     => '@startupkatt',
        'description' => 'Startup Katt — a daily AI-generated webcomic following the misadventures of a cat founder. New strip every day. Read the full archive.',
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
        'publication_id' => env('BEEHIIV_PUBLICATION_ID'),
        'embed_url'      => env('BEEHIIV_EMBED_URL'),
    ],

];
