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
var MAIFEST_FILE_PATH = 'build/terminal-app/manifest.json';

// TODO: make a configurable parameter for api endpoint
var TERMINAL_URL = '/api/v1/terminal';

</script>
<script src="js/bootstrap-components.js"></script>
@endsection
