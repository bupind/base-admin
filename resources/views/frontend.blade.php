
<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? Admin::title() }} | {{ config('backend.title') }}</title>
    <meta name="description" content="{{ $description ?? '' }}">
    <meta name="mobile-web-app-capable" content="yes"/>
    <meta name="viewport" content="user-scalable=no, width=device-width, height=device-height, initial-scale=1, maximum-scale=1"/>
    @if(!is_null($favicon = Admin::favicon()))
        <link rel="shortcut icon" href="{{$favicon}}">
    @endif

    {!! Admin::headerJs() !!}
    {!! Admin::js() !!}
    {!! Admin::js_trans() !!}
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
          rel="stylesheet">
    <style type="text/css">
        body{
            font-family: 'Fira Sans', sans-serif
        }
    </style>
</head>

<body class="">
<div class="wrapper">
    @include('layout.partials.header')
    <main id="main">
        <div id="pjax-container">
            <!--pjax-start-->
            <div id="app">
                @if(!empty($_view_))
                    @include($_view_['view'], $_view_['data'])
                @endif

                @yield('content')
            </div>
            <!--pjax-end-->
        </div>
    </main>
    @include('layout.partials.footer')
</div>

<button id="totop" title="Go to top" style="display: none;"><i class="icon-chevron-up"></i></button>
</body>
</html>
