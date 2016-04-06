@extends('master_layout')

@section('main')
    <h1>Terminal</h1>
    <div class="row">
        <div class="col-md-12 terminal">
            <meta type="js-module" name="terminal" data-parameters="{{ json_encode($hosts) }}">
        </div>
    </div>
@endsection

@section('active_tab_terminal') class="active"@endsection