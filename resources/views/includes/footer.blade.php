<footer class="site-footer">
    <div class="container site-footer__grid">
        <div>
            <strong>{{ __('site.layout.footer') }}</strong>
        </div>
    </div>
    <div class="container site-footer__bottom">
        <span>{{ now()->year }} {{ __('site.footer.copyright') }}</span>
        <a href="{{ route('home') }}">{{ __('site.navigation.home') }}</a>
    </div>
</footer>