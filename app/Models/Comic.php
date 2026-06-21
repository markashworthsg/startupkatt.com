<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    public function previous(): ?Comic
    {
        return static::published()
            ->where('published_at', '<', $this->published_at)
            ->orderByDesc('published_at')
            ->first();
    }

    public function next(): ?Comic
    {
        return static::published()
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
