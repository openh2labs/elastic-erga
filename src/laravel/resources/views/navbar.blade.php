<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="top-level container">
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

                <li @yield('active_tab_terminal')><a href="/terminal">Terminal</a></li>


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