<?php

$server_host = $_SERVER['HTTP_HOST'];

?>!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>@yield('title')</title>

    <!-- Load All CSS from a single concatenated file. See resources/assests/less/app.less  -->
    <link rel="stylesheet" href="{{ elixir("css/app.css") }}">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">elastic-erga</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="dropdown <@yield('active_tab_alert_list')">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dashboard<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="/alert/home/all">All alerts setup</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="/alert/home/pct_state">Active percentage alerts</a></li>
                        <li><a href="/alert/home/hit_state">Active hit alerts</a></li>
                        <li><a href="/alert/home/zero_hit_state">Active zero hit alerts</a></li>
                        <li><a href="/alert/home/es_config_error_state">Errors with configuration</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="/alert/createnew">Create new alert</a></li>
                    </ul>
                </li>

                <li @yield('active_tab_system_log')><a href="/alertrun/systemlog">System log</a></li>

                <ul class="nav navbar-nav">
                    <li class="dropdown <@yield('active_tab_misc_list')">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Misc.<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="/alert/searchtest">Check all now</a></li>
                        </ul>
                    </li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<div class="container">

    <div class="starter-template">
        <br><br>
        <p class="lead">
            @yield('main')
        </p>
    </div>

</div><!-- /.container -->
<div class="container">
    <p>Powered by <a href="https://github.com/openh2labs/elastic-erga/wiki">elastic-erga</a>.</p>
</div>


<!-- Load All JS from a single concatenated file, see gulpfile.js and resources/assets/js/app.js -->
<script src="{{ elixir("js/app.js") }}"></script>

</body>
</html>