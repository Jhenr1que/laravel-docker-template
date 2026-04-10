<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('layouts.partials.metatags')
    @php
        $pageStyle = trim($__env->yieldContent('page_style'));
        $sassEntries = array_filter([
            'resources/assets/scss/app.scss',
            $pageStyle !== '' ? $pageStyle : null,
        ]);
    @endphp
    @if (in_array(config('frontend.style_strategy', 'sass'), ['sass', 'hybrid'], true))
        @vite($sassEntries)
    @endif
    @if (in_array(config('frontend.style_strategy', 'sass'), ['tailwind', 'hybrid'], true))
        @vite(['resources/css/tailwind.css'])
    @endif
</head>