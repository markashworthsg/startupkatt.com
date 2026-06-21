<?php

namespace App\Http\Controllers;

use App\Models\Comic;

class SitemapController extends Controller
{
    /** XML sitemap of the homepage, archive, and every published comic. */
    public function index()
    {
        $comics = Comic::published()
            ->orderByDesc('published_at')
            ->get();

        return response()
            ->view('sitemap.index', compact('comics'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
