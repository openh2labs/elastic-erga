@extends('master_layout')

@section('main')

    @if ($type === "create")
        <h1>create a new librato integration</h1>
        {!!   Form::open(array('url' => array('librato/store', $alert->id))) !!}
    @endif
    @if ($type === "edit")
        <h1>edit librato integration for {{ $alert->description }}</h1>
        {!!  Form::model($librato, array('url' => array('librato/store', $alert->id))) !!}
        {!! Form::hidden('id', $librato->id) !!}
        @endif

    <p>
        {!! Form::label('uri', 'librato uri') !!}
        {!! Form::input('uri', 'uri', null, ['size' => '75'])  !!}
    </p>
    <p>
        {!! Form::label('username', 'librato username') !!}
        {!! Form::input('username', 'username', null, ['size' => '75'])  !!}
    </p>
    <p>
        {!! Form::label('api_key', 'librato api key') !!}
        {!! Form::input('api_key', 'api_key', null, ['size' => '75'])  !!}
    </p>
    </p>
    <p>
        {!! Form::label('gauge_ok', 'gauge description') !!}
        {!! Form::input('gauge_ok', 'gauge_ok', null, ['size' => '75'])  !!} type blank if you are not monitoring a total
    </p>
    <p>
        {!! Form::label('gauge_alert', 'gauge alert') !!}
        {!! Form::input('gauge_alert', 'gauge_alert', null, ['size' => '75'])  !!}
    </p>
    <p>
        {!! Form::label('source', 'source') !!}
        {!! Form::input('source', 'source', null, ['size' => '75'])  !!} for example elastic-erga
    </p>
    @if ($type === "create")
        <p>
            {!! Form::submit('create new alert monitor!') !!}
        </p>
    @elseif ($type === "edit")
        <p>
            {!! Form::submit('save changes') !!}
        </p>
    @endif
    {!!  Form::close() !!}
@endsection