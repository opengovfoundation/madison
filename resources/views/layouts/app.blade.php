<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('pageTitle') | {{ config('app.name', 'Madison') }}</title>

    <!-- Social -->
    <meta property="og:site_name" content="{{ config('app.name', 'Madison') }}">
    <meta property="og:title" content="@yield('pageTitle')">
    <meta property="og:url" content="{{ Request::url() }}">
    @stack('meta')

    @if (config('madison.social_analytics.facebook_app_id'))
        <meta property="fb:app_id" content="{{ config('madison.social_analytics.facebook_app_id') }}" />
    @endif

    @if (config('madison.social_analytics.twitter_username'))
        <meta name="twitter:site" content="{{ config('madison.social_analytics.twitter_username') }}">
    @endif

    <!-- Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="/manifest.json">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="theme-color" content="#ffffff">

    <!-- Styles -->
    <link href="https://fonts.googleapis.com/css?family=Droid+Serif|Poppins:400,700" rel="stylesheet">
    <link href="{{ elixir('css/app.css') }}" rel="stylesheet">
    @stack('styles')

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>

    @if (App::environment('production', 'staging') && !empty(config('services.hotjar.site_id')))
        @include('partials/hotjar-script')
    @endif

    @if (App::environment('production', 'staging') && !empty(config('services.rollbar.client_side_access_token')))
        @include('partials/rollbar-script')
    @endif
</head>
<body>
    <div id="app" class="{{ isset($useDarkContentBg) ? 'dark-content' : '' }}">
        <nav id="main-nav" class="navbar navbar-static-top">
            <div class="container-fluid">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Madison') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        <li>
                            {{ Form::open(['route' => 'documents.index', 'method' => 'get', 'class' => 'navbar-form']) }}
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button class="btn btn-default" type="submit">
                                            <i class="fa fa-search" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <input class="form-control" placeholder="{{ trans('messages.search.placeholder') }}" name="q" type="text">
                                </div>
                            {{ Form::close() }}
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <a href="{{ route('documents.index') }}">
                                @lang('messages.document.list')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('sponsors.info') }}">
                                @lang('messages.sponsor.become')
                            </a>
                        </li>

                        @foreach ($headerPages as $page)
                            <li><a href="{{ $page->getUrl() }}">{{ $page->nav_title }}</a></li>
                        @endforeach

                        <li>
                            <span class="separator hidden-xs" role="separator"></span>
                            <hr class="visible-xs">
                        </li>

                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            @if (Route::currentRouteName() === 'login' || Route::currentRouteName() === 'register')
                                @php($redirect = request()->input('redirect'))
                            @else
                                @php($redirect = Request::path())
                            @endif
                            <li>{{ Html::linkRoute('login', trans('messages.login'), ['redirect' => $redirect]) }}</li>
                            <li>{{ Html::linkRoute('register', trans('messages.register'), ['redirect' => $redirect]) }}</li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ trans('messages.user_greeting', ['name' => Auth::user()->fname]) }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ route('users.settings.edit', Auth::user()->id) }}">@lang('messages.settings')</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('users.sponsors.index', Auth::user()) }}">@lang('messages.sponsor.list')</a>
                                    </li>
                                    @if (Auth::user()->isAdmin())
                                        <li role="separator" class="divider"></li>
                                        <li>
                                            <a href="{{ route('admin.site.index') }}">@lang('messages.administrator')</a>
                                        </li>
                                    @endif

                                    <li role="separator" class="divider"></li>

                                    <li>
                                        <a href="{{ url('/logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            @lang('messages.logout')
                                        </a>

                                        <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>

                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        <div id="content">
            <div class="container">
                <div class="alert alert-warning alert-important browser-support hidden" role="alert">
                    @lang('messages.browser_support_banner')
                </div>

                @if (Auth::check() && Auth::user()->token)
                    <div class="alert alert-info alert-important" role="alert">
                        @php ($resendLink = route('users.resend_email_verification', Auth::user()))
                        @lang('messages.email_verification.banner', [
                        'resendLinkOpen' => '<a href="'.$resendLink.'" onclick="event.preventDefault(); document.getElementById(\'resend-email-verification-form\').submit();">',
                        'resendLinkClose' => '</a>',
                        ])

                        <form id="resend-email-verification-form" action="{{ $resendLink }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </div>
                @endif

                @include('flash::message')
            </div>

            @yield('full-width-content')

            <div class="container">
                @yield('content')
            </div>
        </div>

        <div id="main-footer">
            <footer class="container">
                <div class="logo">
                    <a href-"https://opengovfoundation.org/" target="blank"><img src="/img/logo.svg"></a>
                </div>

                <ul>
                    @foreach ($footerPages as $page)
                        <li><a href="{{ $page->getUrl() }}" class="alt-link">{{ $page->nav_title }}</a></li>
                    @endforeach
                </ul>
            </footer>
        </div>
    </div>

    @if (config('madison.google_analytics_property_id'))
        <!-- Google Analytics -->
        <script>
            window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;
            ga('create', '{{ config('madison.google_analytics_property_id') }}', 'auto');
            ga('send', 'pageview');
        </script>
        <script async src='https://www.google-analytics.com/analytics.js'></script>
        <!-- End Google Analytics -->
     @endif

    <!-- Scripts -->
    <script src="{{ elixir('js/vendor.js') }}"></script>
    <script src="{{ elixir('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
