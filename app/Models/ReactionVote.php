<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReactionVote extends Model
{
    protected $guarded = [];

    public function comic(): BelongsTo
    {
        return $this->belongsTo(Comic::class);
    }

    /**
     * Stable, non-reversible voter key for one (comic, IP) pair.
     *
     * Keyed by the app key so it can't be reconstructed from the table, and
     * salted with the comic id so the same IP yields a different hash on every
     * strip (no cross-comic correlation).
     */
    public static function hashFor(int $comicId, ?string $ip): string
    {
        return hash_hmac('sha256', $comicId.'|'.($ip ?? ''), (string) config('app.key'));
    }
}
