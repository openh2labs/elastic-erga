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
        <div id="prefetch_uris">
            {!! Form::label('uri', 'librato uri') !!}
            {!! Form::input('uri', 'uri', null, ['size' => '75', 'class'=>'typeahead tt-query',  'autocomplete'=>'off', 'spellcheck'=>'false'])  !!}
        </div>
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

@section('typeahead')
    <script src="http://{{ $_SERVER['HTTP_HOST'] }}/js/typeahead.js/bloodhound.js"></script>
    <script src="http://{{ $_SERVER['HTTP_HOST'] }}/js/typeahead.js/typeahead.bundle.js"></script>
    <script type="text/javascript">
        var uris = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            // url points to a json file that contains an array of country names, see
            // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
            prefetch: {
                ttl: 0,
                url: window.location.origin + '/typeahead/listcolumn/uri/librato'
            }
        });

        // passing in `null` for the `options` arguments will result in the default
        // options being used
        $('#prefetch_uris .typeahead').typeahead(null, {
            name: 'uris',
            source: uris,
            limit: 10
        });


        var es_indexes = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            // url points to a json file that contains an array of country names, see
            // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
            prefetch: {
                ttl: 0,
                url: window.location.origin + '/typeahead/listcolumn/es_index'
            }
        });

        $('#prefetch_indexes .typeahead').typeahead(null, {
            name: 'es_indexes',
            source: es_indexes,
            limit: 10,
        });

        var es_types = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            // url points to a json file that contains an array of country names, see
            // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
            prefetch: {
                ttl: 0,
                url: window.location.origin + '/typeahead/listcolumn/es_type'
            }
        });

        $('#prefetch_types .typeahead').typeahead(null, {
            name: 'es_types',
            source: es_types,
            limit: 10,
        });

        var es_datetime_fields = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            // url points to a json file that contains an array of country names, see
            // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
            prefetch: {
                ttl: 0,
                url: window.location.origin + '/typeahead/listcolumn/es_datetime_field'
            }
        });

        $('#prefetch_es_date_time_field .typeahead').typeahead(null, {
            name: 'es_datetime_fields',
            source: es_datetime_fields,
            limit: 10,
        });


        var alert_email_senders = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            // url points to a json file that contains an array of country names, see
            // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
            prefetch: {
                ttl: 0,
                url: window.location.origin + '/typeahead/listcolumn/alert_email_sender'
            }
        });

        $('#prefetch_alert_email_sender .typeahead').typeahead(null, {
            name: 'alert_email_senders',
            source: alert_email_senders,
            limit: 10,
        });

        var alert_email_recipients = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            // url points to a json file that contains an array of country names, see
            // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
            prefetch: {
                ttl: 0,
                url: window.location.origin + '/typeahead/listcolumn/alert_email_recipient'
            }
        });

        $('#prefetch_alert_email_recipient .typeahead').typeahead(null, {
            name: 'alert_email_recipients',
            source: alert_email_recipients,
            limit: 10,
        });

    </script>
@endsection

@section('typeahead_css')
    <link rel="stylesheet" href="http://{{ $_SERVER['HTTP_HOST'] }}/css/typeahead.css">
@endsection