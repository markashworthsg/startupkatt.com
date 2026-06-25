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

    /**
     * A single comic by slug. Future-dated comics 404 until their release date,
     * unless a valid ?preview={token} is supplied (the secret sneak-peek link).
     */
    public function show(Request $request, Comic $comic)
    {
        $isFuture = $comic->published_at->gt(Carbon::today());
        $preview = $isFuture && $this->previewUnlocked($request);

        abort_if($isFuture && ! $preview, Response::HTTP_NOT_FOUND);

        return view('comics.show', [
            'comic'        => $comic,
            // In preview, navigate across the whole pipeline incl. scheduled.
            'previous'     => $comic->previous($preview),
            'next'         => $comic->next($preview),
            'isHome'       => false,
            'preview'      => $preview,
            'previewToken' => $preview ? (string) $request->query('preview') : null,
        ]);
    }

    /** True when the request carries the configured secret preview token. */
    protected function previewUnlocked(Request $request): bool
    {
        $configured = (string) config('comics.preview.token');
        $supplied = (string) $request->query('preview', '');

        return $configured !== '' && hash_equals($configured, $supplied);
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

    /** Legal, privacy, and the fiction disclaimer (in a lawyerly voice). */
    public function legal()
    {
        return view('legal');
    }
}
