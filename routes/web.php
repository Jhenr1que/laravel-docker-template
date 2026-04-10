<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\WordpressCacheController;
use App\Services\Wordpress\WordpressBlogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/sobre', [PageController::class, 'about'])->name('about');
Route::get('/servicos', [PageController::class, 'services'])->name('services');
Route::get('/contato', [PageController::class, 'contact'])->name('contact');
Route::post('/contato', ContactController::class)->name('contact.submit');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::post('/internal/wordpress/cache/invalidate', WordpressCacheController::class)
    ->name('wordpress.cache.invalidate');

Route::get('/idioma/{locale}', function (string $locale): RedirectResponse {
    abort_unless(config('app.translations_enabled', true), 404);
    abort_unless(array_key_exists($locale, config('app.available_locales', [])), 404);

    session()->put('locale', $locale);

    return redirect()->back();
})->name('locale.switch');

Route::get('/sitemap.xml', function (WordpressBlogService $wordpressBlogService) {
    $blogHasPosts = false;

    try {
        $blogHasPosts = $wordpressBlogService->hasPublishedPosts();
    } catch (\Throwable) {
        $blogHasPosts = false;
    }

    $pages = collect([
        ['loc' => route('home'), 'priority' => '1.0'],
        ['loc' => route('about'), 'priority' => '0.8'],
        ['loc' => route('services'), 'priority' => '0.8'],
        ['loc' => route('contact'), 'priority' => '0.7'],
    ]);

    if ($blogHasPosts) {
        $pages->push(['loc' => route('blog.index'), 'priority' => '0.7']);
    }

    try {
        $posts = $wordpressBlogService->sitemapPosts(50)
            ->map(fn (object $post) => [
                'loc' => route('blog.show', $post->slug),
                'lastmod' => optional($post->modified_at ?? $post->published_at)->toAtomString(),
                'priority' => '0.6',
            ]);
    } catch (\Throwable) {
        $posts = collect();
    }

    $xml = [
        '<?xml version="1.0" encoding="UTF-8"?>',
        '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
    ];

    foreach ($pages as $page) {
        $xml[] = '<url>';
        $xml[] = '<loc>'.e($page['loc']).'</loc>';
        $xml[] = '<priority>'.e($page['priority']).'</priority>';
        $xml[] = '</url>';
    }

    foreach ($posts as $post) {
        $xml[] = '<url>';
        $xml[] = '<loc>'.e($post['loc']).'</loc>';

        if (! empty($post['lastmod'])) {
            $xml[] = '<lastmod>'.e($post['lastmod']).'</lastmod>';
        }

        $xml[] = '<priority>'.e($post['priority']).'</priority>';
        $xml[] = '</url>';
    }

    $xml[] = '</urlset>';

    return response()
        ->make(implode('', $xml))
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::get('/robots.txt', function () {
    return response()
        ->view('pages.seo.robots', [
            'sitemapUrl' => URL::route('sitemap'),
            'host' => Str::of(config('app.url'))->replaceFirst('http://', '')->replaceFirst('https://', '')->toString(),
        ])
        ->header('Content-Type', 'text/plain');
})->name('robots');
