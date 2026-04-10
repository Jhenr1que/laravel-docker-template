<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($pages as $page)
    <url><loc>{{ $page['loc'] }}</loc><priority>{{ $page['priority'] }}</priority></url>
@endforeach
@foreach ($posts as $post)
    <url>
        <loc>{{ $post['loc'] }}</loc>
        @if (! empty($post['lastmod']))<lastmod>{{ $post['lastmod'] }}</lastmod>@endif
        <priority>{{ $post['priority'] }}</priority>
    </url>
@endforeach
</urlset>