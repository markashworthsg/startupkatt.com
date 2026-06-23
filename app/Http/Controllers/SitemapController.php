<?php

namespace App\Http\Controllers;

use App\Models\Comic;

class SitemapController extends Controller
{
    /** XML sitemap of the homepage, archive, leaderboards, and every comic. */
    public function index()
    {
        $comics = Comic::published()
            ->orderByDesc('published_at')
            ->get();

        // Leaderboards worth indexing: the overall page, plus any per-reaction
        // page with enough ranked strips to not be thin (3+).
        $topPages = [];
        if (Comic::topOverall(3)->count() >= 3) {
            $topPages[] = route('top');
        }
        foreach (array_keys(config('comics.reactions')) as $reaction) {
            if (Comic::topByReaction($reaction, 3)->count() >= 3) {
                $topPages[] = route('top', $reaction);
            }
        }

        return response()
            ->view('sitemap.index', compact('comics', 'topPages'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
