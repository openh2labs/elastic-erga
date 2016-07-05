@extends('master_layout')

@section('active_tab_terminal') class="active"@endsection

@section('main')
    <div class="row">
        <div class="col-md-12 terminal"></div>
    </div>
@endsection

@section('script_data')
<script>
var MAIFEST_FILE_PATH = 'build/terminal-app/manifest.json';
</script>
<script src="js/bootstrap-components.js"></script>
@endsection
