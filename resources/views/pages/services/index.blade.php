@extends('layouts.master')
@section('meta_title', __('site.pages.services.meta_title'))
@section('meta_description', __('site.pages.services.meta_description'))
@section('body_class', 'page-services')
@section('page_style', 'resources/assets/scss/entries/services.scss')
@section('content')
    <section class="section">
        <div class="container section-heading">
            <h1>{{ __('site.navigation.services') }}</h1>
        </div>
    </section>
@endsection