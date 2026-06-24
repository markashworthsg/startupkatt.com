<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ComicAdminController extends Controller
{
    /** List every comic (published and scheduled), newest release first. */
    public function index()
    {
        $comics = Comic::orderByDesc('published_at')->paginate(60);

        return view('admin.index', compact('comics'));
    }

    /** Edit a single comic's metadata and release date. */
    public function edit(Comic $comic)
    {
        return view('admin.edit', compact('comic'));
    }

    /**
     * Persist edits. `published_at` stays unique (one comic per calendar day);
     * the rest are the human-facing SEO/accessibility fields.
     */
    public function update(Request $request, Comic $comic): RedirectResponse
    {
        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'alt_text'     => ['required', 'string', 'max:1000'],
            'caption'      => ['nullable', 'string', 'max:5000'],
            'description'  => ['nullable', 'string', 'max:5000'],
            'published_at' => [
                'required',
                'date',
                // One comic per calendar day. Compare by date (the column is cast
                // to a date but stored with a 00:00:00 time, so an exact-string
                // unique rule would miss collisions) and ignore this comic itself.
                function (string $attribute, mixed $value, \Closure $fail) use ($comic) {
                    $clash = Comic::whereDate('published_at', $value)
                        ->whereKeyNot($comic->getKey())
                        ->exists();

                    if ($clash) {
                        $fail('Another comic is already scheduled for that date. One comic per day.');
                    }
                },
            ],
        ]);

        $comic->update($validated);

        return redirect()
            ->route('admin.index')
            ->with('status', "Saved “{$comic->title}”. Startup Cat approves.");
    }
}
