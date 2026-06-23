<?php

namespace App\Http\Controllers;

use App\Models\Comic;

class TopController extends Controller
{
    /** How many strips to rank on a leaderboard. */
    protected const LIMIT = 25;

    /**
     * The "top strips" leaderboard — a content/SEO loop built on real reader
     * votes. `/top` ranks by total reactions; `/top/{reaction}` ranks by a
     * single reaction (e.g. /top/funny, /top/unhinged).
     */
    public function index(?string $reaction = null)
    {
        $reactions = config('comics.reactions');

        abort_if($reaction !== null && ! array_key_exists($reaction, $reactions), 404);

        $comics = $reaction === null
            ? Comic::topOverall(self::LIMIT)
            : Comic::topByReaction($reaction, self::LIMIT);

        return view('comics.top', [
            'comics'    => $comics,
            'reaction'  => $reaction,
            'reactions' => $reactions,
        ]);
    }
}
