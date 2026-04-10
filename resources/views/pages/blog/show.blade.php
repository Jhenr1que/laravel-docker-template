@extends('layouts.master')
@section('meta_title', $post->title)
@section('meta_description', $post->summary)
@section('canonical', route('blog.show', $post->slug))
@section('body_class', 'page-post')
@section('page_style', 'resources/assets/scss/entries/post.scss')
@section('content')
    <section class="section">
        <div class="container section-heading">
            <h1>{{ $post->title }}</h1>
        </div>
    </section>
@endsection