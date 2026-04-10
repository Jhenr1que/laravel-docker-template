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
@endsection