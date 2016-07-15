@extends('master_layout')

@section('active_tab_terminal') class="active"@endsection

@section('css')
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/4.2.0/normalize.min.css" />
    <style>
        .container.credit {
            display: none;
        }
    </style>
@endsection

@section('main')
    <div class="row">
        <div class="col-md-12 terminal"></div>
    </div>
@endsection

@section('script_data')
<script>

    /**
     * Configuration container for componenets
     * Each component will have it's own key and a configuration object as value
     * @type Object
     */
    // TODO: fetch configuration from environment variables
    var componentConfig = {
        terminal: {
            TERMINAL_URL: '/api/v1/terminal',
            MANIFEST_FILE_PATH: 'build/terminal-app/manifest.json',
            ATTACH_COMPONENT_TO: ''
        }
    };

</script>
<script src="js/bootstrap-components.js"></script>
@endsection
