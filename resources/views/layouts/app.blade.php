<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('pageTitle') | {{ config('app.name', 'Madison') }}</title>

    <!-- Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="/manifest.json">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="theme-color" content="#ffffff">

    <!-- Styles -->
    <link href="{{ elixir('css/app.css') }}" rel="stylesheet">
    @stack('styles')

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
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
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <a href="{{ route('documents.index') }}">
                                <strong>@lang('messages.document.list')</strong>
                            </a>
                        </li>

                        @foreach ($headerPages as $page)
                            <li><a href="{{ $page->getUrl() }}">{{ $page->nav_title }}</a></li>
                        @endforeach

                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ url('/login') }}">@lang('messages.login')</a></li>
                            <li><a href="{{ url('/register') }}">@lang('messages.register')</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    <i class="fa fa-user"></i> {{ Auth::user()->displayName }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ route('users.settings.edit', Auth::user()->id) }}">@lang('messages.settings')</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('sponsors.index') }}">@lang('messages.sponsor.list')</a>
                                    </li>
                                    @if (Auth::user()->isAdmin())
                                        <li role="separator" class="divider"></li>
                                        <li>
                                            <a href="{{ route('settings.site.index') }}">@lang('messages.admin')</a>
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

        <div class="container">
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
            @yield('content')

            <hr>

            <footer class="nav">
                <ul class="nav navbar-nav navbar-right">
                    @foreach ($footerPages as $page)
                        <li><a href="{{ $page->getUrl() }}">{{ $page->nav_title }}</a></li>
                    @endforeach
                </ul>
            </footer>
        </div>

    </div>

    <!-- Scripts -->
    <script src="{{ elixir('js/vendor.js') }}"></script>
    <script src="{{ elixir('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
