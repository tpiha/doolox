<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Doolox Dashboard</title>

        <!-- Bootstrap core CSS -->
        <link href="{{ url() }}/css/bootstrap.css" rel="stylesheet">

        <!-- Add custom CSS here -->
        <link href="{{ url() }}/css/sb-admin.css" rel="stylesheet">
        <link rel="stylesheet" href="{{ url() }}/font-awesome/css/font-awesome.min.css">
        <!-- Page Specific CSS -->
        <link rel="stylesheet" href="http://cdn.oesmith.co.uk/morris-0.4.3.min.css">
        <!-- JavaScript -->
        <script type="text/javascript">var base_url = '{{ url() }}/';</script>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
        <script src="{{ url() }}/js/bootstrap.js"></script>
        <script src="{{ url() }}/js/jquery-cookie/jquery.cookie.js"></script>
        <script src="{{ url() }}/js/bootbox.min.js"></script>
        @if(Session::has('key'))<script type="text/javascript">window.name = '{{ Session::get("key") }}';</script>@endif
        <script src="{{ url() }}/js/doolox.js"></script>
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
                    <a class="navbar-brand" href="{{ url() }}"><img src="{{ url('images/doolox-logo.png') }}" alt="" /> <div>Doolox</div></a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse navbar-ex1-collapse">
                    <ul class="nav navbar-nav side-nav">
                        @if(Route::currentRouteName() == 'doolox.dashboard')<li class="active">@else<li>@endif
                            <a href="{{ url() }}"><i class="fa fa-dashboard"></i> Dashboard</a>
                        </li>
@if (Sentry::check())
                        <li class="dropdown">
@if (Sentry::getUser()->isSuperUser() || Config::get('doolox.allow_user_management'))
                            <a href="javascript: void null;" onclick="toggle_dropdown('dropdown1');"><i class="fa fa-cog"></i> Settings <b class="caret"></b></a>
                            <ul class="dropdown-menu" id="dropdown1">
                                @if(Route::currentRouteName() == 'user.manage_users' || Route::currentRouteName() == 'user.user_new')<li class="active">@else<li>@endif
                                    <a href="{{ route('user.manage_users') }}">Users</a>
                                </li>
                            </ul>
                        </li>
@endif
                        @if(Route::currentRouteName() == 'user.account')<li class="active">@else<li>@endif
                            <a href="{{ route('user.account') }}"><i class="fa fa-user"></i> Account</a>
                        </li>
@endif
                    </ul>
                    <ul class="nav navbar-nav navbar-right navbar-user">
                        <li><a href="{{ route('user.logout') }}"><i class="fa fa-power-off"></i> Log Out</a></li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </nav>

            <div id="page-wrapper">

                @yield('content')

            </div><!-- /#page-wrapper -->

        </div><!-- /#wrapper -->

        @yield('specific')

    </body>
</html>
