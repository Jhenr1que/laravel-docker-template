<?php

namespace App\Providers;

use App\Services\Wordpress\WordpressBlogService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::share('translationsEnabled', config('app.translations_enabled', true));
        View::share('availableLocales', config('app.available_locales', []));
        View::share('blogHasPosts', rescue(
            fn () => app(WordpressBlogService::class)->hasPublishedPosts(),
            false,
            report: false,
        ));
    }
}
