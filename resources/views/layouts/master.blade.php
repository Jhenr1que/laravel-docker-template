<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('layouts.partials.head')
    <body class="@yield('body_class', 'page-default')">
        <div class="site-shell">
            @include('includes.header')
            <main class="site-main">@yield('content')</main>
            @include('includes.footer')
        </div>
    </body>
</html>