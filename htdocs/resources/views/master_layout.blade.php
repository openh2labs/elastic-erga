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

@include('navbar')

<div class="container">

    <div class="starter-template">
        <br><br>
        <p class="lead">
            @yield('main')
        </p>
    </div>

</div><!-- /.container -->
<div class="container">
    <p>Powered by <a href="https://github.com/openh2labs/elastic-erga/wiki">elastic-erga</a>. Created by Mav Peri with contributions from the <a href="https://github.com/openh2labs/elastic-erga">Openh2labs</a> team and others.</p>
</div>


<!-- Load All JS from a single concatenated file, see gulpfile.js and resources/assets/js/app.js -->
<script src="{{ elixir("js/app.js") }}"></script>

</body>
</html>