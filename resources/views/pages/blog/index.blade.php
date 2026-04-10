@extends('layouts.master')
@section('meta_title', __('site.pages.blog.meta_title'))
@section('meta_description', __('site.pages.blog.meta_description'))
@section('body_class', 'page-blog')
@section('page_style', 'resources/assets/scss/entries/blog.scss')
@section('content')
    <section class="section">
        <div class="container section-heading">
            <h1>{{ __('site.navigation.blog') }}</h1>
        </div>
    </section>

    @if ($posts->count() > 0)
        <section class="section pt-0">
            <div class="container">
                @foreach ($posts as $post)
                    <article class="mb-10 border-b border-black/10 pb-8 last:border-b-0">
                        <p class="mb-2 text-sm text-black/60">{{ $post->published_at_label }}</p>

                        <h2 class="mb-3 text-2xl font-semibold">
                            <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                        </h2>

                        @if ($post->category !== null)
                            <p class="mb-3 text-sm text-black/60">{{ $post->category->name }}</p>
                        @endif

                        <div class="prose max-w-none">
                            {!! $post->content !!}
                        </div>
                    </article>
                @endforeach

                {{ $posts->links() }}
            </div>
        </section>
    @endif
@endsection