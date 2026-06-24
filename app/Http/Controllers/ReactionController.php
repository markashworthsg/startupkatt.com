<?php

namespace App\Http\Controllers;

use App\Models\Comic;
use App\Models\Reaction;
use App\Models\ReactionVote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ReactionController extends Controller
{
    /** How many recent strips' picks we remember in the cookie (bounds header size). */
    protected const VOTE_MEMORY = 100;

    /** Cookie name + lifetime (≈ a year, in minutes). */
    protected const COOKIE = 'sk_votes';
    protected const COOKIE_MINUTES = 60 * 24 * 365;

    /**
     * Cast one login-free vote on a published strip.
     *
     * Anti-spam: the tally only ever moves once per IP per comic: the durable
     * `reaction_votes` row (keyed by a hashed IP) is the sole authority for the
     * count, so clearing the cookie, going incognito, or scripting the endpoint
     * can't inflate anything. The route is also rate-limited (see routes/web.php).
     *
     * Tapping a new reaction sets it; tapping your current one removes it;
     * tapping a different one moves your vote. The cookie only mirrors your pick
     * so the UI can highlight it instantly; it plays no part in counting.
     */
    public function store(Request $request, Comic $comic): JsonResponse
    {
        // Can't react to something the public can't see yet.
        abort_if($comic->published_at->gt(Carbon::today()), Response::HTTP_NOT_FOUND);

        $allowed = array_keys(config('comics.reactions'));

        $validated = $request->validate([
            'reaction' => ['required', 'string', 'in:'.implode(',', $allowed)],
        ]);
        $reaction = $validated['reaction'];

        $ipHash = ReactionVote::hashFor($comic->id, $request->ip());

        $current = DB::transaction(function () use ($comic, $reaction, $ipHash) {
            $vote = ReactionVote::query()
                ->where('comic_id', $comic->id)
                ->where('ip_hash', $ipHash)
                ->lockForUpdate()
                ->first();

            // First vote from this network on this strip.
            if ($vote === null) {
                Reaction::bump($comic->id, $reaction);
                ReactionVote::create([
                    'comic_id' => $comic->id,
                    'ip_hash'  => $ipHash,
                    'reaction' => $reaction,
                ]);

                return $reaction;
            }

            // Tapping the current pick again clears it.
            if ($vote->reaction === $reaction) {
                Reaction::unbump($comic->id, $reaction);
                $vote->delete();

                return null;
            }

            // Otherwise move the single vote from the old reaction to the new one.
            Reaction::unbump($comic->id, $vote->reaction);
            Reaction::bump($comic->id, $reaction);
            $vote->update(['reaction' => $reaction]);

            return $reaction;
        });

        $counts = $comic->reactionCounts();

        return response()
            ->json([
                'counts'       => $counts,
                'total'        => array_sum($counts),
                'userReaction' => $current,
            ])
            ->cookie(self::COOKIE, $this->rememberPick($request, $comic->id, $current), self::COOKIE_MINUTES);
    }

    /**
     * Update the per-browser pick map ({comicId: reaction}) for the UI highlight.
     * Purely cosmetic; counting is governed entirely by the reaction_votes row.
     */
    protected function rememberPick(Request $request, int $comicId, ?string $reaction): string
    {
        $votes = json_decode((string) $request->cookie(self::COOKIE), true);
        $votes = is_array($votes) ? $votes : [];

        unset($votes[$comicId]);
        if ($reaction !== null) {
            $votes[$comicId] = $reaction; // move to the end so recent picks survive the cap
        }

        return json_encode(array_slice($votes, -self::VOTE_MEMORY, null, true));
    }
}
