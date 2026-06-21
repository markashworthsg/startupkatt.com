<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Always emit https:// URLs in production so canonical tags, sitemaps
        // and OpenGraph image URLs are correct behind a TLS-terminating proxy.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
