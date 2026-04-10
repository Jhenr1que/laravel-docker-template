<header class="site-header">
    <div class="container site-header__inner">
        <strong>{{ __('site.layout.header') }}</strong>

        <nav class="site-nav" aria-label="{{ __('site.navigation.language') }}">
            <a href="{{ route('home') }}" @class(['is-active' => request()->routeIs('home')])>{{ __('site.navigation.home') }}</a>
            <a href="{{ route('about') }}" @class(['is-active' => request()->routeIs('about')])>{{ __('site.navigation.about') }}</a>
            <a href="{{ route('services') }}" @class(['is-active' => request()->routeIs('services')])>{{ __('site.navigation.services') }}</a>
            @if ($blogHasPosts)
                <a href="{{ route('blog.index') }}" @class(['is-active' => request()->routeIs('blog.*')])>{{ __('site.navigation.blog') }}</a>
            @endif
            <a href="{{ route('contact') }}" @class(['is-active' => request()->routeIs('contact')])>{{ __('site.navigation.contact') }}</a>
        </nav>

        @if ($translationsEnabled && count($availableLocales) > 1)
            <div class="locale-switcher" aria-label="{{ __('site.navigation.language') }}">
                @foreach ($availableLocales as $localeKey => $localeLabel)
                    <a href="{{ route('locale.switch', $localeKey) }}" @class(['is-active' => app()->getLocale() === $localeKey])>{{ $localeLabel }}</a>
                @endforeach
            </div>
        @endif
    </div>
</header>