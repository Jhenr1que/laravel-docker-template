@extends('layouts.master')
@section('meta_title', __('site.pages.home.meta_title'))
@section('meta_description', __('site.pages.home.meta_description'))
@section('body_class', 'page-home')
@section('page_style', 'resources/assets/scss/entries/home.scss')
@section('content')
    <section class="section">
        <div class="container section-heading">
            <h1 class="bg-black text-white">{{ __('site.navigation.home') }}</h1>
        </div>
    </section>
@endsection