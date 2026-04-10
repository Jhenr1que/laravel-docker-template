@extends('layouts.master')

@section('meta_title', '503')
@section('body_class', 'page-error page-error-503')

@section('content')
    <section class="section">
        <div class="container section-heading">
            <h1>503</h1>
            <p>{{ __('site.errors.unavailable') }}</p>
        </div>
    </section>
@endsection