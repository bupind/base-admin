@extends('backend::index', ['header' => strip_tags($header)])
@section('content')
    @foreach ($css_files as $css_file)
        <link rel="stylesheet" href="{{ $css_file }}">
    @endforeach
    @isset($css)
        <style type="text/css">{{ $css }}</style>
    @endisset
    @include('backend::partials.breadcrumb')
    @php
        use Illuminate\Support\Str;
        $menus = (new \Base\Admin\Admin())->menuLinks();
        $currentUrl = url()->current();
        $activeMenu = collect($menus)->first(function ($menu) use ($currentUrl) {
            return $menu['uri'] && Str::contains($currentUrl, trim($menu['uri'], '/'));
        });

        $icon = $activeMenu['icon'] ?? 'fa fa-book';
    @endphp
    <section class="content-header d-flex align-items-center px-3 py-2">
        <div class="me-3">
            <i class="{{ $icon }} fs-2"></i>
        </div>
        <div>
            <h1 class="fw-bold mb-0 fs-5">{!! $header ?: trans('backend.title') !!}</h1><br/>
            <small class="text-muted">{!! $description ?: trans('backend.description') !!}</small>
        </div>
    </section>

    <section class="content p-3">
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
