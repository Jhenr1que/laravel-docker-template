@extends('layouts.master')
@section('meta_title', __('site.pages.about.meta_title'))
@section('meta_description', __('site.pages.about.meta_description'))
@section('body_class', 'page-about')
@section('page_style', 'resources/assets/scss/entries/about.scss')
@section('content')
    <section class="section">
        <div class="container section-heading">
            <h1>{{ __('site.navigation.about') }}</h1>
        </div>
    </section>
@endsection