@extends('layouts.master')
@section('meta_title', $post->title)
@section('meta_description', $post->summary)
@section('canonical', route('blog.show', $post->slug))
@section('body_class', 'page-post')
@section('page_style', 'resources/assets/scss/entries/post.scss')
@section('content')
    <section class="section">
        <div class="container section-heading">
            @if ($post->category !== null)
                <p class="mb-2 text-sm text-black/60">{{ $post->category->name }}</p>
            @endif

            <h1>{{ $post->title }}</h1>

            @if ($post->published_at_label !== '')
                <p class="mt-3 text-sm text-black/60">{{ $post->published_at_label }}</p>
            @endif
        </div>
    </section>

    <section class="section pt-0">
        <div class="container">
            <div class="prose max-w-none">
                {!! $post->content !!}
            </div>

            @if ($relatedPosts->isNotEmpty())
                <div class="mt-10">
                    <h2 class="mb-4 text-2xl font-semibold">Mais posts</h2>

                    @foreach ($relatedPosts as $relatedPost)
                        <article class="mb-4 border-b border-black/10 pb-4 last:border-b-0">
                            <h3 class="text-xl font-semibold">
                                <a href="{{ route('blog.show', $relatedPost->slug) }}">{{ $relatedPost->title }}</a>
                            </h3>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection