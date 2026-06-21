<?php

use App\Http\Controllers\Admin\ComicAdminController;
use App\Http\Controllers\ComicController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ComicController::class, 'home'])->name('home');

// Admin metadata editor — gated by a single HTTP Basic credential (see config/comics.php).
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [ComicAdminController::class, 'index'])->name('index');
    Route::get('/comics/{comic}/edit', [ComicAdminController::class, 'edit'])->name('comics.edit');
    Route::put('/comics/{comic}', [ComicAdminController::class, 'update'])->name('comics.update');
});

Route::get('/archive', [ComicController::class, 'archive'])->name('comics.archive');
Route::get('/about', [ComicController::class, 'about'])->name('about');

// Machine-readable endpoints
Route::get('/feed', [FeedController::class, 'rss'])->name('feed');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Each comic gets its own SEO-friendly URL: /comic/{slug}
// Keep this last so it doesn't shadow the named routes above.
Route::get('/comic/{comic}', [ComicController::class, 'show'])->name('comics.show');
