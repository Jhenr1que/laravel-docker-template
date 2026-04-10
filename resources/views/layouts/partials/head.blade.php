<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('layouts.partials.metatags')
    @php
        $styleStrategy = config('frontend.style_strategy', 'hybrid');
        $pageStyle = trim($__env->yieldContent('page_style'));
        $sassEntries = array_filter([
            'resources/assets/scss/app.scss',
            $pageStyle !== '' ? $pageStyle : null,
        ]);
    @endphp
    @if (in_array($styleStrategy, ['sass', 'hybrid'], true))
        @vite($sassEntries)
    @endif
    @if (in_array($styleStrategy, ['tailwind', 'hybrid'], true))
        @vite(['resources/assets/css/style.css'])
    @endif
    @vite(['resources/assets/js/app.js'])
</head>