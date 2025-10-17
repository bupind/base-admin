<!doctype html>
<html lang="en" class="no-focus">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>{{ config('backend.title') }} | {{ __('backend.login') }}</title>

    <meta name="description" content="{{ config('app.description') }}">
    <meta name="author" content="{{ config('app.author') }}">
    <meta name="robots" content="noindex, nofollow">

    <!-- Open Graph Meta -->
    <meta property="og:title" content="{{ config('backend.title') }}">
    <meta property="og:site_name" content="{{ config('backend.title') }}">
    <meta property="og:description" content="{{ config('app.description') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ config('app.url') }}">
    <meta property="og:image" content="">

    @if(!is_null($favicon = Admin::favicon()))
        <link rel="shortcut icon" href="{{$favicon}}">
    @endif

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Muli:300,400,400i,600,700">
    <link rel="stylesheet" href="{{ Admin::asset("local/css/codebase.css")}}">

    <style>
        .logo-img {
            max-width: 100%;
            margin-top: 100px;
            transform: scale(0.7) translate(-21%, 0);
        }
    </style>
</head>

<body>
<div id="page-container" class="main-content-boxed bg-secondary">
    <!-- Main Container -->
    <main id="main-container">
        <!-- Page Content -->
        <div class="bg-image" style="background-color: #003366;">

            <div class="row mx-0">
                <!-- Left side -->
                <div class="hero-static col-md-6 col-xl-8 d-none d-md-flex align-items-md-end">
                    <div class="p-30" data-toggle="appear" style="padding-bottom: 20px !important">

                        <p class="font-italic text-white mb-0">
                            &copy; {{ config('backend.name') }} {{ now()->format('Y') }}
                        </p>
                    </div>
                </div>

                <!-- Right side (Login Form) -->
                <div class="hero-static col-md-6 col-xl-4 d-flex align-items-center bg-white"
                     data-toggle="appear" data-class="animated fadeInRight">
                    <div class="content content-full" style="margin-top: -150px; padding-top: 0px">
                        <!-- Header -->
                        <div class="px-50 py-50" style="padding-top: 0px !important; padding-left: 30px !important">
                            <h1 class="h4 font-w700 mt-30 mb-10" style="margin-top: 60px !important">
                                Welcome to {{ config('backend.name') }}
                            </h1>
                        </div>
                        <!-- END Header -->

                        <!-- Sign In Form -->
                        <form class="px-30" action="{{ admin_url('auth/login') }}" method="POST" style="margin-top: 40px">
                            @csrf
                            @if($errors->has('attempts'))
                                <div class="alert alert-danger text-center">{{ $errors->first('attempts') }}</div>
                            @endif

                            <div class="form-group row">
                                <div class="col-12">
                                    <h2 class="h5 font-w400 text-muted mb-0">{{ __('backend.login') }}</h2>
                                    <div class="form-material floating">
                                        <input type="text" class="form-control" id="username" name="username" required>
                                        <label for="username">{{ __('backend.username') }}</label>
                                    </div>
                                    @error('username')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-12">
                                    <div class="form-material floating">
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <label for="password">{{ __('backend.password') }}</label>
                                    </div>
                                    @error('password')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            @if(config('backend.auth.remember'))
                                <div class="form-group row g-1">
                                    <div class="col-12 col-md-7">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                                            <label class="custom-control-label" for="remember">{{ __('backend.remember_me') }}</label>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group">
                                <button type="submit" class="btn btn-sm btn-hero btn-alt-primary w-100">
                                    <i class="si si-login mr-10"></i> {{ __('backend.login') }}
                                </button>
                            </div>
                        </form>
                        <!-- END Sign In Form -->
                    </div>
                </div>
            </div>
        </div>
        <!-- END Page Content -->
    </main>
</div>

<link rel="stylesheet" href="{{ Admin::asset("local/js/codebase.core.min.js")}}">
<link rel="stylesheet" href="{{ Admin::asset("local/js/codebase.app.min.js")}}">
</body>
</html>
