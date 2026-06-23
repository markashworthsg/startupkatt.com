<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reaction extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'count' => 'integer',
        ];
    }

    public function comic(): BelongsTo
    {
        return $this->belongsTo(Comic::class);
    }

    /** Bump a reaction tally up by one, creating the row if needed. */
    public static function bump(int $comicId, string $reaction): void
    {
        static::query()->firstOrCreate(
            ['comic_id' => $comicId, 'reaction' => $reaction],
        )->increment('count');
    }

    /** Knock a reaction tally down by one, never below zero. */
    public static function unbump(int $comicId, string $reaction): void
    {
        static::query()
            ->where('comic_id', $comicId)
            ->where('reaction', $reaction)
            ->where('count', '>', 0)
            ->decrement('count');
    }
}
