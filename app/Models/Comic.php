<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class Comic extends Model
{
    /** @use HasFactory<\Database\Factories\ComicFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'published_at'    => 'date',
            'file_created_at' => 'datetime',
            'number'          => 'integer',
            'width'           => 'integer',
            'height'          => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Route binding
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /** Only comics whose release date is today or earlier. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereDate('published_at', '<=', Carbon::today());
    }

    /** Future (scheduled but not yet live) comics. */
    public function scopeScheduled(Builder $query): Builder
    {
        return $query->whereDate('published_at', '>', Carbon::today());
    }

    /*
    |--------------------------------------------------------------------------
    | Navigation (within the published set)
    |--------------------------------------------------------------------------
    */

    public function previous(bool $includeScheduled = false): ?Comic
    {
        return static::query()
            ->unless($includeScheduled, fn (Builder $q) => $q->published())
            ->where('published_at', '<', $this->published_at)
            ->orderByDesc('published_at')
            ->first();
    }

    public function next(bool $includeScheduled = false): ?Comic
    {
        return static::query()
            ->unless($includeScheduled, fn (Builder $q) => $q->published())
            ->where('published_at', '>', $this->published_at)
            ->orderBy('published_at')
            ->first();
    }

    public static function firstComic(): ?Comic
    {
        return static::published()->orderBy('published_at')->first();
    }

    public static function latestComic(): ?Comic
    {
        return static::published()->orderByDesc('published_at')->first();
    }

    /** First/last across the whole pipeline (incl. scheduled) — preview nav. */
    public static function firstOverall(): ?Comic
    {
        return static::orderBy('published_at')->first();
    }

    public static function latestOverall(): ?Comic
    {
        return static::orderByDesc('published_at')->first();
    }

    public function isLatest(): bool
    {
        return $this->next() === null;
    }

    public function isFirst(): bool
    {
        return $this->previous() === null;
    }

    /*
    |--------------------------------------------------------------------------
    | Reactions (lightweight, login-free voting)
    |--------------------------------------------------------------------------
    */

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }

    /**
     * Tally for every configured reaction, keyed by reaction slug, with any
     * un-voted reactions filled in as 0 so the UI always renders the full set.
     *
     * @return array<string, int>
     */
    public function reactionCounts(): array
    {
        $stored = $this->reactions()->pluck('count', 'reaction')->all();

        $counts = [];
        foreach (array_keys(config('comics.reactions')) as $key) {
            $counts[$key] = (int) ($stored[$key] ?? 0);
        }

        return $counts;
    }

    /** The reaction this strip earned most, or null if it has none yet. */
    public function topReaction(): ?string
    {
        $counts = array_filter($this->reactionCounts());
        if ($counts === []) {
            return null;
        }
        arsort($counts);

        return array_key_first($counts);
    }

    /**
     * Published strips ranked by total reactions (the overall leaderboard).
     * Each result carries a `reactions_total` attribute. Strips with no
     * reactions are excluded so the page never lists dead weight.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Comic>
     */
    public static function topOverall(int $limit = 25): \Illuminate\Database\Eloquent\Collection
    {
        return static::published()
            ->select('comics.*')
            ->selectRaw('COALESCE(SUM(reactions.count), 0) as reactions_total')
            ->join('reactions', 'reactions.comic_id', '=', 'comics.id')
            ->groupBy('comics.id')
            ->havingRaw('SUM(reactions.count) > 0')
            ->orderByDesc('reactions_total')
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Published strips ranked by a single reaction's tally (e.g. funniest).
     * Each result carries a `reactions_total` attribute (that reaction's count).
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Comic>
     */
    public static function topByReaction(string $reaction, int $limit = 25): \Illuminate\Database\Eloquent\Collection
    {
        return static::published()
            ->select('comics.*', 'reactions.count as reactions_total')
            ->join('reactions', 'reactions.comic_id', '=', 'comics.id')
            ->where('reactions.reaction', $reaction)
            ->where('reactions.count', '>', 0)
            ->orderByDesc('reactions.count')
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    */

    public function getUrlAttribute(): string
    {
        return route('comics.show', $this);
    }

    public function getImageUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->image_path);
    }

    /** Dedicated social image if present, otherwise the strip itself. */
    public function getOgImageUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->og_image_path ?: $this->image_path);
    }

    /** Best available text for meta descriptions / RSS summaries. */
    public function getMetaDescriptionAttribute(): string
    {
        return $this->description
            ?: $this->caption
            ?: $this->alt_text
            ?: config('comics.site.description');
    }
}
