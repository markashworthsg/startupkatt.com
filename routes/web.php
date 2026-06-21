<?php

use App\Http\Controllers\ComicController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ComicController::class, 'home'])->name('home');

Route::get('/archive', [ComicController::class, 'archive'])->name('comics.archive');

// Machine-readable endpoints
Route::get('/feed', [FeedController::class, 'rss'])->name('feed');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Each comic gets its own SEO-friendly URL: /comic/{slug}
// Keep this last so it doesn't shadow the named routes above.
Route::get('/comic/{comic}', [ComicController::class, 'show'])->name('comics.show');
