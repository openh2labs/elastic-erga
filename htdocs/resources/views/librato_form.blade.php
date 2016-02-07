@extends('master_layout')

@section('main')

    <a href="/alert/edit/{!! $alert->id !!}">Back to edit the alert</a>
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
        <div id="prefetch_usernames">
            {!! Form::label('username', 'librato username') !!}
            {!! Form::input('username', 'username', null, ['size' => '75',  'class'=>'typeahead tt-query',  'autocomplete'=>'off', 'spellcheck'=>'false'])  !!}
        </div>
    </p>
    <p>
        <div id="prefetch_api_keys">
            {!! Form::label('api_key', 'librato api key') !!}
            {!! Form::input('api_key', 'api_key', null, ['size' => '100',  'class'=>'typeahead tt-query',  'autocomplete'=>'off', 'spellcheck'=>'false'])  !!}
      </div>
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
        <div id="prefetch_sources">
            {!! Form::label('source', 'source') !!}
            {!! Form::input('source', 'source', null, ['size' => '75',  'class'=>'typeahead tt-query',  'autocomplete'=>'off', 'spellcheck'=>'false'])  !!} for example elastic-erga
        </div>
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

        var usernames = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            // url points to a json file that contains an array of country names, see
            // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
            prefetch: {
                ttl: 0,
                url: window.location.origin + '/typeahead/listcolumn/username/librato'
            }
        });

        $('#prefetch_usernames .typeahead').typeahead(null, {
            name: 'usernames',
            source: usernames,
            limit: 10,
        });

        var api_keys = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            // url points to a json file that contains an array of country names, see
            // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
            prefetch: {
                ttl: 0,
                url: window.location.origin + '/typeahead/listcolumn/api_key/librato'
            }
        });

        $('#prefetch_api_keys .typeahead').typeahead(null, {
            name: 'api_keys',
            source: api_keys,
            limit: 10,
        });

        var sources = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            // url points to a json file that contains an array of country names, see
            // https://github.com/twitter/typeahead.js/blob/gh-pages/data/countries.json
            prefetch: {
                ttl: 0,
                url: window.location.origin + '/typeahead/listcolumn/source/librato'
            }
        });

        $('#prefetch_sources .typeahead').typeahead(null, {
            name: 'sources',
            source: sources,
            limit: 10,
        });

    </script>
@endsection

@section('typeahead_css')
    <link rel="stylesheet" href="http://{{ $_SERVER['HTTP_HOST'] }}/css/typeahead.css">
@endsection