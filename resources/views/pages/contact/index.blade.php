@extends('layouts.master')
@section('meta_title', __('site.pages.contact.meta_title'))
@section('meta_description', __('site.pages.contact.meta_description'))
@section('body_class', 'page-contact')
@section('page_style', 'resources/assets/scss/entries/contact.scss')
@section('content')
    <section class="section">
        <div class="container section-heading">
            <h1>{{ __('site.navigation.contact') }}</h1>
        </div>
    </section>
@endsection