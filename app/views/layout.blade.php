<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="">

        @yield('meta')

        <!-- Bootstrap core CSS -->
        <link href="{{ url() }}/css/bootstrap.css" rel="stylesheet">

        <!-- Add custom CSS here -->
        <link href="{{ url() }}/css/sb-admin.css" rel="stylesheet">
        <link rel="stylesheet" href="{{ url() }}/font-awesome/css/font-awesome.min.css">
        <!-- Page Specific CSS -->
        <link rel="stylesheet" href="http://cdn.oesmith.co.uk/morris-0.4.3.min.css">
        <!-- JavaScript -->
        <script type="text/javascript">var base_url = '{{ url() }}/'; var __domain = '{{ Config::get("doolox.system_domain") }}';</script>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script src="{{ url() }}/js/bootstrap.js"></script>
        <script src="{{ url() }}/js/jquery-cookie/jquery.cookie.js"></script>
        <script src="{{ url() }}/js/jquery.caret.js"></script>
        <script src="{{ url() }}/js/bootbox.min.js"></script>
        @if(Session::has('key'))<script type="text/javascript">window.name = '{{ Session::get("key") }}';</script>@endif
        <script src="{{ url() }}/js/doolox.js"></script>
        <link rel="shortcut icon" href="{{ url() }}/images/favicon.ico">
    </head>

    <body>

        <div id="wrapper">

            <!-- Sidebar -->
            <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/"><img src="{{ url('images/doolox-logo.png') }}" alt="" /> <div>Doolox</div></a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse navbar-ex1-collapse">
                    <ul class="nav navbar-nav side-nav">
                        @if(Route::currentRouteName() == 'doolox.dashboard')<li class="active">@else<li>@endif
                            <a href="{{ url() }}"><i class="fa fa-dashboard"></i> Dashboard</a>
                        </li>
@if (Sentry::check())
@if (Sentry::getUser()->isSuperUser() || Config::get('doolox.allow_user_management') || Config::get('doolox.hosting'))
                        <li class="dropdown">
                            <a href="javascript: void null;" onclick="toggle_dropdown('dropdown1');"><i class="fa fa-cog"></i> Settings <b class="caret"></b></a>
                            <ul class="dropdown-menu" id="dropdown1">
@if (Sentry::getUser()->isSuperUser() || Config::get('doolox.allow_user_management'))
                                @if(Route::currentRouteName() == 'user.manage_users' || Route::currentRouteName() == 'user.user_new')<li class="active">@else<li>@endif
                                    <a href="{{ route('user.manage_users') }}">Users</a>
                                </li>
@endif
@if (Config::get('doolox.hosting'))
                                @if(Route::currentRouteName() == 'domain.index')<li class="active">@else<li>@endif
                                    <a href="{{ route('domain.index') }}">Domains</a>
                                </li>
@endif
                            </ul>
                        </li>
@endif
                        @if(Route::currentRouteName() == 'user.account')<li class="active">@else<li>@endif
                            <a href="{{ route('user.account') }}"><i class="fa fa-user"></i> Account</a>
                        </li>
@endif
                    </ul>
                    <ul class="nav navbar-nav navbar-right navbar-user">
@if (Config::get('doolox.saas'))
                        <li class="dropdown alerts-dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-bell"></i> Usage Stats <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-stats">
                                <li>Management:  {{ Session::get('limit-management-current') }} / {{ Session::get('limit-management') }}</li>
                                <li><div class="progress"><div class="progress-bar progress-bar-info" style="width: {{ Session::get('percentage-management') }}%;"></div></div></li>
@if (Config::get('doolox.hosting'))
                                <li>Installations: {{ Session::get('limit-installations-current') }} / {{ Session::get('limit-installations') }}</li>
                                <li><div class="progress"><div class="progress-bar progress-bar-success" style="width: {{ Session::get('percentage-installations') }}%;"></div></div></li>
                                <li>Disc: {{ Session::get('limit-size-current') }} / {{ Session::get('limit-size') }} (MB)</li>
                                <li><div class="progress"><div class="progress-bar progress-bar-danger" style="width: {{ Session::get('percentage-size') }}%;"></div></div></li>
@endif
                            </ul>
                        </li>
@endif
@if (Sentry::check())
                        <li><a href="{{ route('user.logout') }}"><i class="fa fa-power-off"></i> Sign Out</a></li>
@else
                        <li><a href="{{ route('user.login') }}"><i class="fa fa-power-off"></i> Sign In</a></li>
@endif
                    </ul>
                </div><!-- /.navbar-collapse -->
            </nav>

            <div id="page-wrapper">

                @yield('content')

            </div><!-- /#page-wrapper -->

        </div><!-- /#wrapper -->

        @yield('specific')

@if(Config::get("doolox.google_analytics_code"))
        <script type="text/javascript">
            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', '{{ Config::get("doolox.google_analytics_code") }}']);
            _gaq.push(['_setAllowLinker', true]);
            _gaq.push(['_trackPageview']);
            (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();
        </script>
@endif

@if(App::environment() == 'production')
{{ Config::get('doolox.zopim') }}
@endif

    </body>
</html>
