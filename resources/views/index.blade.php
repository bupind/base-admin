<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ Admin::title() }}</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    @if(!is_null($favicon = Admin::favicon()))
        <link rel="shortcut icon" href="{{$favicon}}">
    @endif
    {!! Admin::css() !!}
    {!! Admin::headerJs() !!}
    {!! Admin::js() !!}
    {!! Admin::js_trans() !!}
</head>

<body class="{{config('backend.skin')}} {{ $body_classes }}" data-backend-title="{{ $backendTitle ?? config('backend.title') }}">

@if($alert = config('backend.top_alert'))
    <div class="alert">
        {!! $alert !!}
    </div>
@endif
<div class="wrapper">
    @include('backend::partials.header')
    @include('backend::partials.sidebar')
    <main id="main">
        <div id="pjax-container">
            <!--start-pjax-container-->
            {!! Admin::style() !!}
            <div id="app">
                @yield('content')
            </div>
            {!! Admin::html() !!}
            {!! Admin::script() !!}
            <!--end-pjax-container-->
        </div>
    </main>
</div>

@if (1==2)
    @include('backend::partials.footer')
@endif

<button id="totop" title="Go to top" style="display: none;"><i class="icon-chevron-up"></i></button>

<script>
    window.backendConfig = {
        title: "{{ config('backend.title') }}"
    };
    function LA() {}
    LA.token = "{{ csrf_token() }}";
    LA.user = @json($_user_);
</script>

</body>
</html>
