<title>@yield('meta_title', __('site.pages.home.meta_title'))</title>
<meta name="description" content="@yield('meta_description', __('site.pages.home.meta_description'))">
<meta name="robots" content="@yield('meta_robots', 'index,follow')">
<link rel="canonical" href="@yield('canonical', url()->current())">
<meta property="og:type" content="website">
<meta property="og:site_name" content="@yield('meta_title', __('site.pages.home.meta_title'))">
<meta property="og:title" content="@yield('meta_title', __('site.pages.home.meta_title'))">
<meta property="og:description" content="@yield('meta_description', __('site.pages.home.meta_description'))">
<meta property="og:url" content="@yield('canonical', url()->current())">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="@yield('meta_title', __('site.pages.home.meta_title'))">
<meta name="twitter:description" content="@yield('meta_description', __('site.pages.home.meta_description'))">