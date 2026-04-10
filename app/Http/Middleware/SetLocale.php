<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.translations_enabled', true)) {
            App::setLocale(config('app.locale'));

            return $next($request);
        }

        $availableLocales = array_keys(config('app.available_locales', []));
        $locale = session('locale', config('app.locale'));

        if (! in_array($locale, $availableLocales, true)) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);

        return $next($request);
    }
}