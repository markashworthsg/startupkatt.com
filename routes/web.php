<?php

use App\Http\Controllers\Admin\ComicAdminController;
use App\Http\Controllers\ComicController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PreviewController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ComicController::class, 'home'])->name('home');

// Admin metadata editor, gated by a single HTTP Basic credential (see config/comics.php).
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [ComicAdminController::class, 'index'])->name('index');
    Route::get('/comics/{comic}/edit', [ComicAdminController::class, 'edit'])->name('comics.edit');
    Route::put('/comics/{comic}', [ComicAdminController::class, 'update'])->name('comics.update');
});

Route::get('/archive', [ComicController::class, 'archive'])->name('comics.archive');
Route::get('/about', [ComicController::class, 'about'])->name('about');
Route::get('/legal', [ComicController::class, 'legal'])->name('legal');

// Top-strips leaderboard (content/SEO loop built on reaction tallies).
// /top = overall; /top/{reaction} = a single reaction's leaderboard.
Route::get('/top/{reaction?}', [TopController::class, 'index'])->name('top');

// Newsletter signup: the on-brand form POSTs here, we create the beehiiv
// subscription server-side. Rate-limited so it can't be used to spray emails.
Route::post('/subscribe', [NewsletterController::class, 'subscribe'])
    ->middleware('throttle:10,1')
    ->name('newsletter.subscribe');

// Machine-readable endpoints
Route::get('/feed', [FeedController::class, 'rss'])->name('feed');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Secret sneak-peek of the whole pipeline (scheduled + live). Gated by the
// COMICS_PREVIEW_TOKEN secret in the path; a wrong/unset token 404s.
Route::get('/preview/{token}', [PreviewController::class, 'index'])->name('preview');

// Permanent redirect for comic #1's old placeholder slug → its title-based slug.
// Must sit before the /comic/{comic} catch-all so it isn't treated as a slug.
Route::redirect('/comic/friday', '/comic/seed-round', 301);

// The 3-panel "Blockbuster" strip was replaced by its single-panel version.
Route::redirect('/comic/blockbuster-said-the-same-thing', '/comic/thinking-like-a-retailer-not-an-innovator', 301);

// Login-free reaction voting (the retention growth loop). POST so it never
// collides with the GET show route below. Rate-limited per IP so the endpoint
// can't be hammered to inflate counts (real readers never hit 20/min).
Route::post('/comic/{comic}/react', [ReactionController::class, 'store'])
    ->middleware('throttle:20,1')
    ->name('comics.react');

// Each comic gets its own SEO-friendly URL: /comic/{slug}
// Keep this last so it doesn't shadow the named routes above.
Route::get('/comic/{comic}', [ComicController::class, 'show'])->name('comics.show');
