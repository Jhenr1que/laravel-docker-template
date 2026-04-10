<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Models\BlogPost;
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

Route::get('/idioma/{locale}', function (string $locale): RedirectResponse {
    abort_unless(config('app.translations_enabled', true), 404);
    abort_unless(array_key_exists($locale, config('app.available_locales', [])), 404);

    session()->put('locale', $locale);

    return redirect()->back();
})->name('locale.switch');

Route::get('/sitemap.xml', function () {
    $pages = collect([
        ['loc' => route('home'), 'priority' => '1.0'],
        ['loc' => route('about'), 'priority' => '0.8'],
        ['loc' => route('services'), 'priority' => '0.8'],
        ['loc' => route('contact'), 'priority' => '0.7'],
        ['loc' => route('blog.index'), 'priority' => '0.7'],
    ]);

    try {
        $posts = BlogPost::query()
            ->published()
            ->orderByDesc('post_date')
            ->limit(50)
            ->get()
            ->map(fn (BlogPost $post) => [
                'loc' => route('blog.show', $post->slug),
                'lastmod' => optional($post->post_modified ?? $post->post_date)->toAtomString(),
                'priority' => '0.6',
            ]);
    } catch (Throwable) {
        $posts = collect();
    }

    return response()
        ->view('pages.seo.sitemap', [
            'pages' => $pages,
            'posts' => $posts,
        ])
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
