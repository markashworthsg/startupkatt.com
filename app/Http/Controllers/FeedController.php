<?php

namespace App\Http\Controllers;

use App\Models\Comic;

class FeedController extends Controller
{
    /**
     * RSS 2.0 feed of the latest published comics.
     *
     * This is also the integration point for beehiiv's RSS-to-email feature:
     * point a beehiiv automation at https://startupkatt.com/feed and it will
     * email subscribers each new strip with zero extra code.
     */
    public function rss()
    {
        $comics = Comic::published()
            ->orderByDesc('published_at')
            ->limit(50)
            ->get();

        return response()
            ->view('feed.rss', compact('comics'))
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}
