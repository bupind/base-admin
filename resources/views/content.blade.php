@extends('backend::index', ['header' => strip_tags($header)])

@section('content')

    @foreach ($css_files as $css_file)
        <link rel="stylesheet" href="{{ $css_file }}">
    @endforeach
    @isset($css)
        <style type="text/css">{{ $css }}</style>
    @endisset

    <section class="content-header clearfix">
        <h1>
            {!! $header ?: trans('backend.title') !!}
            <small>{!! $description ?: trans('backend.description') !!}</small>
        </h1>

        @include('backend::partials.breadcrumb')

    </section>

    <section class="content">

        @include('backend::partials.alerts')
        @include('backend::partials.exception')
        @include('backend::partials.toastr')

        @if($_view_)
            @include($_view_['view'], $_view_['data'])
        @else
            {!! $_content_ !!}
        @endif

    </section>
@endsection
