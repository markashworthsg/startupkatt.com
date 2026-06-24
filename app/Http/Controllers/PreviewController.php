<?php

namespace App\Http\Controllers;

use App\Models\Comic;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class PreviewController extends Controller
{
    /**
     * Secret "lite admin" sneak-peek: a scrollable feed of the whole pipeline:
     * every scheduled (not-yet-public) strip up top, then the live archive.
     * Reached at /preview/{token}; a wrong or unset token 404s indistinguishably.
     */
    public function index(string $token)
    {
        $configured = (string) config('comics.preview.token');

        abort_unless(
            $configured !== '' && hash_equals($configured, $token),
            Response::HTTP_NOT_FOUND
        );

        $today = Carbon::today();

        $comics = Comic::orderByDesc('published_at')->get();

        return view('comics.preview', [
            'token'     => $token,
            'scheduled' => $comics->filter(fn (Comic $c) => $c->published_at->gt($today))
                ->sortBy('published_at')
                ->values(),
            'published' => $comics->filter(fn (Comic $c) => ! $c->published_at->gt($today))
                ->values(),
        ]);
    }
}
