<?php

namespace App\Http\Controllers;

use App\Models\Comic;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class ComicController extends Controller
{
    /** Homepage: the most recent published comic. */
    public function home()
    {
        $comic = Comic::latestComic();

        if (! $comic) {
            return response()->view('comics.empty');
        }

        return view('comics.show', [
            'comic'    => $comic,
            'previous' => $comic->previous(),
            'next'     => null, // it's the latest
            'isHome'   => true,
        ]);
    }

    /** A single comic by slug. Future-dated comics 404 until their release date. */
    public function show(Comic $comic)
    {
        abort_if(
            $comic->published_at->gt(Carbon::today()),
            Response::HTTP_NOT_FOUND
        );

        return view('comics.show', [
            'comic'    => $comic,
            'previous' => $comic->previous(),
            'next'     => $comic->next(),
            'isHome'   => false,
        ]);
    }

    /** Full archive grid of every published comic, newest first. */
    public function archive()
    {
        $comics = Comic::published()
            ->orderByDesc('published_at')
            ->paginate(60);

        return view('comics.archive', compact('comics'));
    }

    /** About the comic and the human who makes it. */
    public function about()
    {
        return view('about');
    }
}
