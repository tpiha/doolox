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
                    <a class="navbar-brand" href="./">Doolox</a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse navbar-ex1-collapse">
                    <ul class="nav navbar-nav side-nav">
                        <li class="active"><a href="index.html"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                        <li><a href="charts.html"><i class="fa fa-cog"></i> Settings</a></li>
                        <li><a href="charts.html"><i class="fa fa-user"></i> Account</a></li>
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

        <!-- JavaScript -->
        <script src="{{ url() }}/js/jquery-1.10.2.js"></script>
        <script src="{{ url() }}/js/bootstrap.js"></script>

        <!-- Page Specific Plugins
        <script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
        <script src="http://cdn.oesmith.co.uk/morris-0.4.3.min.js"></script>
        <script src="js/morris/chart-data-morris.js"></script>
        <script src="js/tablesorter/jquery.tablesorter.js"></script>
        <script src="js/tablesorter/tables.js"></script> -->

    </body>
</html>
