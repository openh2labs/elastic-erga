<?php
/**
 * Created by PhpStorm.
 * User: mavperi
 * Date: 08/01/16
 * Time: 08:59
 */

//var_dump($alert);die;
?>

@extends('master_layout')

@section('main')


    @if ($type === "create")
        <h1>create a new alert</h1>
        {!!   Form::open(array('url' => 'alert/store')) !!}
    @endif
    @if ($type === "edit")
        <h1>edit alert : {{ $alert->description }}</h1>
        {!!  Form::model($alert, array('url' => array('alert/storeedit', $alert->id))) !!}
        {!! Form::hidden('id', $alert->id) !!}
    @endif

<p>
        {!! Form::label('description', 'alert description') !!}
        {!! Form::input('text', 'description', null, ['size' => '75'])  !!}
    </p>
    <p>
        {!! Form::label('criteria', 'alert query (json)') !!}
        {!! Form::textarea('criteria', null, ['cols'=>75, 'rows'=>20]) !!}
    </p>
    <p>
        {!! Form::label('criteria_total', 'total query (json)') !!}
        {!! Form::textarea('criteria_total', null, ['cols'=>75, 'rows'=>20]) !!}
    </p>
    <p>
    <div id="prefetch">
            {!! Form::label('es_host', 'es_host') !!}
            {!! Form::input('text', 'es_host', null, ['size' => '50', 'class'=>'typeahead tt-query',  'autocomplete'=>'off', 'spellcheck'=>'false'])  !!}
         </div>
    </p>
    <p>
         <div id="prefetch_indexes">
        {!! Form::label('es_index', 'es_index') !!}
        {!! Form::input('es_index', 'es_index', null, ['size' => '50', 'class'=>'typeahead tt-query',  'autocomplete'=>'off', 'spellcheck'=>'false'])  !!} supported wild cards are %Y% (year in YYYY format), %m% (month 01 to 12) and %d% (day 01 to 31)
        </div>
    </p>
    <p>
        <div id="prefetch_types">
             {!! Form::label('es_index', 'es_type') !!}
            {!! Form::input('es_index', 'es_type', null, ['size' => '50', 'class'=>'typeahead tt-query',  'autocomplete'=>'off', 'spellcheck'=>'false'])  !!}
        </div>
    </p>
    <p>
        <div id="prefetch_es_date_time_field">
            {!! Form::label('es_datetime_field', 'es_datetime_field') !!}
            {!! Form::input('es_datetime_field', 'es_datetime_field', null, ['size' => '50', 'class'=>'typeahead tt-query',  'autocomplete'=>'off', 'spellcheck'=>'false'])  !!}
        </div>
    </p>
    <p>
        {!! Form::label('minutes_back', 'minutes_back') !!}
        {!! Form::input('minutes_back', 'minutes_back', null, ['size' => '10'])  !!} How many minutes back you want to be checking
    </p>
    <p>
        {!! Form::label('pct_of_total_threshold', 'pct_of_total_threshold') !!}
        {!! Form::input('pct_of_total_threshold', 'pct_of_total_threshold', null, ['size' => '10'])  !!}%
    </p>
    <p>
        {!! Form::label('number_of_hits', 'number_of_hits') !!}
        {!! Form::input('number_of_hits', 'number_of_hits', null, ['size' => '10'])  !!}
    </p>
    <p>
        <div id="prefetch_alert_email_sender">
            {!! Form::label('alert_email_sender', 'alert_email_sender') !!}
            {!! Form::input('alert_email_sender', 'alert_email_sender', null, ['size' => '50', 'class'=>'typeahead tt-query',  'autocomplete'=>'off', 'spellcheck'=>'false'])  !!} (if smtp make sure its a permitted sender)
        </div>
    </p>
    <p>
        <div id="prefetch_alert_email_recipient">
          {!! Form::label('alert_email_recipient', 'alert_email_recipient') !!}
            {!! Form::input('alert_email_recipient', 'alert_email_recipient', null, ['size' => '50', 'class'=>'typeahead tt-query',  'autocomplete'=>'off', 'spellcheck'=>'false'])  !!}
        </div>
    </p>
    <p>
        {!! Form::label('alert_type', 'alert_type') !!}
        {!! Form::input('alert_type', 'alert_type', null, ['size' => '50'])  !!} e0 or gt0
    </p>
    <p>
        {!! Form::label('consecutive_failures', 'consecutive_failures') !!}
        {!! Form::input('consecutive_failures', 'consecutive_failures', null, ['size'=>'5']) !!}
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
    @if(isset($librato))
        @if($librato != null)
            <a href="/librato/edit/{!! $alert->id !!}">Edit Librato integration</a>
        @else
            <a href="/librato/create/{!! $alert->id !!}">Create Librato integration</a>
        @endif
    @endif

    <meta type="js-module" name="alert-form"/>
@endsection

@section('active_tab_alert_list') active @endsection