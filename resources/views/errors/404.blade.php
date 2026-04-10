@extends('layouts.master')

@section('meta_title', '404')
@section('body_class', 'page-error page-error-404')

@section('content')
    <section class="section">
        <div class="container section-heading">
            <h1>404</h1>
            <p>{{ __('site.errors.not_found') }}</p>
        </div>
    </section>
@endsection