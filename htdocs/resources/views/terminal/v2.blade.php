@extends('master_layout')

@section('active_tab_terminal') class="active"@endsection

@section('main')
    <div class="row">
        <div class="col-md-12 terminal"></div>
    </div>
@endsection

@section('script_data')
<script>
var MAIFEST_FILE_PATH = 'build/js/components/manifest.json';
</script>
<script src="build/js/components/bootstrap-components.js"></script>
@endsection
